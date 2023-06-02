<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\FeatureForm;
use Botble\RealEstate\Http\Requests\FeatureRequest;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Tables\FeatureTable;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class FeatureController extends BaseController
{
    public function __construct(protected FeatureInterface $featureRepository)
    {
    }

    public function index(FeatureTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/real-estate::feature.name'));

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        return $formBuilder->create(FeatureForm::class)->renderForm();
    }

    public function store(FeatureRequest $request, BaseHttpResponse $response)
    {
        $feature = $this->featureRepository->create($request->all());

        event(new CreatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $response
            ->setPreviousUrl(route('property_feature.index'))
            ->setNextUrl(route('property_feature.edit', $feature->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, Request $request, FormBuilder $formBuilder)
    {
        $feature = $this->featureRepository->findOrFail($id);
        PageTitle::setTitle(trans('plugins/real-estate::feature.edit') . ' "' . $feature->name . '"');

        event(new BeforeEditContentEvent($request, $feature));

        return $formBuilder->create(FeatureForm::class, ['model' => $feature])->renderForm();
    }

    public function update(int|string $id, FeatureRequest $request, BaseHttpResponse $response)
    {
        $feature = $this->featureRepository->findOrFail($id);

        $feature->fill($request->input());
        $this->featureRepository->createOrUpdate($feature);

        event(new UpdatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $response
            ->setPreviousUrl(route('property_feature.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $feature = $this->featureRepository->findOrFail($id);
            $this->featureRepository->delete($feature);

            event(new DeletedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $feature = $this->featureRepository->findOrFail($id);
            $this->featureRepository->delete($feature);

            event(new DeletedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
