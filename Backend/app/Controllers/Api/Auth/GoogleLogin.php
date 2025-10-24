<?php
namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Libraries\Authorization\Issuance;
use App\Libraries\Authorization\Passport;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class GoogleLogin extends BaseController
{
    private string $google_auth_url = "https://www.googleapis.com/oauth2/v3/";

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Login with Google account
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->google_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $token = esc($this->request->getJsonVar("token"));

        try {
            $res = Services::curlrequest([
                "baseURI"     => $this->google_auth_url,
                "headers"     => [
                    "Content-Type"  => "application/json",
                    "Authorization" => "Bearer ".$token
                ],
            ])->get("userinfo");

            $data = json_decode($res->getBody());

            $users = new UsersModel();

            $user = $users
                ->where("email", $data->email)
                ->select("id,email,password,admin,deleted_at")
                ->first();

            $issuance = new Issuance();
            $passport = new Passport(12, false);

            helper('text');

            if (!$user) {
                $userId = $users->insert([
                    "email"    => $data->email,
                    "password" => $passport->HashPassword(random_string('alnum', 16)),
                ]);
                return $this->respond([
                    "email" => $data->email,
                    "token" => $issuance->create_auth_tokens($userId),
                    "login" => true,
                    "admin" => 0
                ], 200);
            }

            if ($user['deleted_at']) {
                return $this->respond(["message" => lang("Message.message_1")], 400);
            }

            return $this->respond([
                "email" => $user['email'],
                "token" => $issuance->create_auth_tokens($user["id"]),
                "login" => true,
                "admin" => (bool) $user["admin"]
            ], 200);
        } catch (Exception $e) {
            return $this->respond(["message" => lang("Message.message_58")], 400);
        }
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for Google sign in
     * @return array
     */
    private function google_validation_type(): array
    {
        return [
            "token" => [
                "label" => lang("Fields.field_82"),
                "rules" => "required|min_length[20]"
            ],
        ];
    }
}