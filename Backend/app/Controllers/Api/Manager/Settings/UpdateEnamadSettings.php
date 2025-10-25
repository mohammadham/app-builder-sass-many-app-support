<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\AdminController;
use App\Models\SiteSettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

class UpdateEnamadSettings extends AdminController
{
    /**
     * Update E-namad settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $rules = [
            'enamad_enabled' => 'required|in_list[0,1]',
            'enamad_code' => 'permit_empty|max_length[2000]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'messages' => ['error' => $this->validator->getErrors()]
            ], 400);
        }

        $settingsModel = new SiteSettingsModel();
        $data = $this->request->getJSON(true);

        $settingsModel->updateSetting('enamad_enabled', $data['enamad_enabled']);
        $settingsModel->updateSetting('enamad_code', $data['enamad_code'] ?? '');

        return $this->respond([
            'message' => 'E-namad settings updated successfully'
        ], 200);
    }
}
