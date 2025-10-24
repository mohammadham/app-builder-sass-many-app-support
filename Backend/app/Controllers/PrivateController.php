<?php namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Psr\Log\LoggerInterface;

class PrivateController extends BaseController
{
    /**
     * User ID
     *
     * @var int
     */
    protected int $userId;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param LoggerInterface $logger
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $req = Services::request();

        $access_payload = $req->fetchGlobal("payload");

        $this->userId       = (int) $access_payload["user_id"];
    }
}