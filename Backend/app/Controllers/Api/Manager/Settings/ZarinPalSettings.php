<?php

namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\SiteSettingsModel;

/**
 * Get ZarinPal settings
 */
class ZarinPalSettings extends PrivateController
{
    protected $modelName = 'App\Models\SiteSettingsModel';

    public function index()
    {
        $this->admin();

        $model = new SiteSettingsModel();

        $settings = [
            'zarinpal_enabled' => $model->getSetting('zarinpal_enabled') ?? '0',
            'zarinpal_sandbox' => $model->getSetting('zarinpal_sandbox') ?? '0',
            'zarinpal_merchant_id' => $model->getSetting('zarinpal_merchant_id') ?? ''
        ];

        return $this->respond([
            'status' => 200,
            'error' => null,
            'data' => [
                'settings' => $settings
            ]
        ]);
    }
}
