<?php namespace App\Controllers\Api\Manager\Projects\Builds;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\BuildsModel;
use CodeIgniter\HTTP\ResponseInterface;

class BuildsList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get builds versions
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("id")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        $builds = new BuildsModel();

        $items = $builds
            ->where("app_id", $app["id"])
            ->orderBy("id", "DESC")
            ->findAll();

        $list = [];
        foreach ($items as $build) {
            $list[] = [
                "uid"      => $build["uid"],
                "platform" => $build["platform"],
                "status"   => (int) $build["status"],
                "version"  => $build["version"],
                "publish"  => (int) $build["publish"],
                "created"  => date('d-m-Y H:i', $build['created_at']),
                "format"   => $build["platform"] == "ios" ? "ipa" : $build["format"],
                "fail"     => (bool) $build["fail"],
                "message"  => $build["message"],
                "build_id" => $build["build_id"],
            ];
        }

        return $this->respond(["list" => $list], 200);
    }

}