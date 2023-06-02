<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\Page\Models\Page;
use Botble\Slug\Models\Slug;
use Botble\Base\Facades\Html;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class PageSeeder extends BaseSeeder
{
    public function run(): void
    {
        $pages = [
            [
                'name' => 'Home',
                'content' =>
                    Html::tag('div', '[search-box title="Find your favorite homes at Flex Home" background_image="general/home-banner.jpg" enable_search_projects_on_homepage_search="yes" default_home_search_type="project"][/search-box]') .
                    Html::tag('div', '[featured-projects title="Featured projects" subtitle="We make the best choices with the hottest and most prestigious projects, please visit the details below to find out more." limit="4"][/featured-projects]') .
                    Html::tag('div', '[properties-by-locations title="Properties by locations" subtitle="Each place is a good choice, it will help you make the right decision, do not miss the opportunity to discover our wonderful properties." limit="10"][/properties-by-locations]') .
                    Html::tag('div', '[properties-for-sale title="Properties For Sale" subtitle="Below is a list of properties that are currently up for sale" limit="8"][/properties-for-sale]') .
                    Html::tag('div', '[properties-for-rent title="Properties For Rent" subtitle="Below is a detailed price list of each property for rent" limit="8"][/properties-for-rent]') .
                    Html::tag('div', '[featured-agents title="Featured Agents"][/featured-agents]') .
                    Html::tag(
                        'div',
                        '[recently-viewed-properties title="Recently Viewed Properties" subtitle="Your currently viewed properties." limit="8"][/recently-viewed-properties]'
                    ) .
                    Html::tag('div', '[latest-news title="News" subtitle="Below is the latest real estate news we get regularly updated from reliable sources." limit="4"][/latest-news]')
                ,
                'template' => 'homepage',
            ],
            [
                'name' => 'News',
                'content' => '---',
                'template' => 'default',
            ],
            [
                'name' => 'About us',
                'description' => 'Founded on August 28, 1993 (formerly known as Truong Thinh Phat Construction Co., Ltd.), Flex Home operates in the field of real estate business, building villas for rent.
With the slogan "Breaking time, through space" with a sustainable development strategy, taking Real Estate as a focus area, Flex Home is constantly connecting between buyers and sellers in the field.',
                'content' => '<h4><span style="font-size:18px;"><b>1. COMPANY</b><span style="font-family:Arial,Helvetica,sans-serif;"><strong> PROFILE</strong></span></span></h4>

<p><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Founded on August 28, 1993 (formerly known as Truong Thinh Phat Construction Co., Ltd.), Flex Home operates in the field of real estate business, building villas for rent.<br />
With the slogan &quot;Breaking time, through space&quot; with a sustainable development strategy, taking Real Estate as a focus area, Flex Home is constantly connecting between buyers and sellers in the field. Real estate, bringing people closer together, over the distance of time and space, is a reliable place for real estate investment - an area that is constantly evolving over time.</span></span></p>

<blockquote>
<h2 style="font-style: italic; text-align: center;"><span style="font-size:24px;"><strong><span style="font-family:Arial,Helvetica,sans-serif;"><span style="color:#16a085;">&quot;Breaking time, through space&quot;</span></span></strong></span></h2>
</blockquote>

<h4 style="text-align: center;"><img alt="" src="' . url('') . '/storage/general/asset-3-at-3x.png" style="width: 90%;" /></h4>

<h4><span style="font-size:18px;"><b><font face="Arial, Helvetica, sans-serif">2. VISION&nbsp;</font></b></span></h4>

<p><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">- Acquiring domestic areas.<br />
- Reaching far across continents.</span></span></p>

<h4><span style="font-size:18px;"><b>3. MISSION</b></span></h4>

<p><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">- Creating the community<br />
- Building destinations<br />
- Nurture happiness</span></span></p>

<p><img alt="" src="' . url('') . '/storage/general/vietnam-office-4.jpg" /></p>
',
                'template' => 'default',
            ],
            [
                'name' => 'Contact',
                'content' => '<p>[contact-form][/contact-form]<br />
&nbsp;</p>

<h3>Directions</h3>

<p>[google-map]North Link Building, 10 Admiralty Street, 757695 Singapore[/google-map]</p>

<p>&nbsp;</p>',
                'template' => 'default',
            ],
            [
                'name' => 'Terms & Conditions',
                'description' => 'Copyrights and other intellectual property rights to all text, images, audio, software and other content on this site are owned by Flex Home and its affiliates. Users are allowed to view the contents of the website, cite the contents by printing, downloading the hard disk and distributing it to others for non-commercial purposes.',
                'content' => '<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Access to and use of the Flex Home website is subject to the following terms, conditions, and relevant laws of Vietnam.</span></span></p>

<h4 style="text-align: justify;"><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>1. Copyright</strong></span></span></h4>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Copyrights and other intellectual property rights to all text, images, audio, software and other content on this site are owned by Flex Home and its affiliates. Users are allowed to view the contents of the website, cite the contents by printing, downloading the hard disk and distributing it to others for non-commercial purposes, providing information or personal purposes. </span></span><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Any content from this site may not be used for sale or distribution for profit, nor may it be edited or included in any other publication or website.</span></span></p>

<h4 style="text-align: justify;"><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>2. Content</strong></span></span></h4>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">The information on this website is compiled with great confidence but for general information research purposes only. While we endeavor to maintain updated and accurate information, we make no representations or warranties in any manner regarding completeness, accuracy, reliability, appropriateness or availability in relation to web site, or related information, product, service, or image within the website for any purpose. </span></span></p>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Flex Home and its employees, managers, and agents are not responsible for any loss, damage or expense incurred as a result of accessing and using this website and the sites. </span></span><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">The web is connected to it, including but not limited to, loss of profits, direct or indirect losses. We are also not responsible, or jointly responsible, if the site is temporarily inaccessible due to technical issues beyond our control. Any comments, suggestions, images, ideas and other information or materials that users submit to us through this site will become our exclusive property, including the right to may arise in the future associated with us.</span></span></p>

<p style="text-align: center;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;"><img alt="" src="' . url('') . '/storage/general/copyright.jpg" style="width: 90%;" /></span></span></p>

<h4 style="text-align: justify;"><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>3. Note on&nbsp;connected sites</strong></span></span></h4>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">At many points in the website, users can get links to other websites related to a specific aspect. This does not mean that we are related to the websites or companies that own these websites. Although we intend to connect users to sites of interest, we are not responsible or jointly responsible for our employees, managers, or representatives. with other websites and information contained therein.</span></span></p>
',
                'template' => 'default',
            ],
            [
                'name' => 'Cookie Policy',
                'content' => Html::tag('h3', 'EU Cookie Consent') .
                    Html::tag(
                        'p',
                        'To use this website we are using Cookies and collecting some Data. To be compliant with the EU GDPR we give you to choose if you allow us to use certain Cookies and to collect some Data.'
                    ) .
                    Html::tag('h4', 'Essential Data') .
                    Html::tag(
                        'p',
                        'The Essential Data is needed to run the Site you are visiting technically. You can not deactivate them.'
                    ) .
                    Html::tag(
                        'p',
                        '- Session Cookie: PHP uses a Cookie to identify user sessions. Without this Cookie the Website is not working.'
                    ) .
                    Html::tag(
                        'p',
                        '- XSRF-Token Cookie: Laravel automatically generates a CSRF "token" for each active user session managed by the application. This token is used to verify that the authenticated user is the one actually making the requests to the application.'
                    ),
                'template' => 'default',
            ],
            [
                'name' => 'Properties',
                'content' =>
                    Html::tag('div', '[properties-list title="Discover our properties" description="Discover our properties" description="Each place is a good choice, it will help you make the right decision, do not miss the opportunity to discover our wonderful properties." number_of_properties_per_page="12"][/properties-list]')
                ,
                'template' => 'homepage',
            ],
            [
                'name' => 'Projects',
                'content' =>
                    Html::tag('div', '[projects-list  title="Discover our projects" description="We make the best choices with the hottest and most prestigious projects, please visit the details below to find out more" number_of_projects_per_page="12"][/projects-list]')
                ,
                'template' => 'homepage',
            ],
        ];

        Page::query()->truncate();
        DB::table('pages_translations')->truncate();
        Slug::query()->where('reference_type', Page::class)->delete();
        MetaBoxModel::query()->where('reference_type', Page::class)->delete();
        LanguageMeta::query()->where('reference_type', Page::class)->delete();

        foreach ($pages as $item) {
            $item['user_id'] = 1;
            $page = Page::query()->create($item);

            Slug::query()->create([
                'reference_type' => Page::class,
                'reference_id' => $page->id,
                'key' => Str::slug($page->name),
                'prefix' => SlugHelper::getPrefix(Page::class),
            ]);
        }

        $translations = [
            [
                'name' => 'Trang chủ',
                'content' =>
                    Html::tag('div', '[search-box title="Tìm kiếm ngôi nhà mơ ước của bạn tại Flex Home" background_image="general/home-banner.jpg" enable_search_projects_on_homepage_search="yes" default_home_search_type="project"][/search-box]') .
                    Html::tag('div', '[featured-projects title="Dự án nổi bật" subtitle="Chúng tôi đưa ra những lựa chọn tốt nhất với những dự án hot nhất và uy tín nhất, vui lòng truy cập chi tiết bên dưới để tìm hiểu thêm." limit="4"][/featured-projects]') .
                    Html::tag('div', '[properties-by-locations title="Bất động sản theo khu vực" subtitle="Mỗi nơi là một sự lựa chọn tốt sẽ giúp bạn đưa ra quyết định đúng đắn, đừng bỏ lỡ cơ hội khám phá những bất động sản tuyệt vời của chúng tôi." limit="10"][/properties-by-locations]') .
                    Html::tag('div', '[properties-for-sale title="Bất động sản bán" subtitle="Dưới đây là danh sách các bất động sản hiện đang được bán." limit="8"][/properties-for-sale]') .
                    Html::tag('div', '[properties-for-rent title="Bất động sản ở cho thuê" subtitle="Dưới đây là danh sách các bất động sản hiện đang được cho thuê." limit="8"][/properties-for-rent]') .
                    Html::tag('div', '[featured-agents title="Đại lý nổi bật"][/featured-agents] limit="8"') .
                    Html::tag('div', '[recently-viewed-properties title="Nhà/căn hộ đã xem" description="Các căn hộ/nhà mà bạn đã xem." limit="8"][/recently-viewed-properties]') .
                    Html::tag('div', '[latest-news title="Tin tức" subtitle="Dưới đây là tin tức bất động sản mới nhất được chúng tôi cập nhật thường xuyên từ các nguồn đáng tin cậy." limit="4"][/latest-news]')
                ,
            ],
            [
                'name' => 'Tin tức',
                'content' => '---',
            ],
            [
                'name' => 'Về chúng tôi',
                'description' => 'Được thành lập ngày 28 - 08 -1993 (tiền thân là công ty TNHH Xây Dựng Trường Thịnh Phát), Flex Home hoạt động trong lĩnh vực kinh doanh bất động sản, xây biệt thự cho thuê. Với khẩu hiệu  “Đánh bật thời gian, xuyên qua không gian” cùng chiến lược phát triển bền vững, Flex Home không ngừng kết nối giữa người cần mua và người cần bán trong lĩnh vực bất động sản',
                'content' => '<h4><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>1. SƠ LƯỢC VỀ C&Ocirc;NG TY</strong></span></span></h4>

<p><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Được th&agrave;nh lập ng&agrave;y 28 - 08 -1993 (tiền th&acirc;n l&agrave; c&ocirc;ng ty TNHH X&acirc;y Dựng Trường Thịnh Ph&aacute;t), Flex Home hoạt động trong lĩnh vực kinh doanh bất động sản, x&acirc;y biệt thự cho thu&ecirc;.&nbsp;<br />
Với khẩu hiệu &nbsp;&ldquo;Đ&aacute;nh bật thời gian, xuy&ecirc;n qua kh&ocirc;ng gian&rdquo; c&ugrave;ng chiến lược ph&aacute;t triển bền vững, lấy Bất Động Sản l&agrave;m lĩnh vực trọng t&acirc;m, Flex Home kh&ocirc;ng ngừng kết nối giữa người cần mua v&agrave; người cần b&aacute;n trong lĩnh vực bất động sản, đưa mọi người x&iacute;ch lại gần nhau hơn, vượt qua khoảng c&aacute;ch về thời gian v&agrave; kh&ocirc;ng gian, l&agrave; nơi đ&aacute;ng tin tưởng cho sự đầu tư bất động sản - một lĩnh vực kh&ocirc;ng ngừng ph&aacute;t triển qua thời gian.</span></span></p>

<blockquote>
<h3 style="text-align: center;"><span style="font-size:24px;"><span style="font-family:Arial,Helvetica,sans-serif;"><em><strong><span style="color:#1abc9c;">&ldquo;Đ&aacute;nh bật thời gian, xuy&ecirc;n qua kh&ocirc;ng gian&rdquo; </span></strong></em></span></span></h3>
</blockquote>

<h3 style="text-align: center;"><img alt="" src="' . url('') . '/storage/general/asset-4-at-3x.png" style="width: 90%;" /></h3>

<h4><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>2. TẦM NH&Igrave;N</strong></span></span></h4>

<ul>
	<li><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Th&acirc;u t&oacute;m địa b&agrave;n trong nước.</span></span></li>
	<li><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Vươn xa khắp c&aacute;c ch&acirc;u lục.</span></span></li>
</ul>

<h4><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>3. SỨ MẠNG</strong></span></span></h4>

<ul>
	<li><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Kiến tạo cộng đồng</span></span></li>
	<li><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">X&acirc;y dựng điểm đến</span></span></li>
	<li><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Vun đắp niềm vui</span></span></li>
</ul>

<p>&nbsp;</p>

<p><img alt="" src="' . url('') . '/storage/general/vietnam-office-4.jpg" style="width: 100%;" /></p>

<p>&nbsp;</p>
',
            ],
            [
                'name' => 'Liên hệ',
                'content' => '<p>[contact-form][/contact-form]<br />
&nbsp;</p>

<h3>Tìm đường đi</h3>

<p>[google-map]North Link Building, 10 Admiralty Street, 757695 Singapore[/google-map]</p>

<p>&nbsp;</p>',
            ],
            [
                'name' => 'Điều khoản và quy định',
                'description' => 'Quyền tác giả và các quyền sở hữu trí tuệ khác đối với mọi văn bản, hình ảnh, âm thanh, phần mềm và các nội dung khác trên trang web này thuộc quyền sở hữu của Flex Home cùng các công ty thành viên. Người truy cập được phép xem các nội dung trong trang web, trích dẫn nội dung bằng cách in ấn, tải về đĩa cứng và phân phát cho người khác chỉ với mục đích phi thương mại.',
                'content' => '<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Việc truy cập v&agrave; sử dụng trang web của Flex Home phụ thuộc v&agrave;o những điều khoản, điều kiện dưới đ&acirc;y, v&agrave; luật ph&aacute;p li&ecirc;n quan của Việt Nam.</span></span></p>

<h4 style="text-align: justify;"><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>1. Quyền t&aacute;c giả&nbsp;</strong></span></span></h4>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Quyền t&aacute;c giả v&agrave; c&aacute;c quyền sở hữu tr&iacute; tuệ kh&aacute;c đối với mọi văn bản, h&igrave;nh ảnh, &acirc;m thanh, phần mềm v&agrave; c&aacute;c nội dung kh&aacute;c tr&ecirc;n trang web n&agrave;y thuộc quyền sở hữu của Flex Home c&ugrave;ng c&aacute;c c&ocirc;ng ty th&agrave;nh vi&ecirc;n. Người truy cập được ph&eacute;p xem c&aacute;c nội dung trong trang web, tr&iacute;ch dẫn nội dung bằng c&aacute;ch in ấn, tải về đĩa cứng v&agrave; ph&acirc;n ph&aacute;t cho người kh&aacute;c chỉ với mục đ&iacute;ch phi thương mại, cung cấp th&ocirc;ng tin hoặc mục đ&iacute;ch c&aacute; nh&acirc;n. Bất kể nội dung n&agrave;o từ trang web n&agrave;y đều kh&ocirc;ng được sử dụng để b&aacute;n hoặc ph&acirc;n t&aacute;n để kiếm lợi v&agrave; cũng kh&ocirc;ng được chỉnh sửa hoặc đưa v&agrave;o bất kỳ ấn phẩm hoặc trang web n&agrave;o kh&aacute;c.</span></span></p>

<h4 style="text-align: justify;"><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>2. Nội dung</strong></span></span></h4>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Th&ocirc;ng tin tr&ecirc;n trang web n&agrave;y được bi&ecirc;n soạn với sự tin tưởng cao độ nhưng chỉ d&agrave;nh cho c&aacute;c mục đ&iacute;ch nghi&ecirc;n cứu th&ocirc;ng tin tổng qu&aacute;t. Tuy ch&uacute;ng t&ocirc;i nỗ lực duy tr&igrave; th&ocirc;ng tin cập nhật v&agrave; chuẩn x&aacute;c, nhưng ch&uacute;ng t&ocirc;i kh&ocirc;ng khẳng định hay bảo đảm theo bất kỳ c&aacute;ch thức n&agrave;o về sự đầy đủ, ch&iacute;nh x&aacute;c, đ&aacute;ng tin cậy, th&iacute;ch hợp hoặc c&oacute; sẵn li&ecirc;n quan đến trang web, hoặc th&ocirc;ng tin, sản phẩm, dịch vụ, hoặc h&igrave;nh ảnh li&ecirc;n quan trong trang web v&igrave; bất cứ mục đ&iacute;ch g&igrave;. </span></span></p>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Flex Home v&agrave; mọi nh&acirc;n vi&ecirc;n, nh&agrave; quản l&yacute;, v&agrave; c&aacute;c b&ecirc;n đại diện ho&agrave;n to&agrave;n kh&ocirc;ng chịu tr&aacute;ch nhiệm g&igrave; đối với bất kỳ tổn thất, thiệt hại hoặc chi ph&iacute; ph&aacute;t sinh do việc truy cập v&agrave; sử dụng trang web n&agrave;y v&agrave; c&aacute;c trang web được kết nối với n&oacute;, bao gồm nhưng kh&ocirc;ng giới hạn, việc mất đi lợi nhuận, c&aacute;c khoản lỗ trực tiếp hoặc gi&aacute;n tiếp. Ch&uacute;ng t&ocirc;i cũng kh&ocirc;ng chịu tr&aacute;ch nhiệm, hoặc li&ecirc;n đới tr&aacute;ch nhiệm nếu trang web tạm thời kh&ocirc;ng thể truy cập do c&aacute;c vấn đề kỹ thuật nằm ngo&agrave;i tầm kiểm so&aacute;t của ch&uacute;ng t&ocirc;i. Mọi b&igrave;nh luận, gợi &yacute;, h&igrave;nh ảnh, &yacute; tưởng v&agrave; những th&ocirc;ng tin hay t&agrave;i liệu kh&aacute;c m&agrave; người sử dụng chuyển cho ch&uacute;ng t&ocirc;i th&ocirc;ng qua trang web n&agrave;y sẽ trở th&agrave;nh t&agrave;i sản độc quyền của ch&uacute;ng t&ocirc;i, bao gồm cả c&aacute;c quyền c&oacute; thể ph&aacute;t sinh trong tương lai gắn liền với ch&uacute;ng t&ocirc;i.</span></span></p>

<p style="text-align:center"><img alt="" src="' . url('') . '/storage/general/copyright.jpg" style="width: 90%;" /></p>

<h4 style="text-align: justify;"><span style="font-size:18px;"><span style="font-family:Arial,Helvetica,sans-serif;"><strong>3. Lưu &yacute; c&aacute;c trang web được kết nối</strong></span></span></h4>

<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">Tại nhiều điểm trong trang web, người sử dụng c&oacute; thể nhận được c&aacute;c kết nối đến c&aacute;c trang web kh&aacute;c li&ecirc;n quan đến một kh&iacute;a cạnh cụ thể. Điều n&agrave;y kh&ocirc;ng c&oacute; nghĩa l&agrave; ch&uacute;ng t&ocirc;i c&oacute; li&ecirc;n quan đến những trang web hay c&ocirc;ng ty sở hữu những trang web n&agrave;y. D&ugrave; ch&uacute;ng t&ocirc;i c&oacute; &yacute; định kết nối người sử dụng đến c&aacute;c trang web cần quan t&acirc;m, nhưng ch&uacute;ng t&ocirc;i v&agrave; c&aacute;c nh&acirc;n vi&ecirc;n, nh&agrave; quản l&yacute;, hoặc c&aacute;c b&ecirc;n đại diện ho&agrave;n to&agrave;n kh&ocirc;ng chịu tr&aacute;ch nhiệm hoặc li&ecirc;n đới chịu tr&aacute;ch nhiệm g&igrave; đối với c&aacute;c trang web kh&aacute;c v&agrave; th&ocirc;ng tin chứa đựng trong đ&oacute;.</span></span></p>
<p style="text-align: justify;"><span style="font-size:16px;"><span style="font-family:Arial,Helvetica,sans-serif;">At many points in the website, users can get links to other websites related to a specific aspect. This does not mean that we are related to the websites or companies that own these websites. Although we intend to connect users to sites of interest, we are not responsible or jointly responsible for our employees, managers, or representatives. with other websites and information contained therein.</span></span></p>
',
            ],
            [
                'name' => 'Cookie Policy',
                'content' => Html::tag('h3', 'EU Cookie Consent') .
                    Html::tag(
                        'p',
                        'Để sử dụng trang web này, chúng tôi đang sử dụng Cookie và thu thập một số Dữ liệu. Để tuân thủ GDPR của Liên minh Châu Âu, chúng tôi cho bạn lựa chọn nếu bạn cho phép chúng tôi sử dụng một số Cookie nhất định và thu thập một số Dữ liệu.'
                    ) .
                    Html::tag('h4', 'Dữ liệu cần thiết') .
                    Html::tag(
                        'p',
                        'Dữ liệu cần thiết là cần thiết để chạy Trang web bạn đang truy cập về mặt kỹ thuật. Bạn không thể hủy kích hoạt chúng.'
                    ) .
                    Html::tag(
                        'p',
                        '- Session Cookie: PHP sử dụng Cookie để xác định phiên của người dùng. Nếu không có Cookie này, trang web sẽ không hoạt động.'
                    ) .
                    Html::tag(
                        'p',
                        '- XSRF-Token Cookie: Laravel tự động tạo "token" CSRF cho mỗi phiên người dùng đang hoạt động do ứng dụng quản lý. Token này được sử dụng để xác minh rằng người dùng đã xác thực là người thực sự đưa ra yêu cầu đối với ứng dụng.'
                    ),
            ],
            [
                'name' => 'Properties',
                'content' =>
                    Html::tag('div', '[properties-list title="Discover our properties" description="Chúng tôi đưa ra những lựa chọn tốt nhất với những dự án hot và uy tín bậc nhất hiện nay, hãy truy cập vào thông tin chi tiết bên dưới để tìm hiểu thêm nhé." number_of_properties_per_page="12"][/properties-list]')
                ,
            ],
            [
                'name' => 'Projects',
                'content' =>
                    Html::tag('div', '[projects-list  title="Discover our projects" description="We make the best choices with the hottest and most prestigious projects, please visit the details below to find out more" number_of_projects_per_page="12"][/projects-list]')
                ,
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['pages_id'] = $index + 1;

            DB::table('pages_translations')->insert($item);
        }
    }
}
