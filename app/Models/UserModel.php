<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['full_name', 'email', 'gender', 'interests', 'resume', 'country', 'created_at'];

    public function get_users($limit, $offset)
    {
        return $this->orderBy('id', 'ASC')->findAll($limit, $offset);  
    }

    public function get_total_users()
    {
        return $this->countAll();
    }
}

