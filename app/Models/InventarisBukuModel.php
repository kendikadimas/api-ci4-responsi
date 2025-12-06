<?php
namespace App\Models;
use CodeIgniter\Model;

class InventarisBukuModel extends Model
{
    protected $table = 'inventaris_buku';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'judul', 'harga', 'jumlah', 'tanggal_masuk', 'volume', 'penulis', 'penerbit'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}