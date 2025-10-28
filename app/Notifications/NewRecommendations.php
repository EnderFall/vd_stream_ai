<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRecommendations extends Notification
{
    use Queueable;

    protected $recommendations;

    public function __construct($recommendations)
    {
        $this->recommendations = $recommendations;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'va_recommendation',
            'title' => 'New AI Recommendations Available',
            'message' => 'Your Virtual Assistant has generated new schedule recommendations based on your goals.',
            'recommendation_count' => count($this->recommendations),
            'actions' => [
                [
                    'label' => 'View Recommendations',
                    'url' => route('va.recommendations')
                ]
            ]
        ];
    }
}