<?php

namespace App\Controllers\Api\Manager\Languages;

use App\Controllers\PrivateController;
use App\Models\LanguagesModel;

/**
 * Controller for managing languages list in admin panel
 */
class LanguagesList extends PrivateController
{
    protected $modelName = 'App\Models\LanguagesModel';

    public function index()
    {
        $this->admin();

        $model = new LanguagesModel();
        $languages = $model->findAll();

        return $this->respond([
            'status' => 200,
            'error' => null,
            'messages' => [
                'success' => 'Languages list retrieved successfully'
            ],
            'data' => [
                'languages' => $languages
            ]
        ]);
    }
}
