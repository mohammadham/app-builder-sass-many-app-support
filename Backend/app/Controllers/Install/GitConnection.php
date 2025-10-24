<?php namespace App\Controllers\Install;

use App\Controllers\BaseController;
use App\Libraries\GitHub;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class GitConnection extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Github connection api
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->api_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $token = esc($this->request->getJsonVar("git_token"));
        $username = esc($this->request->getJsonVar("git_username"));

        $github = new GitHub();

        $res = $github->create_fork($token, $username);

        if (!$res["event"]) {
            return $this->respond(["message" => $res["message"]], 400);
        }

        $settings = new SettingsModel();

        $settings->update("github_repo", [
            "value" => "flangapp_pro",
        ]);

        $settings->update("github_token", [
            "value" => $token,
        ]);

        $settings->update("github_username", [
            "value" => $username,
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for API form
     * @return array
     */
    private function api_validation_type(): array
    {
        return [
            "git_username" => [
                "label" => lang("Install.install_21"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "git_token"    => [
                "label" => lang("Install.install_18"),
                "rules" => "required|min_length[3]|max_length[100]"
            ]
        ];
    }
}