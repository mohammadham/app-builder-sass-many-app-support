<?php namespace App\Controllers\Api\Manager\Templates;

use App\Controllers\PrivateController;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class CategoriesList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get list of unique categories from templates
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $templates = new TemplatesModel();

        $items = $templates
            ->select('category')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->groupBy('category')
            ->findAll();

        $categories = [];
        foreach ($items as $item) {
            if ($item['category']) {
                $categories[] = $item['category'];
            }
        }

        return $this->respond(['categories' => $categories], 200);
    }
}
