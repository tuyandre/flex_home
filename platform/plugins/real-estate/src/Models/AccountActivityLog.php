<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseModel;

class AccountActivityLog extends BaseModel
{
    protected $table = 're_account_activity_logs';

    protected $fillable = [
        'action',
        'user_agent',
        'reference_url',
        'reference_name',
        'ip_address',
        'account_id',
    ];

    protected $casts = [
        'action' => SafeContent::class,
    ];

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $model->user_agent = $model->user_agent ?: request()->userAgent();
            $model->ip_address = $model->ip_address ?: request()->ip();
            $model->account_id = $model->account_id ?: auth('account')->id();
            $model->reference_url = str_replace(url('/'), '', $model->reference_url);
        });
    }

    public function getDescription(): string
    {
        $name = $this->reference_name;
        if ($this->reference_name && $this->reference_url) {
            $name = Html::link($this->reference_url, $this->reference_name, ['style' => 'color: #1d9977']);
        }

        $time = Html::tag('span', $this->created_at->diffForHumans(), ['class' => 'small italic']);

        return trans('plugins/real-estate::dashboard.actions.' . $this->action, ['name' => $name]) . ' . ' . $time;
    }
}
