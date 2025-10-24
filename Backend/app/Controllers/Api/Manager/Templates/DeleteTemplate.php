<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class DeleteTemplate extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Delete template
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->respond(['status' => 'error', 'message' => 'Template ID is required'], 400);
        }

        $templates = new TemplatesModel();
        $template = $templates->find($id);

        if (!$template) {
            return $this->respond(['status' => 'error', 'message' => 'Template not found'], 404);
        }

        // Check if template is being used
        $apps = new AppsModel();
        $usedCount = $apps->where('template_id', $id)->countAllResults();

        if ($usedCount > 0) {
            return $this->respond([
                'status' => 'error',
                'message' => "Cannot delete template. It is being used by {$usedCount} app(s)"
            ], 400);
        }

        // Don't allow deleting primary template
        if ($template['is_primary'] == 1) {
            return $this->respond(['status' => 'error', 'message' => 'Cannot delete primary template'], 400);
        }

        $result = $templates->delete($id);

        if ($result) {
            return $this->respond(['status' => 'success'], 200);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete template'], 500);
    }
}
