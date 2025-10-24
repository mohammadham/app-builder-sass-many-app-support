<?php namespace App\Models;

use CodeIgniter\Model;

class TemplatesModel extends Model
{
    protected $table         = 'templates';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'uid',
        'name_fa',
        'name_en',
        'description_fa',
        'description_en',
        'category',
        'tags',
        'thumbnail',
        'github_repo',
        'github_branch',
        'config_schema',
        'status',
        'is_primary',
        'created_at',
        'updated_at'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'int';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active templates
     * @return array
     */
    public function getActiveTemplates(): array
    {
        return $this->where('status', 1)->findAll();
    }

    /**
     * Get templates by category
     * @param string $category
     * @return array
     */
    public function getByCategory(string $category): array
    {
        return $this->where('status', 1)
                    ->where('category', $category)
                    ->findAll();
    }

    /**
     * Get primary template (for backward compatibility)
     * @return array|null
     */
    public function getPrimaryTemplate(): ?array
    {
        return $this->where('is_primary', 1)->first();
    }

    /**
     * Search templates by tags
     * @param string $tag
     * @return array
     */
    public function searchByTag(string $tag): array
    {
        return $this->where('status', 1)
                    ->like('tags', $tag)
                    ->findAll();
    }
}
