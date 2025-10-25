<?php namespace App\Controllers\Api\Data;

use App\Controllers\BaseController;
use App\Models\SiteSettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

class SiteSettings extends BaseController
{
    /**
     * Get public site settings (E-namad, landing, etc.)
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settingsModel = new SiteSettingsModel();

        $settings = [
            'enamad_enabled' => $settingsModel->getSetting('enamad_enabled') ?? '0',
            'enamad_code' => $settingsModel->getSetting('enamad_code') ?? '',
            'hero_title_en' => $settingsModel->getSetting('hero_title_en') ?? 'Build Your Mobile App in Minutes',
            'hero_title_fa' => $settingsModel->getSetting('hero_title_fa') ?? 'اپلیکیشن موبایل خود را در چند دقیقه بسازید',
            'hero_subtitle_en' => $settingsModel->getSetting('hero_subtitle_en') ?? 'Transform your website into a powerful mobile application',
            'hero_subtitle_fa' => $settingsModel->getSetting('hero_subtitle_fa') ?? 'وبسایت خود را به یک اپلیکیشن موبایل قدرتمند تبدیل کنید',
            'hero_cta_en' => $settingsModel->getSetting('hero_cta_en') ?? 'Get Started',
            'hero_cta_fa' => $settingsModel->getSetting('hero_cta_fa') ?? 'شروع کنید',
        ];

        return $this->respond($settings, 200);
    }
}
