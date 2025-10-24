<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class UpdateFormSchema extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update template form schema
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $id = $this->request->getPost('id');
        $schema = $this->request->getPost('schema'); // JSON string or array

        if (!$id) {
            return $this->respond(['status' => 'error', 'message' => 'Template ID is required'], 400);
        }

        if (!$schema) {
            return $this->respond(['status' => 'error', 'message' => 'Schema is required'], 400);
        }

        $templates = new TemplatesModel();
        $template = $templates->find($id);

        if (!$template) {
            return $this->respond(['status' => 'error', 'message' => 'Template not found'], 404);
        }

        // Parse schema
        if (is_string($schema)) {
            $schemaArray = json_decode($schema, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->respond(['status' => 'error', 'message' => 'Invalid JSON schema'], 400);
            }
        } else {
            $schemaArray = $schema;
        }

        // Validate schema structure
        if (!isset($schemaArray['fields']) || !is_array($schemaArray['fields'])) {
            return $this->respond(['status' => 'error', 'message' => 'Schema must contain fields array'], 400);
        }

        $result = $templates->update($id, [
            'config_schema' => json_encode($schemaArray)
        ]);

        if ($result) {
            return $this->respond(['status' => 'success'], 200);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update schema'], 500);
    }
}
