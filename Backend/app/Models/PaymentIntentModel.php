<?php namespace App\Models;

use CodeIgniter\Model;

class PaymentIntentModel extends Model
{
    protected $table         = 'payment_intent';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'subscribe_id',
        'method_id',
        'is_pending',
        'created_at',
        'updated_at',
        'payment_token',
        'planned_at'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'int';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}