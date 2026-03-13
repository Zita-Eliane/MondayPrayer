<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 0=Dimanche, 1=Lundi, 2=Mardi, ..., 6=Samedi
            $table->tinyInteger('fasting_day')->nullable()->default(1)
                  ->comment('Jour de jeûne hebdomadaire');

            $table->time('fasting_reminder_time')->default('20:30:00')
                  ->comment('Heure du rappel si jeûne non enregistré');

            $table->boolean('notifications_enabled')->default(true);

            $table->string('fcm_token', 500)->nullable()
                  ->comment('Token Firebase pour push notifications mobiles');

            $table->string('role')->default('member')
                  ->comment('admin ou member');

            $table->string('phone', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fasting_day',
                'fasting_reminder_time',
                'notifications_enabled',
                'fcm_token',
                'role',
                'phone',
            ]);
        });
    }
};
