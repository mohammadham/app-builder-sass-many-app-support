<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use CodeIgniter\HTTP\ResponseInterface;

class LicenseSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get license settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settings = new Settings();

        return $this->respond(["value" => $settings->get_config("license")], 200);
    }

}