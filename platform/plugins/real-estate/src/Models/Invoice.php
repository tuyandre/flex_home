<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Botble\Payment\Models\Payment;
use Botble\RealEstate\Enums\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Invoice extends BaseModel
{
    protected $table = 're_invoices';

    protected $fillable = [
        'account_id',
        'payment_id',
        'reference_type',
        'reference_id',
        'code',
        'sub_total',
        'tax_amount',
        'discount_amount',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'sub_total' => 'float',
        'tax_amount' => 'float',
        'discount_amount' => 'float',
        'amount' => 'float',
        'status' => InvoiceStatusEnum::class,
        'paid_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Invoice $invoice) {
            $code = self::generateInvoiceCode();

            $invoice->code = $code;
        });
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public static function generateInvoiceCode(): string
    {
        $prefix = setting('real_estate_invoice_code_prefix', 'INV-');
        $nextId = static::query()->max('id') + 1;

        do {
            $code = sprintf('%s%d', $prefix, $nextId);
            $nextId++;
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }
}
