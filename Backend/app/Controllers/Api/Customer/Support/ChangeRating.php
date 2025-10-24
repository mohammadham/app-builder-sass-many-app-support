<?php namespace App\Controllers\Api\Customer\Support;

use App\Controllers\PrivateController;
use App\Models\SupportCommentsModel;
use App\Models\SupportTicketsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class ChangeRating extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Change comment rating
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $uid = esc($this->request->getGet("uid"));

        $comments = new SupportCommentsModel();

        $comment = $comments
            ->where("uid", $uid)
            ->select("uid,ticket_id,id")
            ->first();

        if (!$comment) {
            return $this->respond(["message" => lang("Message.message_51")], 404);
        }

        $tickets = new SupportTicketsModel();

        $ticket = $tickets
            ->where("id", $comment["ticket_id"])
            ->where("user_id", $this->userId)
            ->select("id")
            ->first();

        if (!$ticket) {
            return $this->respond(["message" => lang("Message.message_49")], 404);
        }

        $comments->update($comment["id"], [
            "estimation" => (int) $this->request->getJsonVar("estimation")
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for set comment rate
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "estimation" => [
                "label" => lang("Fields.field_80"),
                "rules" => "required|in_list[1,2,3,4,5]"
            ],
        ];
    }
}