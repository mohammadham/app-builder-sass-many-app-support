<?php namespace App\Controllers\Api\Manager\Projects\Localization;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\LocalsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UploadOfflineImage extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Upload offline  custom image
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("id,uid")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        $locals = new LocalsModel();

        $detail = $locals
            ->where("app_id", $app["id"])
            ->select("id")
            ->first();

        if ( !is_dir( ROOTPATH.'public_html/upload/info/'.$app['uid'] ) ) {
            mkdir(ROOTPATH.'public_html/upload/info/'.$app['uid'], 0777, true);
        }

        $image = $this->request->getFile('offline_image');
        $name = $image->getRandomName();
        $image->move(ROOTPATH.'public_html/upload/info/'.$app['uid'], $name);

        $locals->update($detail["id"], [
            "offline_image" => $name
        ]);

        return $this->respond(["uri" => base_url('upload/info/'.$app['uid'].'/'.$name)], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for upload offline image
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            'offline_image' => [
                'label' => lang("Fields.field_30"),
                'rules' => 'uploaded[offline_image]|max_size[offline_image,500]|ext_in[offline_image,png]|max_dims[offline_image,1200,1200]'
            ],
        ];
    }
}