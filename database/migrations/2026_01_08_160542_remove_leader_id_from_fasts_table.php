<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fasts', function (Blueprint $table) {
            if (Schema::hasColumn('fasts', 'leader_id')) {
                $table->dropConstrainedForeignId('leader_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fasts', function (Blueprint $table) {
            $table->foreignId('leader_id')->nullable()->constrained('people')->nullOnDelete();
        });
    }
};
