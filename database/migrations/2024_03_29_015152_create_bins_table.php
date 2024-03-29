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
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collector_id')->constrained();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('address');
            $table->string('city');
            $table->boolean('is_active')->default(true);
            // type of waste : street, park, residential, industrial
            $table->enum('waste_type', ['street', 'park', 'residential', 'industrial'])->default('residential');
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            // status : capacity percentage max 100
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bins');
    }
};
