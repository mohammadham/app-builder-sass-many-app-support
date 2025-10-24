<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\EmailConfigModel;
use CodeIgniter\HTTP\ResponseInterface;

class EmailSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get email settings
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $emailSettings = new EmailConfigModel();

        $config = $emailSettings
            ->where("id", 1)
            ->first();

        return $this->respond([
            "host"     => $config["host"],
            "user"     => $config["user"],
            "port"     => (int) $config["port"],
            "timeout"  => (int) $config["timeout"],
            "charset"  => $config["charset"],
            "sender"   => $config["sender"],
            "password" => $config["password"]
        ], 200);
    }

}