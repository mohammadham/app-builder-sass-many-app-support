<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use CodeIgniter\HTTP\ResponseInterface;

class SiteSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get site settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settings = new Settings();

        $items = [
            "site_name"       => $settings->get_config("site_name"),
            "site_url"        => $settings->get_config("site_url"),
            "site_logo"       => base_url("static/".$settings->get_config("site_logo")),
            "google_enabled"  => (int) $settings->get_config("google_enabled"),
            "google_id"       => $settings->get_config("google_id"),
            "currency_symbol" => $settings->get_config("currency_symbol"),
            "currency_code"   => $settings->get_config("currency_code")
        ];

        return $this->respond($items, 200);
    }

}