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
        Schema::create('trashes', function (Blueprint $table) {
            $table->id();
            // foreign key to user
            $table->foreignId('user_id')->constrained();
            // foreign key to bin
            $table->foreignId('bin_id')->constrained();
            $table->enum('trash_type', ['plastic', 'paper', 'glass', 'metal', 'organic', 'mixed', 'dangerous', 'other']);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trashes');
    }
};
