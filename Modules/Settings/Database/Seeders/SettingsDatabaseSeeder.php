<?php

namespace Modules\Settings\Database\Seeders;

use Helper;
use Exception;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Settings\Entities\Setting;
use Modules\Lecturers\Entities\Lecturer;

class SettingsDatabaseSeeder extends Seeder
{

    /**
     * Create a new setting if it doesn't exist.
     *
     * @param string $slug
     * @param string $enTitle
     * @param string $arTitle
     * @param string|null $value
     * @param string|null $parentSlug
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    protected function createSetting($slug, $enTitle, $arTitle, $value = null, $parentSlug = null)
    {
        if (Setting::where('slug', $slug)->doesntExist()) {
            try {
                $setting                = new Setting();
                $setting->translateOrNew('en')->title = $enTitle;
                $setting->translateOrNew('ar')->title = $arTitle;
                $setting->lecturer_id   = Lecturer::first()->id;
                $setting->parent_id     = $parentSlug ? Helper::getIdBySettingSlug($parentSlug) : null;
                $setting->slug          = $slug;
                $setting->value         = $value ?? null;
                $setting->is_active     = true;
                $setting->created_by    = User::first()->id;
                $setting->save();
            } catch (Exception $e) {
                Log::warning($e);
            }
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'slug'      => 'mobile_settings',
                'enTitle'   => 'Mobile Settings',
                'arTitle'   => 'إعدادات التطبيق',
                'icon'      => 'tabler-devices',
                'children' => [
                    [
                        'slug'      => 'app_name',
                        'enTitle'   => 'App Name',
                        'arTitle'   => 'أسم التطبيق',
                        'value'     => env('APP_NAME')
                    ],
                    [
                        'slug'      => 'android_package_name',
                        'enTitle'   => 'Android Package Name',
                        'arTitle'   => 'اسم حزمة الأندرويد',
                        'value'     => 'com.amr.' . env('APP_NAME')
                    ],
                    [
                        'slug'      => 'ios_bundle',
                        'enTitle'   => 'IOS Bundle',
                        'arTitle'   => 'باقه اي او اس',
                        'value'     => 'com.amr.' . env('APP_NAME')
                    ],
                    [
                        'slug'      => 'app_logo',
                        'enTitle'   => 'App Logo',
                        'arTitle'   => 'لوجو التطبيق'
                    ],
                    [
                        'slug'      => 'primary_color',
                        'enTitle'   => 'Primary Color',
                        'arTitle'   => 'اللون الرئيسي',
                        'value'     => '0xff76974C'
                    ],
                    [
                        'slug'      => 'color_primary_light',
                        'enTitle'   => 'Color Primary Light',
                        'arTitle'   => 'اللون الأبيض الأساسي',
                        'value'     => '0xffE8F0DE'
                    ],
                    [
                        'slug'      => 'color_primary_dark',
                        'enTitle'   => 'Color Primary Dark',
                        'arTitle'   => 'اللون الأسود الأساسي',
                        'value'     => '0xff76974C'
                    ],
                    [
                        'slug'      => 'color_secondary',
                        'enTitle'   => 'Color Secondary',
                        'arTitle'   => 'اللون الثانوي',
                        'value'     => '0xffb6aeae'
                    ],
                    [
                        'slug'      => 'color_secondary_light',
                        'enTitle'   => 'Color Secondary Light',
                        'arTitle'   => 'اللون الأبيض الثانوي',
                        'value'     => '0xffe8e0e0'
                    ],
                    [
                        'slug'      => 'color_secondary_dark',
                        'enTitle'   => 'Color Secondary Dark',
                        'arTitle'   => 'اللون الأسود الثانوي',
                        'value'     => '0xff867f7f'
                    ],
                    [
                        'slug'      => 'our_goal',
                        'enTitle'   => 'Our Goal',
                        'arTitle'   => 'هدفنا'
                    ],
                    [
                        'slug'      => 'our_vision',
                        'enTitle'   => 'Our Vision',
                        'arTitle'   => 'رؤيتنا'
                    ],
                    [
                        'slug'      => 'email_address',
                        'enTitle'   => 'Email Address',
                        'arTitle'   => 'عنوان البريد الإلكتروني',
                        'value'     => 'info@' .env('APP_NAME') . '.com'
                    ],
                    [
                        'slug'      => 'phone_number',
                        'enTitle'   => 'Phone Number',
                        'arTitle'   => 'رقم الهاتف',
                        'value'     => '+123456789'
                    ]
                ],
            ],
            [
                'slug'      => 'mobile_themes',
                'enTitle'   => 'Mobile Themes',
                'arTitle'   => 'أشكال الجوال',
                'icon'      => 'tabler-brand-firebase',
                'children' => [
                    [
                        'slug'      => 'home_style',
                        'enTitle'   => 'Home Style',
                        'arTitle'   => 'خصائص الصفحة الرئيسية',
                        'value'     => 'style1'
                    ],
                    [
                        'slug'      => 'home_structure',
                        'enTitle'   => 'Home Structure',
                        'arTitle'   => 'بناء الصفحة الرئيسية',
                        'value'     => '[sliders, consultations]'
                    ]
                ],
            ],
            [
                'slug'      => 'on_boarding',
                'enTitle'   => 'onBoarding',
                'arTitle'   => 'شاشات التحميل',
                'icon'      => 'tabler-carousel-horizontal'
            ],
            [
                'slug'      => 'splash_screen',
                'enTitle'   => 'Splash Screen',
                'arTitle'   => 'شاشة البداية',
                'icon'      => 'tabler-dual-screen'
            ],
            [
                'slug'      => 'email_configurations',
                'enTitle'   => 'Email Configurations',
                'arTitle'   => 'إعدادات الإيميل',
                'icon'      => 'tabler-mail-opened'
            ],
            [
                'slug'      => 'sms_configurations',
                'enTitle'   => 'SMS Configurations',
                'arTitle'   => 'إعدادات الرسائل القصيرة',
                'icon'      => 'tabler-device-mobile-message'
            ],
            [
                'slug'      => 'push_notification',
                'enTitle'   => 'Push Notification',
                'arTitle'   => 'إرسالة الأشعارات',
                'icon'      => 'tabler-device-mobile-vibration'
            ],
            [
                'slug'      => 'consultations_types',
                'enTitle'   => 'Consultations Types',
                'arTitle'   => 'أنواع الاستشارات',
                'icon'      => 'tabler-apps',
                'children' => [
                    [
                        'slug'      => 'emergency_consultations',
                        'enTitle'   => 'Emergency Consultations',
                        'arTitle'   => 'استشارات الطوارئ'
                    ],
                    [
                        'slug'      => 'appointments_consultations',
                        'enTitle'   => 'Appointments Consultations',
                        'arTitle'   => 'مواعيد الاستشارات'
                    ],
                    [
                        'slug'      => 'texts_consultations',
                        'enTitle'   => 'Texts Consultations',
                        'arTitle'   => 'استشارات النصوص'
                    ],
                    [
                        'slug'      => 'videos_consultations',
                        'enTitle'   => 'Videos Consultations',
                        'arTitle'   => 'استشارات الفيديو'
                    ],
                    [
                        'slug'      => 'voice_calls_consultations',
                        'enTitle'   => 'Voices Consultations',
                        'arTitle'   => 'استشارات صوتية'
                    ],
                    [
                        'slug'      => 'faqs_consultations',
                        'enTitle'   => 'FAQs Consultations',
                        'arTitle'   => 'استشارات الأسئلة الشائعة'
                    ],
                    [
                        'slug'      => 'meeting_consultations',
                        'enTitle'   => 'Meeting Consultations',
                        'arTitle'   => 'اجتماعات التشاور'
                    ]
                ],
            ],
            [
                'slug'      => 'consultations_settings',
                'enTitle'   => 'Consultations Settings',
                'arTitle'   => 'إعدادات الاستشارات',
                'icon'      => 'tabler-plug'
            ],
            [
                'slug'      => 'courses_types',
                'enTitle'   => 'Courses Types',
                'arTitle'   => 'أنواع الدورات',
                'icon'      => 'tabler-brand-youtube-kids',
                'children' => [
                    [
                        'slug'      => 'online_course',
                        'enTitle'   => 'Online Course',
                        'arTitle'   => 'الدورات الاونلاين'
                    ],
                    [
                        'slug'      => 'offline_course',
                        'enTitle'   => 'Offline Course',
                        'arTitle'   => 'الدورات المسجله'
                    ],
                    [
                        'slug'      => 'diplomat_course',
                        'enTitle'   => 'Diplomat Course',
                        'arTitle'   => 'البرنامج التربوي'
                    ]
                ],
            ],
            [
                'slug'      => 'books_types',
                'enTitle'   => 'Books Types',
                'arTitle'   => 'أنواع الكتب',
                'icon'      => 'tabler-books',
                'children' => [
                    [
                        'slug'      => 'business_administration',
                        'enTitle'   => 'Business Administration',
                        'arTitle'   => 'إدارة الأعمال'
                    ]
                ],
            ],
            [
                'slug'      => 'services',
                'enTitle'   => 'Services',
                'arTitle'   => 'الخدمات',
                'icon'      => 'tabler-magnet'
            ],
            [
                'slug'      => 'teams',
                'enTitle'   => 'Teams',
                'arTitle'   => 'فريق العمل',
                'icon'      => 'tabler-friends'
            ],
            [
                'slug'      => 'sliders',
                'enTitle'   => 'Sliders',
                'arTitle'   => 'السليدر',
                'icon'      => 'tabler-adjustments'
            ],
            [
                'slug'      => 'work_fields',
                'enTitle'   => 'Work Fields',
                'arTitle'   => 'مجالات العمل',
                'icon'      => 'tabler-old'
            ],
            [
                'slug'      => 'media_settings',
                'enTitle'   => 'Media Settings',
                'arTitle'   => 'إعدادات الوسائط',
                'icon'      => 'tabler-topology-complex'
            ],
        ];

        // Create settings
        foreach ($settings as $setting) {
            $this->createSetting($setting['slug'], $setting['enTitle'], $setting['arTitle']);
            if (isset($setting['children'])) {
                foreach ($setting['children'] as $child) {
                    $this->createSetting($child['slug'], $child['enTitle'], $child['arTitle'], $child['value'] ?? null, $setting['slug']);
                }
            }
        }

    }
}
