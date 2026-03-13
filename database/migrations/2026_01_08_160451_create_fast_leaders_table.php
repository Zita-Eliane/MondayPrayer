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
        Schema::create('fast_leaders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fast_id')->constrained('fasts')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
        //    $table->foreignId('leader_id')->constrained('people')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['fast_id', 'person_id']); // évite doublons
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fast_leaders');
    }
};
