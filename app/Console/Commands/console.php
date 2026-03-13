<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─────────────────────────────────────────────────────────────────
// KOINONIA — Tâches planifiées
// ─────────────────────────────────────────────────────────────────

// Envoie un rappel de jeûne chaque jour à 20h30
// La commande vérifie elle-même quel est le jour de jeûne de chaque user
Schedule::command('koinonia:fasting-reminders')
    ->dailyAt('20:30')
    ->withoutOverlapping()
    ->runInBackground();
