<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Facades\Html;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    protected $table = 're_categories';

    protected $fillable = [
        'name',
        'description',
        'status',
        'order',
        'is_default',
        'parent_id',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 're_property_categories')->with('slugable');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 're_project_categories')->with('slugable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id')->withDefault();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getBadgeWithCountAttribute(): string
    {
        $badge = match ($this->status->getValue()) {
            BaseStatusEnum::DRAFT => 'bg-secondary',
            BaseStatusEnum::PENDING => 'bg-warning',
            default => 'bg-success',
        };

        $html = '';

        if ($this->is_default) {
            $html .= Html::tag('span', '<i class="fas fa-award"></i>', [
                'class' => 'badge bg-info me-1',
                'data-bs-toggle' => 'tooltip',
                'title' => trans('plugins/real-estate::category.is_default'),
            ]);
        }

        $html .= Html::tag('span', (string)$this->projects_count, [
            'class' => 'badge font-weight-bold me-1 ' . $badge,
            'data-bs-toggle' => 'tooltip',
            'title' => trans('plugins/real-estate::category.total_projects', ['total' => $this->projects_count]),
        ]);

        $html .= Html::tag('span', (string)$this->properties_count, [
            'class' => 'badge font-weight-bold ' . $badge,
            'data-bs-toggle' => 'tooltip',
            'title' => trans('plugins/real-estate::category.total_properties', ['total' => $this->properties_count]),
        ]);

        return $html;
    }

    protected static function boot(): void
    {
        parent::boot();

        self::deleting(function (Category $category) {
            foreach ($category->children()->get() as $child) {
                $child->parent_id = $category->parent_id;
                $child->save();
            }

            $category->properties()->detach();
            $category->projects()->detach();
        });
    }
}
