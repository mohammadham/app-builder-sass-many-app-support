<?php namespace App\Controllers\Api\Customer\Support;

use App\Controllers\PrivateController;
use App\Models\SupportCommentsModel;
use App\Models\SupportTicketsModel;
use CodeIgniter\HTTP\ResponseInterface;

class TicketDetail extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get ticket detail and comments
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $tickets = new SupportTicketsModel();

        $ticket = $tickets
            ->where("uid", $uid)
            ->where("user_id", $this->userId)
            ->select("id,title,status,uid")
            ->first();

        if (!$ticket) {
            return $this->respond(["message" => lang("Message.message_49")], 404);
        }

        $comments = new SupportCommentsModel();

        $ticket_comments = $comments
            ->where("ticket_id", $ticket["id"])
            ->orderBy("id", "ASC")
            ->findAll();

        $list = [];

        foreach ($ticket_comments as $comment) {
            $list[] = [
                "uid"        => $comment["uid"],
                "message"    => $comment["message"],
                "created"    => date('d-m-Y H:i', $comment['created_at']),
                "estimation" => (int) $comment["estimation"],
                "admin"      => !$comment["user_id"],
            ];
        }

        return $this->respond([
            "list"   => $list,
            "ticket" => [
                "title"  => $ticket["title"],
                "uid"    => $ticket["uid"],
                "status" => (int) $ticket["status"]
            ]
        ], 200);
    }
}