<?php namespace App\Controllers\Api\Manager\Projects\Newsletter;

use App\Controllers\PrivateController;
use App\Libraries\Common;
use App\Libraries\OneSignal;
use App\Models\AppsModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CreateNotification extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create new notification
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $validationRules = $this->create_validation_type();

        if (empty($_FILES['image']['name'])) {
            unset($validationRules['image']);
        }

        $validation = Services::validation();
        $validation->setRules($validationRules);

        if (!$validation->run($_POST)) {
            return $this->respond(["message" => $validation->getErrors()], 400);
        }

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("id,one_signal_id,one_signal_rest,uid")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        if (!$app["one_signal_id"]) {
            return $this->respond(["message" => lang("Message.message_81")], 400);
        }

        if (!$app["one_signal_rest"]) {
            return $this->respond(["message" => lang("Message.message_81")], 400);
        }

        $image = "";

        $uploadedImage = $this->request->getFile('image');
        if ($uploadedImage) {
            $nameImage = $uploadedImage->getRandomName();
            $uploadedImage->move(ROOTPATH.'public_html/upload/newsletter', $nameImage);
            $image = base_url('upload/newsletter/'.$nameImage);
        }

        $common = new Common();

        $onesignal = new OneSignal();

        $data = [
            "content"     => esc($this->request->getPost("message")),
            "heading"     => esc($this->request->getPost("title")),
            "large_icon"  => $common->get_icon($app["uid"]),
            "big_picture" => $image
        ];

        $res = $onesignal->create_newsletter(
            $data,
            $app["one_signal_rest"],
            $app["one_signal_id"]
        );

        if (!$res["event"]) {
            return $this->respond(["message" => $res["message"]], 400);
        }

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for create push
     * @return array
     */
    private function create_validation_type(): array
    {
        return [
            "title"    => [
                "label" => lang("Fields.field_78"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "message"  => [
                "label" => lang("Fields.field_79"),
                "rules" => "required|max_length[300]|min_length[3]"
            ],
            "image" => [
                "label" => lang("Fields.field_147"),
                "rules" => "max_size[image,500]|ext_in[image,png,jpg]"
            ],
        ];
    }
}