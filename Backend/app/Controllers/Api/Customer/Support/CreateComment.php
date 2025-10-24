<?php namespace App\Controllers\Api\Customer\Support;

use App\Controllers\PrivateController;
use App\Libraries\Uid;
use App\Models\SupportCommentsModel;
use App\Models\SupportTicketsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class CreateComment extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create new comment
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->create_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $uid = esc($this->request->getGet("uid"));

        $tickets = new SupportTicketsModel();

        $ticket = $tickets
            ->where("uid", $uid)
            ->where("user_id", $this->userId)
            ->select("id,status")
            ->first();

        if (!$ticket) {
            return $this->respond(["message" => lang("Message.message_49")], 404);
        }

        if ($ticket["status"] == 2) {
            return $this->respond(["message" => lang("Message.message_50")], 400);
        }

        $uid = new Uid();

        $comment_uid = $uid->create();
        $comments = new SupportCommentsModel();

        $comments->insert([
            "user_id"    => $this->userId,
            "message"    => esc($this->request->getJsonVar("message")),
            "estimation" => 0,
            "uid"        => $comment_uid,
            "ticket_id"  => $ticket["id"]
        ]);

        $tickets->update($ticket["id"], [
            "status" => 0
        ]);

        return $this->respond(["uid" => $comment_uid, "created" => date('d-m-Y H:i')], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for create new comment
     * @return array
     */
    private function create_validation_type(): array
    {
        return [
            "message" => [
                "label" => lang("Fields.field_79"),
                "rules" => "required|min_length[2]|max_length[1000]"
            ],
        ];
    }
}