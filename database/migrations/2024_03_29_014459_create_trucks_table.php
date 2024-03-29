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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collector_id')->constrained();
            $table->string('latitude');
            $table->string('longitude');
            $table->boolean('is_active')->default(true);
            // working_hours
            $table->time('start_time');
            $table->time('end_time');
            // working_days
            $table->enum('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            // truck_capacity
            $table->integer('truck_capacity');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
