<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Libraries\Uid;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class CreateTemplate extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create new template
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $name_fa        = $this->request->getPost('name_fa');
        $name_en        = $this->request->getPost('name_en');
        $description_fa = $this->request->getPost('description_fa');
        $description_en = $this->request->getPost('description_en');
        $category       = $this->request->getPost('category');
        $tags           = $this->request->getPost('tags'); // JSON string or array
        $github_repo    = $this->request->getPost('github_repo');
        $github_branch  = $this->request->getPost('github_branch');
        $status         = $this->request->getPost('status');

        // Validation
        if (!$name_fa || !$name_en) {
            return $this->respond(['status' => 'error', 'message' => 'Name is required in both languages'], 400);
        }

        $templates = new TemplatesModel();
        $uid = new Uid();

        // Parse tags
        if (is_string($tags)) {
            $tagsArray = json_decode($tags, true);
        } else {
            $tagsArray = $tags;
        }

        $data = [
            'uid'            => $uid->generate(),
            'name_fa'        => $name_fa,
            'name_en'        => $name_en,
            'description_fa' => $description_fa ?? '',
            'description_en' => $description_en ?? '',
            'category'       => $category ?? '',
            'tags'           => json_encode($tagsArray ?? []),
            'github_repo'    => $github_repo ?? '',
            'github_branch'  => $github_branch ?? 'main',
            'config_schema'  => json_encode(['fields' => []]),
            'status'         => $status ?? 1,
            'is_primary'     => 0,
        ];

        $result = $templates->insert($data);

        if ($result) {
            return $this->respond(['status' => 'success', 'id' => $result], 200);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to create template'], 500);
    }
}
