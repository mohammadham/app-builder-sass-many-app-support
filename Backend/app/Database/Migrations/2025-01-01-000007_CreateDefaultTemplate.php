<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDefaultTemplate extends Migration
{
    public function up()
    {
        // Insert default primary template (Flutter WebView App)
        $data = [
            'uid'            => 'flutter_webview_primary',
            'name_fa'        => 'اپلیکیشن وب‌ویو (پیش‌فرض)',
            'name_en'        => 'WebView Application (Default)',
            'description_fa' => 'تبدیل وبسایت شما به اپلیکیشن موبایل اندروید و iOS',
            'description_en' => 'Convert your website to Android and iOS mobile application',
            'category'       => 'webview',
            'tags'           => json_encode(['webview', 'website', 'pwa', 'flutter']),
            'thumbnail'      => null,
            'github_repo'    => 'flangapp_pro_starter',
            'github_branch'  => 'main',
            'config_schema'  => json_encode([
                'fields' => [
                    [
                        'name' => 'app_name',
                        'type' => 'text',
                        'label_fa' => 'نام اپلیکیشن',
                        'label_en' => 'App Name',
                        'required' => true,
                        'immutable' => false,
                    ],
                    [
                        'name' => 'package_id',
                        'type' => 'text',
                        'label_fa' => 'شناسه بسته (Package ID)',
                        'label_en' => 'Package ID',
                        'required' => true,
                        'immutable' => true,
                        'pattern' => '^[a-z][a-z0-9_]*(\.[a-z][a-z0-9_]*)+$',
                        'placeholder' => 'com.example.app',
                    ],
                    [
                        'name' => 'website_url',
                        'type' => 'url',
                        'label_fa' => 'آدرس وبسایت',
                        'label_en' => 'Website URL',
                        'required' => true,
                        'immutable' => false,
                    ],
                    [
                        'name' => 'orientation',
                        'type' => 'select',
                        'label_fa' => 'جهت صفحه',
                        'label_en' => 'Screen Orientation',
                        'required' => true,
                        'immutable' => false,
                        'options' => [
                            ['value' => '0', 'label_fa' => 'همه جهت‌ها', 'label_en' => 'All Orientations'],
                            ['value' => '1', 'label_fa' => 'عمودی', 'label_en' => 'Portrait'],
                            ['value' => '2', 'label_fa' => 'افقی', 'label_en' => 'Landscape'],
                        ],
                    ],
                    [
                        'name' => 'theme_color',
                        'type' => 'color',
                        'label_fa' => 'رنگ تم',
                        'label_en' => 'Theme Color',
                        'required' => true,
                        'immutable' => false,
                        'default' => '#2196F3',
                    ],
                    [
                        'name' => 'pull_to_refresh',
                        'type' => 'boolean',
                        'label_fa' => 'بارگذاری مجدد با کشیدن',
                        'label_en' => 'Pull to Refresh',
                        'required' => false,
                        'immutable' => false,
                        'default' => true,
                    ],
                    [
                        'name' => 'enable_gps',
                        'type' => 'boolean',
                        'label_fa' => 'فعال‌سازی GPS',
                        'label_en' => 'Enable GPS',
                        'required' => false,
                        'immutable' => false,
                        'default' => false,
                    ],
                    [
                        'name' => 'enable_camera',
                        'type' => 'boolean',
                        'label_fa' => 'فعال‌سازی دوربین',
                        'label_en' => 'Enable Camera',
                        'required' => false,
                        'immutable' => false,
                        'default' => false,
                    ],
                    [
                        'name' => 'enable_microphone',
                        'type' => 'boolean',
                        'label_fa' => 'فعال‌سازی میکروفون',
                        'label_en' => 'Enable Microphone',
                        'required' => false,
                        'immutable' => false,
                        'default' => false,
                    ],
                ],
            ]),
            'status'         => 1,
            'is_primary'     => 1,
            'created_at'     => time(),
            'updated_at'     => time(),
        ];

        $this->db->table('templates')->insert($data);
    }

    public function down()
    {
        $this->db->table('templates')->where('uid', 'flutter_webview_primary')->delete();
    }
}
