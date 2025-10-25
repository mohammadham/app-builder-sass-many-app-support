<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\AdminController;
use App\Models\SiteSettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

class LandingSettings extends AdminController
{
    /**
     * Get landing page settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settingsModel = new SiteSettingsModel();

        $settings = [
            'hero_title_en' => $settingsModel->getSetting('hero_title_en') ?? 'Build Your Mobile App in Minutes',
            'hero_title_fa' => $settingsModel->getSetting('hero_title_fa') ?? 'اپلیکیشن موبایل خود را در چند دقیقه بسازید',
            'hero_subtitle_en' => $settingsModel->getSetting('hero_subtitle_en') ?? 'Transform your website into a powerful mobile application',
            'hero_subtitle_fa' => $settingsModel->getSetting('hero_subtitle_fa') ?? 'وبسایت خود را به یک اپلیکیشن موبایل قدرتمند تبدیل کنید',
            'hero_cta_en' => $settingsModel->getSetting('hero_cta_en') ?? 'Get Started',
            'hero_cta_fa' => $settingsModel->getSetting('hero_cta_fa') ?? 'شروع کنید',
        ];

        return $this->respond([
            'settings' => $settings
        ], 200);
    }
}
