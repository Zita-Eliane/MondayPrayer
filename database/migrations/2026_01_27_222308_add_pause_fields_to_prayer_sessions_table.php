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
            if (! Schema::hasColumn('prayer_sessions', 'prayer_date')) {
                $table->date('prayer_date')->nullable()->after('leader_id');
            }

            if (! Schema::hasColumn('prayer_sessions', 'active_seconds')) {
                $table->unsignedInteger('active_seconds')->default(0)->after('duration_seconds');
            }

            if (! Schema::hasColumn('prayer_sessions', 'paused_at')) {
                $table->timestamp('paused_at')->nullable()->after('started_at');
            }

            if (! Schema::hasColumn('prayer_sessions', 'is_running')) {
                $table->boolean('is_running')->default(true)->after('paused_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prayer_sessions', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('prayer_sessions', 'prayer_date') ? 'prayer_date' : null,
                Schema::hasColumn('prayer_sessions', 'active_seconds') ? 'active_seconds' : null,
                Schema::hasColumn('prayer_sessions', 'paused_at') ? 'paused_at' : null,
                Schema::hasColumn('prayer_sessions', 'is_running') ? 'is_running' : null,
            ]);

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
