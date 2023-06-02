<?php

namespace Botble\Career\Listeners;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Career\Repositories\Interfaces\CareerInterface;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Theme\Facades\SiteMapManager;

class AddSitemapListener
{
    public function __construct(protected CareerInterface $careerRepository)
    {
    }

    public function handle(RenderingSiteMapEvent $event): void
    {
        if ($event->key == 'careers') {
            $careerLastUpdated = $this->careerRepository
                ->getModel()
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->latest('updated_at')
                ->value('updated_at');

            SiteMapManager::add(route('public.careers'), $careerLastUpdated, '0.4', 'monthly');

            $careers = $this->careerRepository->allBy(['status' => BaseStatusEnum::PUBLISHED], ['slugable']);

            foreach ($careers as $career) {
                SiteMapManager::add($career->url, $career->updated_at, '0.6');
            }
        }

        $careerLastUpdated = $this->careerRepository
            ->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->latest('updated_at')
            ->value('updated_at');

        SiteMapManager::addSitemap(SiteMapManager::route('careers'), $careerLastUpdated);
    }
}
