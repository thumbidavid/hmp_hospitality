<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected array $payload)
    {
        // [title, message, url, type, priority, grouping_key]
    }

    public function via($notifiable): array
    {
        \Illuminate\Support\Facades\Log::info(
            '[DEBUG] Notification via() called for user: ' . $notifiable->id,
        );
        return ['database', 'broadcast'];
    }

    /**
     * Store in the 'notifications' table.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => $this->payload['title'],
            'message' => $this->payload['message'],
            'url' => $this->payload['url'],
            'type' => $this->payload['type'],
            'priority' => $this->payload['priority'] ?? 'medium',
            'grouping_key' => $this->payload['grouping_key'] ?? null,
            'context' => $this->payload['context'] ?? [],
        ];
    }

    /**
     * Broadcast over WebSockets (Reverb).
     */
    // public function toBroadcast($notifiable): BroadcastMessage
    // {
    //     return new BroadcastMessage([
    //         'id' => $this->id, // Laravel's internal Notification UUID
    //         'data' => $this->toArray($notifiable),
    //         'created_at' => now()->toDateTimeString(),
    //     ]);
    // }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
