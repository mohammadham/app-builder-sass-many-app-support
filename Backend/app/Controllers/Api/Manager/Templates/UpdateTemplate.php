<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class UpdateTemplate extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update template
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $id             = $this->request->getPost('id');
        $name_fa        = $this->request->getPost('name_fa');
        $name_en        = $this->request->getPost('name_en');
        $description_fa = $this->request->getPost('description_fa');
        $description_en = $this->request->getPost('description_en');
        $category       = $this->request->getPost('category');
        $tags           = $this->request->getPost('tags');
        $github_repo    = $this->request->getPost('github_repo');
        $github_branch  = $this->request->getPost('github_branch');
        $status         = $this->request->getPost('status');

        if (!$id) {
            return $this->respond(['status' => 'error', 'message' => 'Template ID is required'], 400);
        }

        $templates = new TemplatesModel();
        $template = $templates->find($id);

        if (!$template) {
            return $this->respond(['status' => 'error', 'message' => 'Template not found'], 404);
        }

        // Parse tags
        if (is_string($tags)) {
            $tagsArray = json_decode($tags, true);
        } else {
            $tagsArray = $tags;
        }

        $data = [];
        if ($name_fa !== null) $data['name_fa'] = $name_fa;
        if ($name_en !== null) $data['name_en'] = $name_en;
        if ($description_fa !== null) $data['description_fa'] = $description_fa;
        if ($description_en !== null) $data['description_en'] = $description_en;
        if ($category !== null) $data['category'] = $category;
        if ($tags !== null) $data['tags'] = json_encode($tagsArray ?? []);
        if ($github_repo !== null) $data['github_repo'] = $github_repo;
        if ($github_branch !== null) $data['github_branch'] = $github_branch;
        if ($status !== null) $data['status'] = $status;

        $result = $templates->update($id, $data);

        if ($result) {
            return $this->respond(['status' => 'success'], 200);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update template'], 500);
    }
}
