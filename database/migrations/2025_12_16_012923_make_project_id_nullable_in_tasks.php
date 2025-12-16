<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * SQLite doesn't support ALTER COLUMN, so we recreate the table.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('PRAGMA foreign_keys=off');

        // Create new table with nullable project_id
        DB::statement('CREATE TABLE tasks_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            project_id INTEGER,
            parent_id INTEGER,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            completed BOOLEAN DEFAULT 0,
            "order" INTEGER DEFAULT 0,
            created_at DATETIME,
            updated_at DATETIME,
            category VARCHAR(255) DEFAULT "recommended",
            scheduled_date DATE,
            scheduled_time VARCHAR(255),
            priority VARCHAR(255) DEFAULT "med",
            due_date DATE,
            is_parent BOOLEAN DEFAULT 0,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES tasks(id) ON DELETE CASCADE
        )');

        // Copy data from old table
        DB::statement('INSERT INTO tasks_new SELECT * FROM tasks');

        // Drop old table
        DB::statement('DROP TABLE tasks');

        // Rename new table
        DB::statement('ALTER TABLE tasks_new RENAME TO tasks');

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys=on');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('PRAGMA foreign_keys=off');

        // Create table with NOT NULL project_id
        DB::statement('CREATE TABLE tasks_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            project_id INTEGER NOT NULL,
            parent_id INTEGER,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            completed BOOLEAN DEFAULT 0,
            "order" INTEGER DEFAULT 0,
            created_at DATETIME,
            updated_at DATETIME,
            category VARCHAR(255) DEFAULT "recommended",
            scheduled_date DATE,
            scheduled_time VARCHAR(255),
            priority VARCHAR(255) DEFAULT "med",
            due_date DATE,
            is_parent BOOLEAN DEFAULT 0,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES tasks(id) ON DELETE CASCADE
        )');

        // Copy data (will fail if there are NULL project_ids)
        DB::statement('INSERT INTO tasks_new SELECT * FROM tasks WHERE project_id IS NOT NULL');

        // Drop old table
        DB::statement('DROP TABLE tasks');

        // Rename new table
        DB::statement('ALTER TABLE tasks_new RENAME TO tasks');

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys=on');
    }
};
