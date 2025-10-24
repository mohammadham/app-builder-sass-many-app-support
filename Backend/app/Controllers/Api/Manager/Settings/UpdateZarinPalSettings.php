<?php

namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\SiteSettingsModel;

/**
 * Admin controller for ZarinPal settings
 */
class UpdateZarinPalSettings extends PrivateController
{
    protected $modelName = 'App\Models\SiteSettingsModel';

    public function index()
    {
        $this->admin();

        $data = $this->request->getJSON(true);
        
        $rules = [
            'zarinpal_enabled' => 'permit_empty|in_list[0,1]',
            'zarinpal_sandbox' => 'permit_empty|in_list[0,1]',
            'zarinpal_merchant_id' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 400,
                'error' => true,
                'messages' => $this->validator->getErrors()
            ]);
        }

        $model = new SiteSettingsModel();

        // Update settings
        if (isset($data['zarinpal_enabled'])) {
            $model->updateSetting('zarinpal_enabled', $data['zarinpal_enabled']);
        }

        if (isset($data['zarinpal_sandbox'])) {
            $model->updateSetting('zarinpal_sandbox', $data['zarinpal_sandbox']);
        }

        if (isset($data['zarinpal_merchant_id'])) {
            $model->updateSetting('zarinpal_merchant_id', $data['zarinpal_merchant_id']);
        }

        return $this->respond([
            'status' => 200,
            'error' => null,
            'messages' => [
                'success' => 'ZarinPal settings updated successfully'
            ]
        ]);
    }
}
