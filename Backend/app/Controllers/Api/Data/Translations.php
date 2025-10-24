<?php

namespace App\Controllers\Api\Data;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TranslationsModel;
use App\Models\LanguagesModel;

/**
 * Public API for getting translations
 */
class Translations extends ResourceController
{
    protected $format = 'json';

    /**
     * Get translations for specific language
     * GET /public/data/translations?lang=fa&section=frontend
     */
    public function index()
    {
        $langCode = $this->request->getGet('lang') ?? 'en';
        $section = $this->request->getGet('section') ?? 'frontend';

        // Check if language is active
        $langModel = new LanguagesModel();
        $language = $langModel->where('code', $langCode)->where('status', 1)->first();

        if (!$language) {
            // Fallback to default language
            $language = $langModel->getDefaultLanguage();
            $langCode = $language['code'];
        }

        $model = new TranslationsModel();
        $translations = $model->getAsKeyValue($langCode, $section);

        return $this->respond([
            'status' => 200,
            'error' => null,
            'data' => [
                'translations' => $translations,
                'lang_code' => $langCode,
                'direction' => $language['direction'] ?? 'ltr'
            ]
        ]);
    }

    /**
     * Get all active languages
     * GET /public/data/languages
     */
    public function languages()
    {
        $model = new LanguagesModel();
        $languages = $model->getActiveLanguages();

        return $this->respond([
            'status' => 200,
            'error' => null,
            'data' => [
                'languages' => $languages
            ]
        ]);
    }
}
