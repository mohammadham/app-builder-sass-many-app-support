<?php namespace App\Controllers\Api\Customer\Support;

use App\Controllers\PrivateController;
use App\Libraries\Uid;
use App\Models\SupportCommentsModel;
use App\Models\SupportTicketsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class CreateTicket extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create new ticket
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->create_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $uid = new Uid();

        $ticket_uid = $uid->create();

        $tickets = new SupportTicketsModel();

        $ticket_id = $tickets->insert([
            "title"   => esc($this->request->getJsonVar("title")),
            "user_id" => $this->userId,
            "status"  => 0,
            "uid"     => $ticket_uid
        ]);

        $comments = new SupportCommentsModel();

        $comments->insert([
            "user_id"    => $this->userId,
            "message"    => esc($this->request->getJsonVar("message")),
            "estimation" => 0,
            "uid"        => $uid->create(),
            "ticket_id"  => $ticket_id
        ]);

        return $this->respond(["uid" => $ticket_uid], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for create new ticket
     * @return array
     */
    private function create_validation_type(): array
    {
        return [
            "title"   => [
                "label" => lang("Fields.field_78"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "message" => [
                "label" => lang("Fields.field_79"),
                "rules" => "required|min_length[2]|max_length[1000]"
            ],
        ];
    }
}