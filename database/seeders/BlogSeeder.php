<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Language\Models\LanguageMeta;
use Botble\Slug\Models\Slug;
use Carbon\Carbon;
use Botble\Base\Facades\Html;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Botble\Language\Facades\Language;
use Botble\Media\Facades\RvMedia;
use Botble\Slug\Facades\SlugHelper;

class BlogSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('news');

        Post::query()->where('id', '>', 8)->delete();
        Category::query()->where('id', '>', 8)->delete();
        Tag::query()->where('id', '>', 3)->delete();
        DB::table('posts_translations')->truncate();
        DB::table('categories_translations')->truncate();
        DB::table('tags_translations')->truncate();
        Slug::query()->where('reference_type', Post::class)->where('reference_id', '>', 8)->delete();
        Slug::query()->where('reference_type', Tag::class)->where('reference_id', '>', 3)->delete();
        Slug::query()->where('reference_type', Category::class)->where('reference_id', '>', 8)->delete();
        MetaBoxModel::query()->where('reference_type', Post::class)->where('reference_id', '>', 8)->delete();
        MetaBoxModel::query()->where('reference_type', Tag::class)->where('reference_id', '>', 3)->delete();
        MetaBoxModel::query()->where('reference_type', Category::class)->where('reference_id', '>', 8)->delete();
        LanguageMeta::query()->where('reference_type', Post::class)->where('reference_id', '>', 8)->delete();
        LanguageMeta::query()->where('reference_type', Tag::class)->where('reference_id', '>', 3)->delete();
        LanguageMeta::query()->where('reference_type', Category::class)->where('reference_id', '>', 8)->delete();

        $posts = [
            [
                'name' => 'The Top 2020 Handbag Trends to Know',
            ],
            [
                'name' => 'Top Search Engine Optimization Strategies!',
            ],
            [
                'name' => 'Which Company Would You Choose?',
            ],
            [
                'name' => 'Used Car Dealer Sales Tricks Exposed',
            ],
            [
                'name' => '20 Ways To Sell Your Product Faster',
            ],
            [
                'name' => 'The Secrets Of Rich And Famous Writers',
            ],
            [
                'name' => 'Imagine Losing 20 Pounds In 14 Days!',
            ],
            [
                'name' => 'Are You Still Using That Slow, Old Typewriter?',
            ],
            [
                'name' => 'A Skin Cream That’s Proven To Work',
            ],
            [
                'name' => '10 Reasons To Start Your Own, Profitable Website!',
            ],
            [
                'name' => 'Simple Ways To Reduce Your Unwanted Wrinkles!',
            ],
            [
                'name' => 'Apple iMac with Retina 5K display review',
            ],
            [
                'name' => '10,000 Web Site Visitors In One Month:Guaranteed',
            ],
            [
                'name' => 'Unlock The Secrets Of Selling High Ticket Items',
            ],
            [
                'name' => '4 Expert Tips On How To Choose The Right Men’s Wallet',
            ],
            [
                'name' => 'Sexy Clutches: How to Buy & Wear a Designer Clutch Bag',
            ],
        ];

        $inserted = [];

        $faker = fake();

        foreach ($posts as $index => $item) {
            $item['content'] =
                ($index % 3 == 0 ? Html::tag(
                    'p',
                    '[youtube-video]https://www.youtube.com/watch?v=SlPhMPnQ58k[/youtube-video]'
                ) : '') .
                Html::tag('p', $faker->realText(1000)) .
                Html::tag(
                    'p',
                    Html::image(RvMedia::getImageUrl('news/' . $faker->numberBetween(1, 5) . '.jpg'))
                        ->toHtml(),
                    ['class' => 'text-center']
                ) .
                Html::tag('p', $faker->realText(500)) .
                Html::tag(
                    'p',
                    Html::image(RvMedia::getImageUrl('news/' . $faker->numberBetween(6, 10) . '.jpg'))
                        ->toHtml(),
                    ['class' => 'text-center']
                ) .
                Html::tag('p', $faker->realText(1000)) .
                Html::tag(
                    'p',
                    Html::image(RvMedia::getImageUrl('news/' . $faker->numberBetween(11, 14) . '.jpg'))
                        ->toHtml(),
                    ['class' => 'text-center']
                ) .
                Html::tag('p', $faker->realText(1000));
            $item['author_id'] = User::query()->value('id');
            $item['author_type'] = User::class;
            $item['views'] = $faker->numberBetween(100, 2500);
            $item['is_featured'] = $index < 9;
            $item['image'] = 'news/' . ($index + 1) . '.jpg';
            $item['description'] = $faker->text();
            $item['content'] = str_replace(url(''), '', $item['content']);

            $post = Post::query()->create($item);

            $post->categories()->sync([Arr::random([1, 2, 4, 6])]);

            $post->tags()->sync([1, 2, 3]);

            Slug::query()->create([
                'reference_type' => Post::class,
                'reference_id' => $post->id,
                'key' => Str::slug($post->name),
                'prefix' => SlugHelper::getPrefix(Post::class),
            ]);

            $inserted[] = $post;
        }

        Post::query()->update(['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        $translations = [
            [
                'name' => 'Xu hướng túi xách hàng đầu năm 2020 cần biết',
            ],
            [
                'name' => 'Các Chiến lược Tối ưu hóa Công cụ Tìm kiếm Hàng đầu!',
            ],
            [
                'name' => 'Bạn sẽ chọn công ty nào?',
            ],
            [
                'name' => 'Lộ ra các thủ đoạn bán hàng của đại lý ô tô đã qua sử dụng',
            ],
            [
                'name' => '20 Cách Bán Sản phẩm Nhanh hơn',
            ],
            [
                'name' => 'Bí mật của những nhà văn giàu có và nổi tiếng',
            ],
            [
                'name' => 'Hãy tưởng tượng bạn giảm 20 bảng Anh trong 14 ngày!',
            ],
            [
                'name' => 'Bạn vẫn đang sử dụng máy đánh chữ cũ, chậm đó?',
            ],
            [
                'name' => 'Một loại kem dưỡng da đã được chứng minh hiệu quả',
            ],
            [
                'name' => '10 Lý do Để Bắt đầu Trang web Có Lợi nhuận của Riêng Bạn!',
            ],
            [
                'name' => 'Những cách đơn giản để giảm nếp nhăn không mong muốn của bạn!',
            ],
            [
                'name' => 'Đánh giá Apple iMac với màn hình Retina 5K',
            ],
            [
                'name' => '10.000 Khách truy cập Trang Web Trong Một Tháng: Được Đảm bảo',
            ],
            [
                'name' => 'Mở khóa Bí mật Bán được vé Cao',
            ],
            [
                'name' => '4 Lời khuyên của Chuyên gia về Cách Chọn Ví Nam Phù hợp',
            ],
            [
                'name' => 'Sexy Clutches: Cách Mua & Đeo Túi Clutch Thiết kế',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['posts_id'] = $index + 9;
            $item['description'] = $inserted[$index]->description;
            $item['content'] = $inserted[$index]->content;

            DB::table('posts_translations')->insert($item);
        }

        $posts = LanguageMeta::query()->where('reference_type', Post::class)
            ->where('lang_meta_code', '!=', Language::getDefaultLocaleCode())
            ->get();

        foreach ($posts as $item) {
            $post = Post::query()->find($item->reference_id);

            $originalId = LanguageMeta::query()->where('lang_meta_origin', $item->lang_meta_origin)
                ->where('lang_meta_code', Language::getDefaultLocaleCode())
                ->value('reference_id');

            DB::table('posts_translations')->insert([
                'posts_id' => $originalId,
                'lang_code' => $item->lang_meta_code,
                'name' => $post->name,
                'description' => $post->description,
                'content' => $post->content,
            ]);

            $post->delete();
        }

        LanguageMeta::query()->where('reference_type', Post::class)->delete();
    }
}
