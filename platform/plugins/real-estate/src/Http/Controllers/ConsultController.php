<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Http\Requests\ConsultRequest;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\RealEstate\Tables\ConsultTable;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\ConsultForm;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Facades\PageTitle;

class ConsultController extends BaseController
{
    public function __construct(protected ConsultInterface $consultRepository)
    {
    }

    public function index(ConsultTable $table)
    {
        PageTitle::setTitle(trans('plugins/real-estate::consult.name'));

        return $table->renderTable();
    }

    public function edit(int|string $id, FormBuilder $formBuilder, Request $request)
    {
        $consult = $this->consultRepository->findOrFail($id, ['project', 'property']);

        event(new BeforeEditContentEvent($request, $consult));

        PageTitle::setTitle(trans('plugins/real-estate::consult.edit') . ' "' . $consult->name . '"');

        return $formBuilder->create(ConsultForm::class, ['model' => $consult])->renderForm();
    }

    public function update(int|string $id, ConsultRequest $request, BaseHttpResponse $response)
    {
        $consult = $this->consultRepository->findOrFail($id);

        $consult->fill($request->input());

        $this->consultRepository->createOrUpdate($consult);

        event(new UpdatedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));

        return $response
            ->setPreviousUrl(route('consult.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $consult = $this->consultRepository->findOrFail($id);

            $this->consultRepository->delete($consult);

            event(new DeletedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));

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
            $consult = $this->consultRepository->findOrFail($id);
            $this->consultRepository->delete($consult);
            event(new DeletedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
