<?php namespace App\Controllers\Api\Manager\Support;

use App\Controllers\PrivateController;
use App\Models\SupportTicketsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class ChangeTicketStatus extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Change ticket status
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $tickets = new SupportTicketsModel();

        $ticket = $tickets
            ->where("uid", $uid)
            ->select("id,status")
            ->first();

        if (!$ticket) {
            return $this->respond(["message" => lang("Message.message_49")], 404);
        }

        $tickets = new SupportTicketsModel();

        $tickets->update($ticket["id"], [
            "status" => $ticket["status"] == 2 ? 0 : 2
        ]);

        return $this->respond(["status" => "ok"], 200);
    }
}