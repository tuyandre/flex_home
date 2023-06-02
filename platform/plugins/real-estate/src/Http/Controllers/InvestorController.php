<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Http\Requests\InvestorRequest;
use Botble\RealEstate\Repositories\Interfaces\InvestorInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\RealEstate\Tables\InvestorTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\InvestorForm;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Facades\PageTitle;

class InvestorController extends BaseController
{
    public function __construct(protected InvestorInterface $investorRepository)
    {
    }

    public function index(InvestorTable $table)
    {
        PageTitle::setTitle(trans('plugins/real-estate::investor.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/real-estate::investor.create'));

        return $formBuilder->create(InvestorForm::class)->renderForm();
    }

    public function store(InvestorRequest $request, BaseHttpResponse $response)
    {
        $investor = $this->investorRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(INVESTOR_MODULE_SCREEN_NAME, $request, $investor));

        return $response
            ->setPreviousUrl(route('investor.index'))
            ->setNextUrl(route('investor.edit', $investor->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder, Request $request)
    {
        $investor = $this->investorRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $investor));

        PageTitle::setTitle(trans('plugins/real-estate::investor.edit') . ' "' . $investor->name . '"');

        return $formBuilder->create(InvestorForm::class, ['model' => $investor])->renderForm();
    }

    public function update(int|string $id, InvestorRequest $request, BaseHttpResponse $response)
    {
        $investor = $this->investorRepository->findOrFail($id);

        $investor->fill($request->input());

        $this->investorRepository->createOrUpdate($investor);

        event(new UpdatedContentEvent(INVESTOR_MODULE_SCREEN_NAME, $request, $investor));

        return $response
            ->setPreviousUrl(route('investor.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $investor = $this->investorRepository->findOrFail($id);

            $this->investorRepository->delete($investor);

            event(new DeletedContentEvent(INVESTOR_MODULE_SCREEN_NAME, $request, $investor));

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
            $investor = $this->investorRepository->findOrFail($id);
            $this->investorRepository->delete($investor);
            event(new DeletedContentEvent(INVESTOR_MODULE_SCREEN_NAME, $request, $investor));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
