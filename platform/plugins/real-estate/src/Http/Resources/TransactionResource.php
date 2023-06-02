<?php

namespace Botble\RealEstate\Http\Resources;

use Botble\RealEstate\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'description' => $this->getDescription(),
        ];
    }
}
