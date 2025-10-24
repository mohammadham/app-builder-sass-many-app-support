<?php

namespace App\Controllers\Api\Manager\Languages;

use App\Controllers\PrivateController;
use App\Models\TranslationsModel;

/**
 * Controller for bulk updating translations
 */
class BulkUpdateTranslations extends PrivateController
{
    protected $modelName = 'App\Models\TranslationsModel';

    public function index()
    {
        $this->admin();

        $data = $this->request->getJSON(true);
        
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            return $this->respond([
                'status' => 400,
                'error' => true,
                'messages' => ['error' => 'Invalid translations data']
            ]);
        }

        $model = new TranslationsModel();
        $successCount = 0;
        $failCount = 0;

        foreach ($data['translations'] as $translation) {
            if (!isset($translation['lang_code']) || 
                !isset($translation['translation_key']) || 
                !isset($translation['translation_value'])) {
                $failCount++;
                continue;
            }

            $section = $translation['section'] ?? 'frontend';
            $result = $model->upsertTranslation(
                $translation['lang_code'],
                $translation['translation_key'],
                $translation['translation_value'],
                $section
            );

            if ($result) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return $this->respond([
            'status' => 200,
            'error' => null,
            'messages' => [
                'success' => "Updated {$successCount} translations successfully"
            ],
            'data' => [
                'success_count' => $successCount,
                'fail_count' => $failCount
            ]
        ]);
    }
}
