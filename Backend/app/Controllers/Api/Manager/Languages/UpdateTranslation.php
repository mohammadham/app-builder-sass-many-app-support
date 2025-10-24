<?php

namespace App\Controllers\Api\Manager\Languages;

use App\Controllers\PrivateController;
use App\Models\TranslationsModel;

/**
 * Controller for updating translations
 */
class UpdateTranslation extends PrivateController
{
    protected $modelName = 'App\Models\TranslationsModel';

    public function index()
    {
        $this->admin();

        $data = $this->request->getJSON(true);
        
        // Validation
        $rules = [
            'lang_code' => 'required|max_length[10]',
            'translation_key' => 'required|max_length[255]',
            'translation_value' => 'required',
            'section' => 'permit_empty|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 400,
                'error' => true,
                'messages' => $this->validator->getErrors()
            ]);
        }

        $model = new TranslationsModel();
        
        $section = $data['section'] ?? 'frontend';
        $result = $model->upsertTranslation(
            $data['lang_code'],
            $data['translation_key'],
            $data['translation_value'],
            $section
        );

        if ($result) {
            return $this->respond([
                'status' => 200,
                'error' => null,
                'messages' => [
                    'success' => 'Translation updated successfully'
                ]
            ]);
        } else {
            return $this->respond([
                'status' => 500,
                'error' => true,
                'messages' => ['error' => 'Failed to update translation']
            ]);
        }
    }
}
