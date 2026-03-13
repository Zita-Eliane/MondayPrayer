<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FastingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Ajoute mail si l'utilisateur a un email vérifié
        if ($notifiable->email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✨ Rappel de jeûne — Koinonia')
            ->greeting("Bonjour {$notifiable->name} 👋")
            ->line("Nous avons remarqué que tu n'as pas encore enregistré ton jeûne d'aujourd'hui.")
            ->line("Si tu as jeûné, prends un moment pour l'enregistrer dans l'application.")
            ->action('Enregistrer mon jeûne', url('/fasts/create'))
            ->line("Que la grâce de Dieu t'accompagne dans ta démarche spirituelle.")
            ->salutation('L\'équipe Koinonia 🙏');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'fasting_reminder',
            'title'   => 'Rappel de jeûne',
            'message' => "Tu n'as pas encore enregistré ton jeûne d'aujourd'hui.",
            'url'     => '/fasts/create',
            'icon'    => '🕊️',
        ];
    }
}
