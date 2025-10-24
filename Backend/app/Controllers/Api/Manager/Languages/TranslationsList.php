<?php

namespace App\Controllers\Api\Manager\Languages;

use App\Controllers\PrivateController;
use App\Models\TranslationsModel;

/**
 * Controller for getting translations list
 */
class TranslationsList extends PrivateController
{
    protected $modelName = 'App\Models\TranslationsModel';

    public function index()
    {
        $this->admin();

        $langCode = $this->request->getGet('lang_code') ?? 'en';
        $section = $this->request->getGet('section') ?? 'frontend';
        $search = $this->request->getGet('search') ?? '';

        $model = new TranslationsModel();
        $builder = $model->where('lang_code', $langCode)
                         ->where('section', $section);

        if (!empty($search)) {
            $builder->groupStart()
                   ->like('translation_key', $search)
                   ->orLike('translation_value', $search)
                   ->groupEnd();
        }

        $translations = $builder->orderBy('translation_key', 'ASC')->findAll();

        return $this->respond([
            'status' => 200,
            'error' => null,
            'messages' => [
                'success' => 'Translations retrieved successfully'
            ],
            'data' => [
                'translations' => $translations,
                'lang_code' => $langCode,
                'section' => $section
            ]
        ]);
    }
}
