<?php
namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Libraries\Authorization\Issuance;
use App\Libraries\Authorization\JWT;
use App\Libraries\Authorization\SignatureInvalidException;
use CodeIgniter\HTTP\ResponseInterface;
use UnexpectedValueException;

class Refresh extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Refresh JWT tokens
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->refresh_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $token = esc($this->request->getJsonVar("token"));

        $sign = $this->decodeJWT($token);

        if (!$sign) {
            return $this->respond(["message" => lang("Message.message_10")], 400);
        }

        if ($sign->type != "refresh") {
            return $this->respond(["message" => lang("Message.message_10")], 400);
        }

        $issuance = new Issuance();

        return $this->respond(["token" => $issuance->create_auth_tokens($sign->user)], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Decode JWT auth token
     * @param string $token
     * @return object|null
     */
    private function decodeJWT(string $token): ?object
    {
        try {
            $decoded = JWT::decode($token, env('jwt.secret.key.refresh'), array('HS256'));
        } catch (SignatureInvalidException|UnexpectedValueException $ex) {
            $decoded = null;
        }
        return $decoded;
    }

    /**
     * Get validation rules for refresh tokens
     * @return array
     */
    private function refresh_validation_type(): array
    {
        return [
            "token" => [
                "label" => lang("Fields.field_139"),
                "rules" => "required|max_length[300]|min_length[10]"
            ]
        ];
    }
}