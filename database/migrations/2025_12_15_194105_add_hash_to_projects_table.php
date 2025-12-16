<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Generate a unique hash for projects.
     */
    private function generateUniqueHash(): string
    {
        do {
            $hash = Str::random(16);
        } while (DB::table('projects')->where('hash', $hash)->exists());

        return $hash;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('hash', 16)->nullable()->after('id');
        });

        // Generate hashes for existing projects using DB facade
        $projects = DB::table('projects')->whereNull('hash')->get();
        foreach ($projects as $project) {
            DB::table('projects')
                ->where('id', $project->id)
                ->update(['hash' => $this->generateUniqueHash()]);
        }

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
            $table->dropUnique(['hash']);
            $table->dropColumn('hash');
        });
    }
};
