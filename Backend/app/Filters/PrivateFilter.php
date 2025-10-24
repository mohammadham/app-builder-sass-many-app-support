<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;
use App\Models\UsersModel;
use App\Libraries\Authorization\BeforeValidException;
use App\Libraries\Authorization\SignatureInvalidException;
use App\Libraries\Authorization\JWT;
use UnexpectedValueException;

class PrivateFilter implements FilterInterface
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /* @param RequestInterface $request
     * @param null $arguments
     * @return ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        if (empty($request->getHeaderLine('Authorization'))) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(["message" => lang("Message.message_9")]);
        }

        $sign = $this->decodeJWT(esc($request->getHeaderLine('Authorization')));

        if (!$sign) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(["message" => lang("Message.message_10")]);
        }

        $users = new UsersModel();

        $user = $users
            ->where("id", $sign->user)
            ->select("id")
            ->first();

        if (!$user) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(["message" => lang("Message.message_11")]);
        }

        $req = Services::request();
        $req->setGlobal("payload", ["user_id"  => $user["id"]]);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
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
            $decoded = JWT::decode($token, env('jwt.secret.key.access'), array('HS256'));
        } catch (SignatureInvalidException|BeforeValidException|UnexpectedValueException $ex) {
            $decoded = null;
        }
        return $decoded;
    }
}