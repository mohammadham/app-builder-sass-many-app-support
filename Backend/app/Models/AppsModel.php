<?php namespace App\Models;

use CodeIgniter\Model;

class AppsModel extends Model
{
    protected $table         = 'apps';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'uid',
        'name',
        'status',
        'created_at',
        'updated_at',
        'user',
        'link',
        'color_theme',
        'color_title',
        'template',
        'app_id',
        'user_agent',
        'orientation',
        'loader',
        'pull_to_refresh',
        'loader_color',
        'gps',
        'gps_description',
        'language',
        'camera',
        'camera_description',
        'microphone',
        'microphone_description',
        'email',
        'display_title',
        'icon_color',
        'active_color',
        'deleted_at',
        'one_signal_id',
        'one_signal_rest'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'int';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}