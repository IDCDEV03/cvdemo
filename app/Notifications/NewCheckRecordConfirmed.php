<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCheckRecordConfirmed extends Notification
{
    use Queueable;

    public function __construct(public array $payload) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'      => 'มีการตรวจรถใหม่',
            'message'    => $this->payload['message'] ?? null,
            'chk_id'     => $this->payload['chk_id'],      // chk_records.id
            'record_id'  => $this->payload['record_id'],   // Str::random
            'veh_id'     => $this->payload['veh_id'],
            'form_id'    => $this->payload['form_id'],
            'agency_id'  => $this->payload['agency_id'],
            'user_id'    => $this->payload['user_id'],
            'url'        => $this->payload['url'],
            'created_at' => $this->payload['created_at'],
        ];
    }
}
