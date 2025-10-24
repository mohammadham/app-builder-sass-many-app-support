<?php namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $table         = 'transactions';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'uid',
        'amount',
        'created_at',
        'updated_at',
        'status',
        'method_id',
        'subscribe_external_id',
        'external_uid'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $dateFormat    = 'int';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}