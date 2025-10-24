<?php namespace App\Controllers\Api\Manager\Projects;

use App\Controllers\PrivateController;
use App\Libraries\Common;
use App\Models\AppsModel;
use CodeIgniter\HTTP\ResponseInterface;

define("LIMIT", 20);

class ProjectsUserList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get projects list by user ID
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $offset = (int) $this->request->getGet("offset");
        $user_id = (int) $this->request->getGet("user_id");

        $apps = new AppsModel();

        $items = $apps
            ->where("user", $user_id)
            ->where("deleted_at", 0)
            ->orderBy("id", "DESC")
            ->findAll(LIMIT, $offset);

        $common = new Common();

        $list = [];

        foreach ($items as $item) {
            $list[] = [
                "created" => date('d-m-Y H:i', $item['created_at']),
                "name"    => $item["name"],
                "uid"     => $item["uid"],
                "link"    => $item["link"],
                "status"  => (bool) $item["status"],
                "icon"    => $common->get_icon($item["uid"])
            ];
        }

        $total = $apps
            ->where("user", $user_id)
            ->where("deleted_at", 0)
            ->countAllResults();

        return $this->respond(["list" => $list, "total" => $total], 200);
    }
}