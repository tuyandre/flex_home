<?php

namespace Botble\Menu\Listeners;

use Botble\Menu\Repositories\Interfaces\MenuNodeInterface;
use Botble\Slug\Events\UpdatedSlugEvent;
use Exception;
use Botble\Menu\Facades\Menu;

class UpdateMenuNodeUrlListener
{
    public function __construct(protected MenuNodeInterface $menuNodeRepository)
    {
    }

    public function handle(UpdatedSlugEvent $event): void
    {
        try {
            if (in_array(get_class($event->data), Menu::getMenuOptionModels())) {
                $nodes = $this->menuNodeRepository->allBy([
                    'reference_id' => $event->data->id,
                    'reference_type' => get_class($event->data),
                ]);

                foreach ($nodes as $node) {
                    $newUrl = str_replace(url(''), '', $node->reference->url);
                    if ($node->url != $newUrl) {
                        $node->url = $newUrl;
                        $node->save();
                    }
                }
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
