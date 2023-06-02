<?php

namespace Botble\RssFeed\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Models\Post;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\RssFeed\FeedItem;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Support\Facades\File;
use Mimey\MimeTypes;
use Botble\RssFeed\Facades\RssFeed;
use Botble\Media\Facades\RvMedia;

class RssFeedController extends PublicController
{
    public function getPostFeeds(PostInterface $postRepository)
    {
        if (! is_plugin_active('blog')) {
            abort(404);
        }

        $posts = $postRepository->advancedGet([
            'condition' => ['status' => BaseStatusEnum::PUBLISHED],
            'order_by' => ['created_at' => 'desc'],
            'take' => 20,
        ]);

        $feedItems = collect();

        foreach ($posts as $post) {
            /**
             * @var Post $post
             */
            $imageURL = RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage());

            $feedItem = FeedItem::create()
                ->id($post->id)
                ->title(BaseHelper::clean($post->name))
                ->summary(BaseHelper::clean($post->description))
                ->updated($post->updated_at)
                ->enclosure($imageURL)
                ->enclosureType((new MimeTypes())->getMimeType(File::extension($imageURL)))
                ->enclosureLength(RssFeed::remoteFilesize($imageURL))
                ->category($post->categories()->value('name'))
                ->link((string) $post->url);

            if (method_exists($feedItem, 'author')) {
                $feedItem = $feedItem->author($post->author_id ? $post->author->name : '');
            } else {
                $feedItem = $feedItem
                    ->authorName($post->author_id && ! empty($post->author->name) ? $post->author->name : '')
                    ->authorEmail(! empty($post->author_id) ? $post->author->email : '');
            }

            $feedItems[] = $feedItem;
        }

        return RssFeed::renderFeedItems($feedItems, 'Posts feed', 'Latest posts from ' . theme_option('site_title'));
    }
}
