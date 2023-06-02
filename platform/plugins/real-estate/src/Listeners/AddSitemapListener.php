<?php

namespace Botble\RealEstate\Listeners;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Theme\Facades\SiteMapManager;

class AddSitemapListener
{
    public function __construct(
        protected ProjectInterface $projectRepository,
        protected PropertyInterface $propertyRepository,
        protected AccountInterface $accountRepository,
        protected CategoryInterface $categoryRepository,
        protected CityInterface $cityRepository
    ) {
    }

    public function handle(RenderingSiteMapEvent $event): void
    {
        if ($key = $event->key) {
            switch ($key) {
                case 'properties':
                    $propertyLastUpdated = $this->propertyRepository
                        ->getModel()
                        ->where(RealEstateHelper::getPropertyDisplayQueryConditions())
                        ->latest('updated_at')
                        ->value('updated_at');

                    SiteMapManager::add(route('public.properties'), $propertyLastUpdated, '0.4', 'monthly');

                    $items = $this->propertyRepository->advancedGet([
                        'condition' => RealEstateHelper::getPropertyDisplayQueryConditions(),
                        'with' => ['slugable'],
                    ]);

                    foreach ($items as $item) {
                        SiteMapManager::add($item->url, $item->updated_at, '0.8');
                    }

                    break;

                case 'projects':

                    $projectLastUpdated = $this->projectRepository
                        ->getModel()
                        ->where(RealEstateHelper::getProjectDisplayQueryConditions())
                        ->latest('updated_at')
                        ->value('updated_at');

                    SiteMapManager::add(route('public.projects'), $projectLastUpdated, '0.4', 'monthly');

                    $items = $this->projectRepository->advancedGet([
                        'condition' => RealEstateHelper::getProjectDisplayQueryConditions(),
                        'with' => ['slugable'],
                    ]);

                    foreach ($items as $item) {
                        SiteMapManager::add($item->url, $item->updated_at, '0.8');
                    }

                    break;

                case 'agents':

                    $agentLastUpdated = $this->accountRepository
                        ->getModel()
                        ->latest('updated_at')
                        ->value('updated_at');

                    SiteMapManager::add(route('public.agents'), $agentLastUpdated, '0.4', 'monthly');

                    $items = $this->accountRepository
                        ->getModel()
                        ->latest('created_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add($item->url, $item->updated_at, '0.8');
                    }

                    break;

                case 'property-categories':

                    $items = $this->categoryRepository
                        ->getModel()
                        ->with('slugable')
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->latest('created_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add($item->url, $item->updated_at, '0.8');
                    }

                    break;

                case 'properties-city':

                    $items = $this->cityRepository
                        ->getModel()
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->latest('updated_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add(route('public.properties-by-city', $item->slug), $item->updated_at, '0.8');
                    }

                    break;

                case 'projects-city':

                    $items = $this->cityRepository
                        ->getModel()
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->latest('updated_at')
                        ->get();

                    foreach ($items as $item) {
                        SiteMapManager::add(route('public.projects-by-city', $item->slug), $item->updated_at, '0.8');
                    }

                    break;
            }

            return;
        }

        $propertyLastUpdated = $this->propertyRepository
            ->getModel()
            ->where(RealEstateHelper::getPropertyDisplayQueryConditions())
            ->latest('updated_at')
            ->value('updated_at');

        SiteMapManager::addSitemap(SiteMapManager::route('properties'), $propertyLastUpdated);

        $projectLastUpdated = $this->projectRepository
            ->getModel()
            ->where(RealEstateHelper::getProjectDisplayQueryConditions())
            ->latest('updated_at')
            ->value('updated_at');

        SiteMapManager::addSitemap(SiteMapManager::route('projects'), $projectLastUpdated);

        $agentLastUpdated = $this->accountRepository
            ->getModel()
            ->latest('updated_at')
            ->value('updated_at');

        SiteMapManager::addSitemap(SiteMapManager::route('agents'), $agentLastUpdated);

        $cityLastUpdated = $this->cityRepository
            ->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->latest('updated_at')
            ->value('updated_at');

        SiteMapManager::addSitemap(SiteMapManager::route('properties-city'), $cityLastUpdated);
        SiteMapManager::addSitemap(SiteMapManager::route('projects-city'), $cityLastUpdated);
    }
}
