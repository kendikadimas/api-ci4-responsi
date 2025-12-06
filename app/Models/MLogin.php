<?php
namespace App\Models;
use CodeIgniter\Model;

class MLogin extends Model
{
    protected $table = 'user_token';
    protected $allowedFields = ['user_id', 'auth_key'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}