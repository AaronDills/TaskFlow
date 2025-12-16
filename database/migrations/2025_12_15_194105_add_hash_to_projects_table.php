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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('hash', 16)->nullable()->after('id');
        });

        // Generate hashes for existing projects
        \App\Models\Project::whereNull('hash')->get()->each(function ($project) {
            $project->hash = \App\Models\Project::generateUniqueHash();
            $project->save();
        });

        // Now make the hash field required and add constraints
        Schema::table('projects', function (Blueprint $table) {
            $table->string('hash', 16)->nullable(false)->change();
            $table->unique('hash');
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['hash']);
            $table->dropColumn('hash');
        });
    }
};