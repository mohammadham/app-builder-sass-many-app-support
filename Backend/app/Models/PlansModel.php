<?php namespace App\Models;

use CodeIgniter\Model;

class PlansModel extends Model
{
    protected $table         = 'plans';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'count',
        'price',
        'status',
        'deleted_at',
        'api_id',
        'save'
    ];
    protected $returnType    = 'array';
}