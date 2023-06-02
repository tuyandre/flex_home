<?php

namespace Botble\Slug\Listeners;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Slug\Services\SlugService;
use Exception;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class CreatedContentListener
{
    public function __construct(protected SlugInterface $slugRepository)
    {
    }

    public function handle(CreatedContentEvent $event): void
    {
        if (SlugHelper::isSupportedModel(get_class($event->data)) && $event->request->input('is_slug_editable', 0)) {
            try {
                $slug = $event->request->input('slug');

                $fieldNameToGenerateSlug = SlugHelper::getColumnNameToGenerateSlug($event->data);

                if (! $slug) {
                    $slug = $event->request->input($fieldNameToGenerateSlug);
                }

                if (! $slug && $event->data->{$fieldNameToGenerateSlug}) {
                    if (! SlugHelper::turnOffAutomaticUrlTranslationIntoLatin()) {
                        $slug = Str::slug($event->data->{$fieldNameToGenerateSlug});
                    } else {
                        $slug = $event->data->{$fieldNameToGenerateSlug};
                    }
                }

                if (! $slug) {
                    $slug = time();
                }

                $slugService = new SlugService($this->slugRepository);

                $this->slugRepository->createOrUpdate([
                    'key' => $slugService->create($slug, (int)$event->data->slug_id, get_class($event->data)),
                    'reference_type' => get_class($event->data),
                    'reference_id' => $event->data->id,
                    'prefix' => SlugHelper::getPrefix(get_class($event->data)),
                ]);
            } catch (Exception $exception) {
                info($exception->getMessage());
            }
        }
    }
}
