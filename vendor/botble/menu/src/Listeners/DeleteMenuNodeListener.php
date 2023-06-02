<?php

namespace Botble\Menu\Listeners;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Menu\Repositories\Interfaces\MenuNodeInterface;
use Exception;
use Botble\Menu\Facades\Menu;

class DeleteMenuNodeListener
{
    public function __construct(protected MenuNodeInterface $menuNodeRepository)
    {
    }

    public function handle(DeletedContentEvent $event): void
    {
        if (in_array(get_class($event->data), Menu::getMenuOptionModels())) {
            try {
                $this->menuNodeRepository->deleteBy([
                    'reference_id' => $event->data->id,
                    'reference_type' => get_class($event->data),
                ]);
            } catch (Exception $exception) {
                info($exception->getMessage());
            }
        }
    }
}
