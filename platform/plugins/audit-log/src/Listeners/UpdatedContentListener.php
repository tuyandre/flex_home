<?php

namespace Botble\AuditLog\Listeners;

use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Exception;
use Botble\AuditLog\Facades\AuditLog;

class UpdatedContentListener
{
    public function handle(UpdatedContentEvent $event): void
    {
        try {
            if ($event->data->getKey()) {
                event(new AuditHandlerEvent(
                    $event->screen,
                    'updated',
                    $event->data->getKey(),
                    AuditLog::getReferenceName($event->screen, $event->data),
                    'primary'
                ));
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
