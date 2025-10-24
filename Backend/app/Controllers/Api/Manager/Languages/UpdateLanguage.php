<?php

namespace App\Controllers\Api\Manager\Languages;

use App\Controllers\PrivateController;
use App\Models\LanguagesModel;

/**
 * Controller for updating language settings
 */
class UpdateLanguage extends PrivateController
{
    protected $modelName = 'App\Models\LanguagesModel';

    public function index()
    {
        $this->admin();

        $data = $this->request->getJSON(true);
        
        // Validation
        $rules = [
            'id' => 'required|integer',
            'status' => 'permit_empty|integer|in_list[0,1]',
            'is_default' => 'permit_empty|integer|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 400,
                'error' => true,
                'messages' => $this->validator->getErrors()
            ]);
        }

        $model = new LanguagesModel();
        $language = $model->find($data['id']);

        if (!$language) {
            return $this->respond([
                'status' => 404,
                'error' => true,
                'messages' => ['error' => 'Language not found']
            ]);
        }

        $updateData = [];
        
        // Update status
        if (isset($data['status'])) {
            $updateData['status'] = (int)$data['status'];
        }

        // Handle default language
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            // Remove default from all other languages
            $model->update(null, ['is_default' => 0]);
            $updateData['is_default'] = 1;
        }

        if (!empty($updateData)) {
            $model->update($data['id'], $updateData);
        }

        return $this->respond([
            'status' => 200,
            'error' => null,
            'messages' => [
                'success' => 'Language updated successfully'
            ]
        ]);
    }
}
