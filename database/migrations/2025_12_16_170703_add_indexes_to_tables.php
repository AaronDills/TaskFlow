<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if an index exists (MySQL compatible)
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $database = config('database.connections.mysql.database');
        $indexes = DB::select("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND INDEX_NAME = ?
        ", [$database, $table, $indexName]);

        return count($indexes) > 0;
    }

    /**
     * Safely add an index if it doesn't exist
     */
    private function addIndexIfNotExists(string $table, string|array $columns, ?string $indexName = null): void
    {
        $cols = is_array($columns) ? $columns : [$columns];
        $name = $indexName ?? $table . '_' . implode('_', $cols) . '_index';

        if (!$this->indexExists($table, $name)) {
            Schema::table($table, function (Blueprint $table) use ($cols, $name) {
                $table->index($cols, $name);
            });
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to projects table
        $this->addIndexIfNotExists('projects', 'user_id');
        $this->addIndexIfNotExists('projects', 'label_id');
        $this->addIndexIfNotExists('projects', 'status');

        // Add indexes to tasks table
        $this->addIndexIfNotExists('tasks', 'project_id');
        $this->addIndexIfNotExists('tasks', 'parent_id');
        $this->addIndexIfNotExists('tasks', 'user_id');
        $this->addIndexIfNotExists('tasks', 'completed');
        $this->addIndexIfNotExists('tasks', 'scheduled_date');
        $this->addIndexIfNotExists('tasks', ['user_id', 'category']);
        $this->addIndexIfNotExists('tasks', ['user_id', 'completed', 'completed_at']);

        // Add indexes to events table
        $this->addIndexIfNotExists('events', 'user_id');
        $this->addIndexIfNotExists('events', 'start_datetime');
        $this->addIndexIfNotExists('events', 'end_datetime');

        // Add indexes to labels table
        $this->addIndexIfNotExists('labels', 'user_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            'projects' => ['projects_user_id_index', 'projects_label_id_index', 'projects_status_index'],
            'tasks' => ['tasks_project_id_index', 'tasks_parent_id_index', 'tasks_user_id_index',
                       'tasks_completed_index', 'tasks_scheduled_date_index',
                       'tasks_user_id_category_index', 'tasks_user_id_completed_completed_at_index'],
            'events' => ['events_user_id_index', 'events_start_datetime_index', 'events_end_datetime_index'],
            'labels' => ['labels_user_id_index'],
        ];

        foreach ($indexes as $table => $indexNames) {
            foreach ($indexNames as $indexName) {
                if ($this->indexExists($table, $indexName)) {
                    Schema::table($table, function (Blueprint $t) use ($indexName) {
                        $t->dropIndex($indexName);
                    });
                }
            }
        }
    }
};
