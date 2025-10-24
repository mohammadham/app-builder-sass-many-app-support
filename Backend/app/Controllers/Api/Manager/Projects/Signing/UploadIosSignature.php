<?php namespace App\Controllers\Api\Manager\Projects\Signing;

use App\Controllers\PrivateController;
use App\Libraries\Flangapp;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\SignsIosModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UploadIosSignature extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Upload ios signature
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->upload_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

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

        $cert = $this->request->getFile('api_key');
        $certName = $cert->getRandomName();
        $cert->move(WRITEPATH.'storage/ios/', $certName);

        $uid = new Uid();

        $sign_uid = $uid->create();

        $ios_signs = new SignsIosModel();

        $name = esc($this->request->getPost("name"));
        $issuer_id = esc($this->request->getPost("issuer_id"));
        $key_id = esc($this->request->getPost("key_id"));

        $flangapp = new Flangapp();

        $res = $flangapp->create_pem();

        if (!$res["event"]) {
            return $this->respond(["message" => $res["message"]], 400);
        }

        $ios_signs->insert([
            "name"      => $name,
            "issuer_id" => $issuer_id,
            "key_id"    => $key_id,
            "user_id"   => $app["user"],
            "app_id"    => $app["id"],
            "uid"       => $sign_uid,
            "file"      => $certName,
            "pub_file"  => $res["name"]
        ]);

        return $this->respond([
            "uid"     => $sign_uid,
            "name"    => $name,
            "info"    => $issuer_id." / ".$key_id,
            "type"    => "ios",
            "created" => date('d-m-Y H:i'),
            "unix"    => time()
        ], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules upload ios cert
     * @return array
     */
    private function upload_validation_type(): array
    {
        return [
            "name"      => [
                "label" => lang("Fields.field_61"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
            "issuer_id" => [
                "label" => lang("Fields.field_66"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
            "key_id"    => [
                "label" => lang("Fields.field_67"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
            'api_key'   => [
                'label' => lang("Fields.field_68"),
                'rules' => 'uploaded[api_key]|max_size[api_key,500]|ext_in[api_key,p8]'
            ],
        ];
    }
}