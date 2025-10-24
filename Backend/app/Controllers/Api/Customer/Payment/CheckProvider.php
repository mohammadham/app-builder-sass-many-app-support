<?php namespace App\Controllers\Api\Customer\Payment;

use App\Controllers\PrivateController;
use App\Models\DepositMethodsModel;
use CodeIgniter\HTTP\ResponseInterface;

class CheckProvider extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get default payment method link
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("status", 1)
            ->select("route")
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_85")], 404);
        }

        return $this->respond([
            "url" => base_url("private/payment/".$method["route"])
        ], 200);
    }
}