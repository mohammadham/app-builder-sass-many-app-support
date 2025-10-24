<?php namespace App\Controllers\Api\Manager\Support;

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
     * Get tickets list
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $offset = (int) $this->request->getGet("offset");
        $sort = esc($this->request->getGet("sort"));

        $tickets = new SupportTicketsModel();

        $where = $sort == "inbox"
            ? ["status" => 0]
            : ($sort == "pending" ? ["status" => 1] : ["status" => 2]);

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