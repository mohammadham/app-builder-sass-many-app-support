<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use CodeIgniter\HTTP\ResponseInterface;

class ExternalApiSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get API settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settings = new Settings();

        $items = [
            "codemagic_id"    => $settings->get_config("codemagic_id"),
            "codemagic_key"   => $settings->get_config("codemagic_key"),
            "github_branch"   => $settings->get_config("github_branch"),
            "github_repo"     => $settings->get_config("github_repo"),
            "github_token"    => $settings->get_config("github_token"),
            "github_username" => $settings->get_config("github_username"),
        ];

        return $this->respond($items, 200);
    }

}