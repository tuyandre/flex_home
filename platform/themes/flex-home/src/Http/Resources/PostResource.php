<?php

namespace Theme\FlexHome\Http\Resources;

use Botble\Blog\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Botble\Media\Facades\RvMedia;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'description' => Str::words($this->description, 35),
            'image' => $this->image ? RvMedia::getImageUrl($this->image, 'small', false, RvMedia::getDefaultImage()) : null,
            'created_at' => $this->created_at->translatedFormat('M d, Y'),
            'views' => number_format($this->views),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
