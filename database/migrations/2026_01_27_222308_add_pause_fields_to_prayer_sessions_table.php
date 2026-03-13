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
        Schema::table('prayer_sessions', function (Blueprint $table) {
            //
            $table->date('prayer_date')->nullable()->after('leader_id');

            $table->unsignedInteger('active_seconds')->default(0)->after('duration_seconds');
            $table->timestamp('paused_at')->nullable()->after('started_at');
            $table->boolean('is_running')->default(true)->after('paused_at');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prayer_sessions', function (Blueprint $table) {
            $table->dropColumn(['prayer_date', 'active_seconds', 'paused_at', 'is_running']);
        });
    }
};
