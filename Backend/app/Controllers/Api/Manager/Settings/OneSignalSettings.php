<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use CodeIgniter\HTTP\ResponseInterface;

class OneSignalSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get onesignal settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settings = new Settings();

        $items = [
            "one_signal_auth_key"        => $settings->get_config("one_signal_auth_key"),
            "one_signal_fcm_file"        => $settings->get_config("one_signal_fcm_file"),
            "one_signal_organization_id" => $settings->get_config("one_signal_organization_id")
        ];

        return $this->respond($items, 200);
    }

}