<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\AdminController;
use App\Models\SiteSettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

class EnamadSettings extends AdminController
{
    /**
     * Get E-namad settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settingsModel = new SiteSettingsModel();

        $settings = [
            'enamad_enabled' => $settingsModel->getSetting('enamad_enabled') ?? '0',
            'enamad_code' => $settingsModel->getSetting('enamad_code') ?? '',
        ];

        return $this->respond([
            'settings' => $settings
        ], 200);
    }
}
