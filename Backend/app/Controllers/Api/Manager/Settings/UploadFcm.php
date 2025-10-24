<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UploadFcm extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Upload FCM JSON key
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->upload_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $key = $this->request->getFile('fcm');
        $name = $key->getRandomName();
        $key->move(WRITEPATH.'storage/fcm', $name);

        $settings = new SettingsModel();

        $settings->update("one_signal_fcm_file", [
            "value" => $name
        ]);

        return $this->respond(["one_signal_fcm_file" => $name], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for upload fcm
     * @return array
     */
    private function upload_validation_type(): array
    {
        return [
            'fcm' => [
                'label' => lang("Fields.field_156"),
                'rules' => 'uploaded[fcm]|max_size[fcm,500]|ext_in[fcm,json]'
            ],
        ];
    }
}