<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display the calendar page.
     */
    public function index(Request $request)
    {
        $projects = Auth::user()->projects()->latest()->get();

        return view('calendar', compact('projects'));
    }

    /**
     * Get events for a date range (API).
     */
    public function getEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $events = Event::forUser(Auth::id())
            ->inDateRange($request->start, $request->end)
            ->orderBy('start_datetime')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start' => $event->start_datetime->toIso8601String(),
                    'end' => $event->end_datetime->toIso8601String(),
                    'color' => $event->color,
                    'recurrence' => $event->recurrence,
                    'colorClasses' => $event->getColorClasses(),
                ];
            });

        return response()->json($events);
    }

    /**
     * Get events for a specific date (for daily view integration).
     */
    public function getEventsForDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);

        $events = Event::forUser(Auth::id())
            ->onDate($date)
            ->orderBy('start_datetime')
            ->get()
            ->map(function ($event) use ($date) {
                $dayStart = $date->copy()->setTime(8, 0);
                $dayEnd = $date->copy()->setTime(18, 0);

                $eventStart = $event->start_datetime->lt($dayStart)
                    ? $dayStart
                    : $event->start_datetime;
                $eventEnd = $event->end_datetime->gt($dayEnd)
                    ? $dayEnd
                    : $event->end_datetime;

                // Calculate slot index (0-39 for 8am-6pm in 15-min increments)
                $startSlot = max(0, $dayStart->diffInMinutes($eventStart) / 15);
                $endSlot = min(40, $dayStart->diffInMinutes($eventEnd) / 15);
                $slotCount = $endSlot - $startSlot;

                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start' => $event->start_datetime->toIso8601String(),
                    'end' => $event->end_datetime->toIso8601String(),
                    'startTime' => $event->start_datetime->format('g:i A'),
                    'endTime' => $event->end_datetime->format('g:i A'),
                    'color' => $event->color,
                    'colorClasses' => $event->getColorClasses(),
                    'startSlot' => (int) $startSlot,
                    'slotCount' => (int) $slotCount,
                    'continuesFromPrevDay' => $event->start_datetime->lt($date->copy()->startOfDay()),
                    'continuesToNextDay' => $event->end_datetime->gt($date->copy()->endOfDay()),
                ];
            });

        return response()->json($events);
    }

    /**
     * Store a new event.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'color' => 'in:blue,green,red,purple,yellow,pink,indigo,gray',
            'recurrence' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_end_date' => 'nullable|date|after:start_datetime',
        ]);

        $event = Event::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'color' => $request->color ?? 'blue',
            'recurrence' => $request->recurrence,
            'recurrence_end_date' => $request->recurrence_end_date,
        ]);

        // If recurrence is set, generate recurring instances
        if ($request->recurrence && $request->recurrence_end_date) {
            $this->generateRecurringEvents($event);
        }

        return response()->json($event, 201);
    }

    /**
     * Update an event.
     */
    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'sometimes|required|date',
            'end_datetime' => 'sometimes|required|date|after:start_datetime',
            'color' => 'sometimes|in:blue,green,red,purple,yellow,pink,indigo,gray',
            'recurrence' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_end_date' => 'nullable|date',
        ]);

        $event->update($request->only([
            'title', 'description', 'start_datetime', 'end_datetime',
            'color', 'recurrence', 'recurrence_end_date'
        ]));

        return response()->json($event);
    }

    /**
     * Delete an event.
     */
    public function destroy(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $deleteSeries = $request->boolean('delete_series', false);

        if ($deleteSeries && $event->parent_event_id) {
            // Delete parent and all instances
            Event::where('id', $event->parent_event_id)
                ->orWhere('parent_event_id', $event->parent_event_id)
                ->delete();
        } elseif ($deleteSeries && $event->recurringInstances()->exists()) {
            // This is a parent, delete all instances too
            $event->recurringInstances()->delete();
            $event->delete();
        } else {
            $event->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Generate recurring event instances.
     */
    protected function generateRecurringEvents(Event $parentEvent)
    {
        $endDate = Carbon::parse($parentEvent->recurrence_end_date);
        $currentStart = Carbon::parse($parentEvent->start_datetime);
        $currentEnd = Carbon::parse($parentEvent->end_datetime);
        $duration = $currentStart->diffInMinutes($currentEnd);

        $increment = match($parentEvent->recurrence) {
            'daily' => fn($d) => $d->addDay(),
            'weekly' => fn($d) => $d->addWeek(),
            'monthly' => fn($d) => $d->addMonth(),
            'yearly' => fn($d) => $d->addYear(),
            default => null,
        };

        if (!$increment) return;

        $maxInstances = 365;
        $count = 0;

        while ($count < $maxInstances) {
            $currentStart = $increment($currentStart);
            $currentEnd = $currentStart->copy()->addMinutes($duration);

            if ($currentStart->gt($endDate)) break;

            Event::create([
                'user_id' => $parentEvent->user_id,
                'title' => $parentEvent->title,
                'description' => $parentEvent->description,
                'start_datetime' => $currentStart,
                'end_datetime' => $currentEnd,
                'color' => $parentEvent->color,
                'parent_event_id' => $parentEvent->id,
            ]);

            $count++;
        }
    }
}
