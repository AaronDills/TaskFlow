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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('color', 20)->default('blue');
            $table->string('location')->nullable();
            $table->string('reminder')->nullable();
            $table->string('recurrence')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->unsignedBigInteger('parent_event_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_datetime']);
            $table->foreign('parent_event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
