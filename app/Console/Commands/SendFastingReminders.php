<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Fast;
use App\Notifications\FastingReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendFastingReminders extends Command
{
    protected $signature   = 'koinonia:fasting-reminders';
    protected $description = 'Envoie un rappel aux utilisateurs qui n\'ont pas enregistré leur jeûne du jour';

    public function handle(): void
    {
        $today     = Carbon::today();
        $dayOfWeek = $today->dayOfWeek; // 0=Dimanche, 1=Lundi, ..., 6=Samedi

        // Récupère tous les utilisateurs actifs dont c'est le jour de jeûne
        $users = User::where('notifications_enabled', true)
                     ->whereNotNull('fasting_day')
                     ->where('fasting_day', $dayOfWeek)
                     ->get();

        $this->info("Vérification pour le jour {$dayOfWeek} — {$users->count()} utilisateur(s) concerné(s)");

        $sent = 0;
        foreach ($users as $user) {
            // Vérifie si l'utilisateur a déjà enregistré un jeûne aujourd'hui
            $hasFasted = Fast::where('participant_user_id', $user->id)
                             ->whereDate('fast_date', $today)
                             ->exists();

            if (! $hasFasted) {
                $user->notify(new FastingReminderNotification());
                $sent++;
                $this->line("  → Rappel envoyé à {$user->name} ({$user->email})");
            }
        }

        $this->info("✅ {$sent} rappel(s) envoyé(s).");
    }
}
