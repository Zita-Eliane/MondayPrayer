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
        //
        Schema::table('fasts', function (Blueprint $table) {
        // si tu avais beneficiary_name, prayed, etc. tu peux les supprimer
            if (Schema::hasColumn('fasts', 'beneficiary_name')) $table->dropColumn('beneficiary_name');
            if (Schema::hasColumn('fasts', 'prayed')) $table->dropColumn('prayed');

            $table->date('fast_date')->change();

            $table->foreignId('participant_user_id')->after('id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('leader_id')->nullable()->after('participant_user_id')->constrained('people')->nullOnDelete();

            $table->string('fast_type')->default('partial'); // partial, total, etc.
            $table->unsignedInteger('prayer_minutes')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
