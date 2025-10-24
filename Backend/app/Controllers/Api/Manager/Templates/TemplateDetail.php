<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class TemplateDetail extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get template detail
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $id = $this->request->getGet('id');

        if (!$id) {
            return $this->respond(['status' => 'error', 'message' => 'Template ID is required'], 400);
        }

        $templates = new TemplatesModel();
        $template = $templates->find($id);

        if (!$template) {
            return $this->respond(['status' => 'error', 'message' => 'Template not found'], 404);
        }

        $data = [
            'id'             => (int) $template['id'],
            'uid'            => $template['uid'],
            'name_fa'        => $template['name_fa'],
            'name_en'        => $template['name_en'],
            'description_fa' => $template['description_fa'],
            'description_en' => $template['description_en'],
            'category'       => $template['category'],
            'tags'           => $template['tags'] ? json_decode($template['tags'], true) : [],
            'thumbnail'      => $template['thumbnail'],
            'github_repo'    => $template['github_repo'],
            'github_branch'  => $template['github_branch'],
            'config_schema'  => $template['config_schema'] ? json_decode($template['config_schema'], true) : null,
            'status'         => (int) $template['status'],
            'is_primary'     => (int) $template['is_primary'],
            'created_at'     => (int) $template['created_at'],
            'updated_at'     => (int) $template['updated_at'],
        ];

        return $this->respond(['template' => $data], 200);
    }
}
