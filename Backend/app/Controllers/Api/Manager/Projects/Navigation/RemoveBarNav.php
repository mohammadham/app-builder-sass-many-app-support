<?php namespace App\Controllers\Api\Manager\Projects\Navigation;

use App\Controllers\PrivateController;
use App\Models\BarNavigationModel;
use CodeIgniter\HTTP\ResponseInterface;

class RemoveBarNav extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Remove bar navigation item
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $id = (int) $this->request->getGet("id");

        $navigation = new BarNavigationModel();

        $item = $navigation
            ->where("id", $id)
            ->select("id,app_id,icon")
            ->first();

        if (!$item) {
            return $this->respond(["message" => lang("Message.message_17")], 400);
        }

        $navigation->delete($item["id"]);

        return $this->respond(["status" => "ok"], 200);
    }

}