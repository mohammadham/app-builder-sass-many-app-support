<?php namespace App\Controllers\Api\Customer\Projects\Signing;

use App\Controllers\PrivateController;
use App\Libraries\Flangapp;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\SignsAndroidModel;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class CreateAndroidSignature extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create android signature
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->create_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        helper('text');

        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("user", $this->userId)
            ->where("deleted_at", 0)
            ->select("id")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        $name = esc($this->request->getJsonVar("name"));

        $alias = random_string("alpha", 10);
        $password = random_string('alpha', 16);

        $flangapp = new Flangapp();

        $res = $flangapp->create_keystore($alias, $password);

        if (!$res["event"]) {
            return $this->respond(["message" => $res["message"]], 400);
        }

        $uid = new Uid();

        $sign_uid = $uid->create();

        $encrypter = Services::encrypter();

        $android_signs = new SignsAndroidModel();

        $android_signs->insert([
            "name"              => $name,
            "alias"             => $alias,
            "user_id"           => $this->userId,
            "app_id"            => $app["id"],
            "uid"               => $sign_uid,
            "keystore_password" => $encrypter->encrypt($password),
            "key_password"      => $encrypter->encrypt($password),
            "file"              => $res["name"]
        ]);

        return $this->respond([
            "uid"     => $sign_uid,
            "name"    => $name,
            "info"    => $alias,
            "type"    => "android",
            "created" => date('d-m-Y H:i'),
            "unix"    => time()
        ], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules create keystore
     * @return array
     */
    private function create_validation_type(): array
    {
        return [
            "name" => [
                "label" => lang("Fields.field_61"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
        ];
    }
}