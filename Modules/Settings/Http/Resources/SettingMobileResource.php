<?php

namespace Modules\Settings\Http\Resources;

use App\Models\Imageable;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Setting;
use Modules\Settings\Http\Resources\SettingShowResource;
use Modules\Settings\Http\Resources\SettingRedirectMobileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        // appLogo
        $appLogo    = $this->where('slug', 'app_logo')->first();
        $logoUrl    = $appLogo->image['url'] ?? NULL;
        $appLogo    = $logoUrl
            ? Imageable::getImagePath('settings', $logoUrl, true)
            : request()->root() . '/bridge-logo.png';

        // homeLogo
        $homeLogo       = $this->where('slug', 'home_app_logo')->first();
        $homeUrl        = $homeLogo->image['url'] ?? NULL;
        $homeAppLogo    = Imageable::getImagePath('settings', $homeUrl, true);

        // video
        $videoHomePromo     = $this->where('slug', 'videohomepromo')->first();
        $videoHomePromoUrl  = $videoHomePromo->image['url'] ?? NULL;
        $video              = Imageable::getImagePath('settings', $videoHomePromoUrl, true);

        return [
            'appName'                   => $this->where('slug', 'app_name')->first()['value'] ?? 'HappyLand',
            'androidPackageName'        => $this->where('slug', 'android_package_name')->first()['value'] ?? 'com.amr.imallsApp',
            'iosBundle'                 => $this->where('slug', 'ios_bundle')->first()['value'] ?? 'com.amr.imallsApp',
            'androidAppUrl'             => $this->where('slug', 'android_app_url')->first()['value'] ?? NULL,
            'iosAppUrl'                 => $this->where('slug', 'ios_app_url')->first()['value'] ?? NULL,
            'appLogo'                   => $appLogo,
            'homeAppLogo'               => $homeAppLogo,
            'appCurrency'               => $this->where('slug', 'app_currency')->first()['value'] ?? 'KWD',
            'primaryColor'              => $this->where('slug', 'primary_color')->first()['value'] ?? '0xFF6a0c80',
            'colorPrimaryLight'         => $this->where('slug', 'color_primary_light')->first()['value'] ?? '0xffe7bcfa',
            'colorPrimaryDark'          => $this->where('slug', 'color_primary_dark')->first()['value'] ?? '0xFF3b0042',
            'colorSecondary'            => $this->where('slug', 'color_secondary')->first()['value'] ?? '0xffb6aeae',
            'colorSecondaryLight'       => $this->where('slug', 'color_secondary_light')->first()['value'] ?? '0xffe8e0e0',
            'colorSecondaryDark'        => $this->where('slug', 'color_secondary_dark')->first()['value'] ?? '0xff867f7f',
            'videoHomePromo'            => $video,
            'onBoarding'                => SettingRedirectMobileResource::collection(Setting::getSettingDataBySlug('on_boarding')),
            'splashScreen'              => SettingRedirectMobileResource::collection(Setting::getSettingDataBySlug('splash_screen')),
            'socialMedia'               => SettingShowResource::collection(Setting::getSettingDataBySlug('social_media')),
            'appStyle'                  => [
                'homeStyle' => [
                    'homeStyle'         => $this->where('slug', 'home_style')->first()['value'] ?? 'style1',
                    'homeStructure'     => explode(',', $this->where('slug', 'home_structure')->first()['value']) ?? [],
                ],
                'homeStructureStyle' => [
                    [
                        'style'             => $this->where('slug', 'home_structure_style1')->first()['value'] ?? 'sliders',
                        'productItemStyle'  => $this->where('slug', 'home_structure_item_style1')->first()['value'] ?? 'style1',
                    ],
                    [
                        'style'             => $this->where('slug', 'home_structure_style2')->first()['value'] ?? 'categories',
                        'productItemStyle'  => $this->where('slug', 'home_structure_item_style2')->first()['value'] ?? 'style1',
                    ],
                    [
                        'style'             => $this->where('slug', 'home_structure_style3')->first()['value'] ?? 'brands',
                        'productItemStyle'  => $this->where('slug', 'home_structure_item_style3')->first()['value'] ?? 'style1',
                    ],
                    [
                        'style'             => $this->where('slug', 'home_structure_style4')->first()['value'] ?? 'bestSelling',
                        'productItemStyle'  => $this->where('slug', 'home_structure_item_style4')->first()['value'] ?? 'style1',
                    ],
                    [
                        'style'             => $this->where('slug', 'home_structure_style5')->first()['value'] ?? 'mostRecent',
                        'productItemStyle'  => $this->where('slug', 'home_structure_item_style5')->first()['value'] ?? 'style2',
                    ],
                    [
                        'style'             => $this->where('slug', 'home_structure_style6')->first()['value'] ?? 'popular',
                        'productItemStyle'  => $this->where('slug', 'home_structure_item_style6')->first()['value'] ?? 'style3',
                    ]
                ],

                'productDetailsStyle' => [
                    'style'                 => $this->where('slug', 'product_details_style')->first()['value'] ?? 'style1',
                    'relatedProductItemStyle' => $this->where('slug', 'product_details_item_style')->first()['value'] ?? 'style1',
                ],
                'cartStyle' => [
                    'style'                 => $this->where('slug', 'cart_style')->first()['value'] ?? 'style1',
                    'productItemStyle'      => $this->where('slug', 'cart_item_style')->first()['value'] ?? 'style1',
                ],
                'categoriesStyle' => [
                    'style'                 => $this->where('slug', 'categories_style')->first()['value'] ?? 'style1',
                    'categoryItemStyle'     => $this->where('slug', 'categories_item_style')->first()['value'] ?? 'style1',
                ],
                'brandsStyle' => [
                    'style'                 => $this->where('slug', 'brands_style')->first()['value'] ?? 'style1',
                    'brandItemStyle'        => $this->where('slug', 'brands_item_style')->first()['value'] ?? 'style1',
                ],
                'offersStyle' => [
                    'style'                 => $this->where('slug', 'offers_style')->first()['value'] ?? 'style1',
                    'offerItemStyle'        => $this->where('slug', 'offers_item_style')->first()['value'] ?? 'style1',
                ],
            ],
        ];
    }
}
