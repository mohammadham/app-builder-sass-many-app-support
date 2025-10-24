<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class TemplatesList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get templates list
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $templates = new TemplatesModel();

        $items = $templates
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $list = [];

        foreach ($items as $item) {
            $list[] = [
                'id'             => (int) $item['id'],
                'uid'            => $item['uid'],
                'name_fa'        => $item['name_fa'],
                'name_en'        => $item['name_en'],
                'description_fa' => $item['description_fa'],
                'description_en' => $item['description_en'],
                'category'       => $item['category'],
                'tags'           => $item['tags'] ? json_decode($item['tags'], true) : [],
                'thumbnail'      => $item['thumbnail'],
                'github_repo'    => $item['github_repo'],
                'github_branch'  => $item['github_branch'],
                'status'         => (int) $item['status'],
                'is_primary'     => (int) $item['is_primary'],
                'created_at'     => (int) $item['created_at'],
                'updated_at'     => (int) $item['updated_at'],
            ];
        }

        return $this->respond(['list' => $list], 200);
    }
}
