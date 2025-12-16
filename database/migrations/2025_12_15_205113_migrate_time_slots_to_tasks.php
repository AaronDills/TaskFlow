<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate time slots to tasks
        $timeSlots = DB::table('time_slots')->get();
        
        foreach ($timeSlots as $timeSlot) {
            if (!empty(trim($timeSlot->content))) {
                DB::table('tasks')->insert([
                    'project_id' => $timeSlot->project_id,
                    'title' => $timeSlot->content,
                    'category' => 'scheduled', // Mark these as scheduled tasks
                    'description' => null,
                    'completed' => false,
                    'order' => 0,
                    'scheduled_date' => $timeSlot->date,
                    'scheduled_time' => $timeSlot->time,
                    'created_at' => $timeSlot->created_at,
                    'updated_at' => $timeSlot->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove migrated time slot tasks
        DB::table('tasks')
            ->whereNotNull('scheduled_date')
            ->whereNotNull('scheduled_time')
            ->where('category', 'scheduled')
            ->delete();
    }
};