<?php namespace App\Models;

use CodeIgniter\Model;

class SubscribesModel extends Model
{
    protected $table         = 'subscribes';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'subscribe_external_id',
        'customer_external_id',
        'plan_id',
        'user_id',
        'expires_at',
        'created_at',
        'updated_at',
        'updated_at',
        'is_disable',
        'app_id',
        'price',
        'uid',
        'is_active',
        'method_id'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'int';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}