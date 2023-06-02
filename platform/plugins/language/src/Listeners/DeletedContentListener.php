<?php

namespace Botble\Language\Listeners;

use Botble\Base\Events\DeletedContentEvent;
use Exception;
use Botble\Language\Facades\Language;

class DeletedContentListener
{
    public function handle(DeletedContentEvent $event): void
    {
        try {
            Language::deleteLanguage($event->screen, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
