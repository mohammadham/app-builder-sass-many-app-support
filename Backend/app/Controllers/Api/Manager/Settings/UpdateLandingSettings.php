<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\AdminController;
use App\Models\SiteSettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

class UpdateLandingSettings extends AdminController
{
    /**
     * Update landing page settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $rules = [
            'hero_title_en' => 'required|max_length[255]',
            'hero_title_fa' => 'required|max_length[255]',
            'hero_subtitle_en' => 'required|max_length[500]',
            'hero_subtitle_fa' => 'required|max_length[500]',
            'hero_cta_en' => 'required|max_length[100]',
            'hero_cta_fa' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'messages' => ['error' => $this->validator->getErrors()]
            ], 400);
        }

        $settingsModel = new SiteSettingsModel();
        $data = $this->request->getJSON(true);

        foreach ($data as $key => $value) {
            $settingsModel->updateSetting($key, $value);
        }

        return $this->respond([
            'message' => 'Settings updated successfully'
        ], 200);
    }
}
