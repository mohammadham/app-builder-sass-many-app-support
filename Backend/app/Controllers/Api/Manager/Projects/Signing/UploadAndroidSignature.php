<?php namespace App\Controllers\Api\Manager\Projects\Signing;

use App\Controllers\PrivateController;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\SignsAndroidModel;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UploadAndroidSignature extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Upload android signature
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->upload_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        helper('filesystem');

        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("id,user")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        $keystore = $this->request->getFile("keystore");
        $name = $keystore->getRandomName();
        $keystore->move(WRITEPATH.'storage/android/', $name);

        $uid = new Uid();

        $encrypter = Services::encrypter();

        $sign_uid = $uid->create();

        $android_signs = new SignsAndroidModel();

        $name_key = esc($this->request->getPost("name"));
        $alias = esc($this->request->getPost("alias"));

        $android_signs->insert([
            "name"              => $name_key,
            "alias"             => $alias,
            "user_id"           => $app["user"],
            "app_id"            => $app["id"],
            "uid"               => $sign_uid,
            "keystore_password" => $encrypter->encrypt(
                esc($this->request->getPost("keystore_password"))
            ),
            "key_password"      => $encrypter->encrypt(
                esc($this->request->getPost("key_password"))
            ),
            "file"              => $name
        ]);

        return $this->respond([
            "uid"     => $sign_uid,
            "name"    => $name_key,
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
     * Get validation rules upload keystore
     * @return array
     */
    private function upload_validation_type(): array
    {
        return [
            "name"              => [
                "label" => lang("Fields.field_61"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
            "alias"             => [
                "label" => lang("Fields.field_62"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
            "keystore_password" => [
                "label" => lang("Fields.field_63"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
            "key_password"      => [
                "label" => lang("Fields.field_64"),
                "rules" => "required|min_length[2]|max_length[100]"],
            "keystore"          => [
                'label' => lang("Fields.field_65"),
                'rules' => 'uploaded[keystore]|max_size[keystore,500]|ext_in[keystore,jks]'
            ],
        ];
    }
}