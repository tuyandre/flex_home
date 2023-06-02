<?php

namespace Botble\Language\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Botble\Language\Facades\Language as LanguageFacade;

class LanguageMeta extends BaseModel
{
    protected $primaryKey = 'lang_meta_id';

    protected $table = 'language_meta';

    public $timestamps = false;

    protected $fillable = [
        'lang_meta_code',
        'lang_meta_origin',
        'reference_id',
        'reference_type',
    ];

    public function reference(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    public static function saveMetaData(BaseModel $model, string|null $locale = null, string|null $originValue = null)
    {
        if (! $locale) {
            $locale = LanguageFacade::getDefaultLocaleCode();
        }

        if (! $originValue) {
            $originValue = md5($model->id . get_class($model) . time());
        }

        LanguageMeta::create([
            'reference_id' => $model->id,
            'reference_type' => get_class($model),
            'lang_meta_code' => $locale,
            'lang_meta_origin' => $originValue,
        ]);
    }
}
