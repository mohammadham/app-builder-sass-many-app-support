<?php namespace App\Controllers\Api\Manager\Users;

use App\Controllers\PrivateController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

define("LIMIT", 20);

class UsersList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get customers
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $offset = (int) $this->request->getGet("offset");
        $search = esc($this->request->getGet("search"));

        $users = new UsersModel();

        $users_list = $users
            ->like("email", $search)
            ->orderBy("id", "DESC")
            ->findAll(LIMIT, $offset);

        $count = $users
            ->like("email", $search)
            ->orderBy("id", "DESC")
            ->countAllResults();

        $items = [];

        foreach ($users_list as $item) {
            $items[] = [
                "id"      => (int) $item["id"],
                "email"   => $item["email"],
                "status"  => (int) $item["status"],
                "created" => date('d-m-Y H:i', $item['created_at']),
                "admin"   => (int) $item["admin"],
                "deleted" => (bool) $item["deleted_at"]
            ];
        }

        return $this->respond(["list" => $items, "total" => $count], 200);
    }

}