<?php namespace App\Controllers\Api\Data;

use App\Controllers\BaseController;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class Templates extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get active templates for users
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $category = $this->request->getGet('category');
        $tag = $this->request->getGet('tag');

        $templates = new TemplatesModel();

        $query = $templates->where('status', 1);

        if ($category) {
            $query->where('category', $category);
        }

        if ($tag) {
            $query->like('tags', $tag);
        }

        $items = $query->orderBy('is_primary', 'DESC')
                       ->orderBy('created_at', 'DESC')
                       ->findAll();

        $list = [];

        foreach ($items as $item) {
            $list[] = [
                'id'             => (int) $item['id'],
                'uid'            => $item['uid'],
                'name_fa'        => $item['name_fa'],
                'name_en'        => $item['name_en'],
                'description_fa' => $item['description_fa'],
                'description_en' => $item['description_en'],
                'category'       => $item['category'],
                'tags'           => $item['tags'] ? json_decode($item['tags'], true) : [],
                'thumbnail'      => $item['thumbnail'] 
                    ? base_url('public/upload/templates/' . $item['thumbnail']) 
                    : null,
                'is_primary'     => (int) $item['is_primary'],
            ];
        }

        return $this->respond(['templates' => $list], 200);
    }

    /**
     * Get template detail by UID
     * @param string $uid
     * @return ResponseInterface
     */
    public function detail(string $uid): ResponseInterface
    {
        $templates = new TemplatesModel();
        $template = $templates->where('uid', $uid)
                              ->where('status', 1)
                              ->first();

        if (!$template) {
            return $this->respond(['status' => 'error', 'message' => 'Template not found'], 404);
        }

        $data = [
            'id'             => (int) $template['id'],
            'uid'            => $template['uid'],
            'name_fa'        => $template['name_fa'],
            'name_en'        => $template['name_en'],
            'description_fa' => $template['description_fa'],
            'description_en' => $template['description_en'],
            'category'       => $template['category'],
            'tags'           => $template['tags'] ? json_decode($template['tags'], true) : [],
            'thumbnail'      => $template['thumbnail'] 
                ? base_url('public/upload/templates/' . $template['thumbnail']) 
                : null,
            'config_schema'  => $template['config_schema'] 
                ? json_decode($template['config_schema'], true) 
                : null,
            'is_primary'     => (int) $template['is_primary'],
        ];

        return $this->respond(['template' => $data], 200);
    }

    /**
     * Get categories
     * @return ResponseInterface
     */
    public function categories(): ResponseInterface
    {
        $templates = new TemplatesModel();

        $items = $templates
            ->select('category, COUNT(*) as count')
            ->where('status', 1)
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->groupBy('category')
            ->findAll();

        $categories = [];
        foreach ($items as $item) {
            $categories[] = [
                'name' => $item['category'],
                'count' => (int) $item['count']
            ];
        }

        return $this->respond(['categories' => $categories], 200);
    }
}
