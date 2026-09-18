<?php

declare(strict_types=1);

namespace Rimba\Workflow\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Rimba\Workflow\Models\WorkflowInstance;

final class WorkflowNotification extends Notification
{
    use Queueable;

    public function __construct(public WorkflowInstance $instance, public string $event) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['workflow_instance_id' => $this->instance->getKey(), 'definition_slug' => $this->instance->definition_slug, 'event' => $this->event, 'state' => $this->instance->current_state];
    }
}
