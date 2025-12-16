<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('project_id')->constrained('tasks')->onDelete('cascade');
            $table->enum('priority', ['high', 'med', 'low'])->default('med')->after('category');
            $table->date('due_date')->nullable()->after('priority');
            $table->boolean('is_parent')->default(false)->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'priority', 'due_date', 'is_parent']);
        });
    }
};
