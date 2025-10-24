<?php namespace App\Controllers\Api\Customer\Support;

use App\Controllers\PrivateController;
use App\Models\SupportCommentsModel;
use App\Models\SupportTicketsModel;
use CodeIgniter\HTTP\ResponseInterface;

define("LIMIT", 20);

class TicketsList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get tickets
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $offset = (int) $this->request->getGet("offset");
        $sort = esc($this->request->getGet("sort"));

        $tickets = new SupportTicketsModel();

        $where = $sort == "pending"
            ? ["status <" => 2, "user_id" => $this->userId]
            : ["status"   => 2, "user_id" => $this->userId];

        $list = $tickets
            ->where($where)
            ->orderBy("updated_at", "DESC")
            ->findAll(LIMIT, $offset);

        $comments = new SupportCommentsModel();

        $items = [];

        foreach ($list as $item) {
            $last_comment = $comments
                ->where("ticket_id", $item["id"])
                ->select("message,created_at")
                ->orderBy("id", "DESC")
                ->first();
            $items[] = [
                "uid"     => $item["uid"],
                "title"   => $item["title"],
                "status"  => (int) $item["status"],
                "updated" => date('d-m-Y H:i', $item['updated_at']),
                "message" => [
                    "comment" => $last_comment["message"],
                    "created" => date('d-m-Y', $last_comment['created_at']),
                ]
            ];
        }

        $total = $tickets
            ->where($where)
            ->countAllResults();

        return $this->respond(["list" => $items, "total" => $total], 200);
    }
}