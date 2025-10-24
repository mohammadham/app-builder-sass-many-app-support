<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UploadLogo extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Upload website logo
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->upload_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $image = $this->request->getFile('logo');
        $name = $image->getRandomName();
        $image->move(ROOTPATH.'public_html/static', $name);

        $settings = new SettingsModel();

        $settings->update("site_logo", [
            "value" => $name
        ]);

        return $this->respond(["logo" => base_url("static/".$name."?unix=".time())], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for upload logo
     * @return array
     */
    private function upload_validation_type(): array
    {
        return [
            'logo' => [
                'label' => lang("Fields.field_30"),
                'rules' => 'uploaded[logo]|max_size[logo,500]|ext_in[logo,png,jpg]|max_dims[logo,1200,1200]'
            ],
        ];
    }
}