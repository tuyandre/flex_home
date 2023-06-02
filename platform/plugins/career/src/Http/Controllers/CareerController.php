<?php

namespace Botble\Career\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Career\Http\Requests\CareerRequest;
use Botble\Career\Repositories\Interfaces\CareerInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Career\Tables\CareerTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Career\Forms\CareerForm;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Facades\PageTitle;

class CareerController extends BaseController
{
    public function __construct(protected CareerInterface $careerRepository)
    {
    }

    public function index(CareerTable $table)
    {
        PageTitle::setTitle(trans('plugins/career::career.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/career::career.create'));

        return $formBuilder->create(CareerForm::class)->renderForm();
    }

    public function store(CareerRequest $request, BaseHttpResponse $response)
    {
        $career = $this->careerRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(CAREER_MODULE_SCREEN_NAME, $request, $career));

        return $response
            ->setPreviousUrl(route('career.index'))
            ->setNextUrl(route('career.edit', $career->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder, Request $request)
    {
        $career = $this->careerRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $career));

        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $career->name]));

        return $formBuilder->create(CareerForm::class, ['model' => $career])->renderForm();
    }

    public function update(int|string $id, CareerRequest $request, BaseHttpResponse $response)
    {
        $career = $this->careerRepository->findOrFail($id);

        $career->fill($request->input());

        $this->careerRepository->createOrUpdate($career);

        event(new UpdatedContentEvent(CAREER_MODULE_SCREEN_NAME, $request, $career));

        return $response
            ->setPreviousUrl(route('career.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $career = $this->careerRepository->findOrFail($id);

            $this->careerRepository->delete($career);

            event(new DeletedContentEvent(CAREER_MODULE_SCREEN_NAME, $request, $career));

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
            $career = $this->careerRepository->findOrFail($id);
            $this->careerRepository->delete($career);
            event(new DeletedContentEvent(CAREER_MODULE_SCREEN_NAME, $request, $career));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
