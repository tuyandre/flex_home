<?php

namespace Botble\RealEstate\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Enums\TransactionTypeEnum;
use Botble\RealEstate\Http\Requests\CreateTransactionRequest;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\TransactionInterface;
use Botble\RealEstate\Facades\RealEstateHelper;

class TransactionController extends BaseController
{
    public function __construct(
        protected TransactionInterface $transactionRepository,
        protected AccountInterface $accountRepository
    ) {
    }

    public function postCreate(int|string $id, CreateTransactionRequest $request, BaseHttpResponse $response)
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $account = $this->accountRepository->findOrFail($id);

        $request->merge([
            'user_id' => Auth::user()->getKey(),
            'account_id' => $id,
        ]);

        $this->transactionRepository->createOrUpdate($request->input());

        if ($request->input('type') == TransactionTypeEnum::ADD) {
            $account->credits += $request->input('credits');
        } elseif ($request->input('type') == TransactionTypeEnum::REMOVE) {
            $credits = $account->credits - $request->input('credits');
            $account->credits = max($credits, 0);
        }

        $this->accountRepository->createOrUpdate($account);

        return $response
            ->setMessage(trans('core/base::notices.create_success_message'));
    }
}
