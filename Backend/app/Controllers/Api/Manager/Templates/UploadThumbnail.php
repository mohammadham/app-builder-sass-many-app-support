<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class UploadThumbnail extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Upload template thumbnail
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('thumbnail');

        if (!$id) {
            return $this->respond(['status' => 'error', 'message' => 'Template ID is required'], 400);
        }

        if (!$file || !$file->isValid()) {
            return $this->respond(['status' => 'error', 'message' => 'Invalid file'], 400);
        }

        $templates = new TemplatesModel();
        $template = $templates->find($id);

        if (!$template) {
            return $this->respond(['status' => 'error', 'message' => 'Template not found'], 404);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->respond(['status' => 'error', 'message' => 'Invalid file type. Only images are allowed'], 400);
        }

        // Create upload directory if not exists
        $uploadPath = ROOTPATH . 'public_html/upload/templates/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Delete old thumbnail if exists
        if ($template['thumbnail']) {
            $oldFile = $uploadPath . $template['thumbnail'];
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }

        // Generate unique filename
        $newName = $template['uid'] . '_' . time() . '.' . $file->getExtension();
        
        // Move file
        if ($file->move($uploadPath, $newName)) {
            $result = $templates->update($id, ['thumbnail' => $newName]);

            if ($result) {
                return $this->respond([
                    'status' => 'success',
                    'filename' => $newName,
                    'url' => base_url('public/upload/templates/' . $newName)
                ], 200);
            }
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to upload thumbnail'], 500);
    }
}
