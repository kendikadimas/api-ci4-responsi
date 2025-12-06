<?php

namespace App\Controllers;

use App\Models\InventarisBukuModel;
use App\Models\MLogin;
use Config\Services;

class InventarisBukuController extends RestfulController
{
    private function authenticateUser()
    {
        $request = Services::request();
        
        // Try different ways to get the token
        $authKey = $request->getHeaderLine('Authorization');
        if (!$authKey) {
            $authKey = $request->getServer('HTTP_AUTHORIZATION');
        }
        if (!$authKey) {
            $authKey = $request->getVar('token'); // Fallback to form data
        }
        
        if (!$authKey) {
            return null;
        }
        
        // Remove "Bearer " prefix if present  
        $token = str_replace('Bearer ', '', $authKey);
        $token = trim($token);
        
        $loginModel = new MLogin();
        $tokenData = $loginModel->where('auth_key', $token)->first();
        
        if (!$tokenData) {
            return null;
        }
        
        return $tokenData['user_id'];
    }

    public function create()
    {
        $userId = $this->authenticateUser();
        if (!$userId) {
            return $this->responseHasil(401, false, "Token tidak valid");
        }

        $request = Services::request();
        
        $data = [
            'user_id' => $userId,
            'judul' => $request->getVar('judul'),
            'harga' => $request->getVar('harga'),
            'jumlah' => $request->getVar('jumlah'),
            'tanggal_masuk' => $request->getVar('tanggal_masuk'),
            'volume' => $request->getVar('volume'),
            'penulis' => $request->getVar('penulis'),
            'penerbit' => $request->getVar('penerbit')
        ];
        
        $model = new InventarisBukuModel();
        $model->insert($data);
        $buku = $model->find($model->getInsertID());
        
        return $this->responseHasil(200, true, $buku);
    }

    public function list()
    {
        $userId = $this->authenticateUser();
        if (!$userId) {
            return $this->responseHasil(401, false, "Token tidak valid");
        }

        $model = new InventarisBukuModel();
        $buku = $model->where('user_id', $userId)->findAll();
        
        return $this->responseHasil(200, true, $buku);
    }

    public function detail($id)
    {
        $userId = $this->authenticateUser();
        if (!$userId) {
            return $this->responseHasil(401, false, "Token tidak valid");
        }

        $model = new InventarisBukuModel();
        $buku = $model->where(['id' => $id, 'user_id' => $userId])->first();
        
        if (!$buku) {
            return $this->responseHasil(404, false, "Buku tidak ditemukan");
        }
        
        return $this->responseHasil(200, true, $buku);
    }

    public function ubah($id)
    {
        $userId = $this->authenticateUser();
        if (!$userId) {
            return $this->responseHasil(401, false, "Token tidak valid");
        }

        $request = Services::request();
        
        $data = [
            'judul' => $request->getVar('judul'),
            'harga' => $request->getVar('harga'),
            'jumlah' => $request->getVar('jumlah'),
            'tanggal_masuk' => $request->getVar('tanggal_masuk'),
            'volume' => $request->getVar('volume'),
            'penulis' => $request->getVar('penulis'),
            'penerbit' => $request->getVar('penerbit')
        ];
        
        $model = new InventarisBukuModel();
        $buku = $model->where(['id' => $id, 'user_id' => $userId])->first();
        
        if (!$buku) {
            return $this->responseHasil(404, false, "Buku tidak ditemukan");
        }
        
        $model->update($id, $data);
        $updatedBuku = $model->find($id);
        
        return $this->responseHasil(200, true, $updatedBuku);
    }

    public function hapus($id)
    {
        $userId = $this->authenticateUser();
        if (!$userId) {
            return $this->responseHasil(401, false, "Token tidak valid");
        }

        $model = new InventarisBukuModel();
        $buku = $model->where(['id' => $id, 'user_id' => $userId])->first();
        
        if (!$buku) {
            return $this->responseHasil(404, false, "Buku tidak ditemukan");
        }
        
        $result = $model->delete($id);
        
        return $this->responseHasil(200, true, $result);
    }

    public function statistik()
    {
        $userId = $this->authenticateUser();
        if (!$userId) {
            return $this->responseHasil(401, false, "Token tidak valid");
        }

        try {
            $model = new InventarisBukuModel();
            
            // Get statistics with separate queries to avoid issues
            $totalJudul = $model->where('user_id', $userId)->countAllResults();
            
            // Reset model for next query
            $model = new InventarisBukuModel();
            $totalJumlah = 0;
            $totalNilai = 0;
            
            $books = $model->where('user_id', $userId)->findAll();
            
            foreach ($books as $book) {
                $totalJumlah += $book['jumlah'];
                $totalNilai += ($book['harga'] * $book['jumlah']);
            }
            
            $stats = [
                'total_judul' => $totalJudul,
                'total_buku' => $totalJumlah,
                'total_nilai' => $totalNilai
            ];
            
            return $this->responseHasil(200, true, $stats);
            
        } catch (\Exception $e) {
            return $this->responseHasil(500, false, "Error: " . $e->getMessage());
        }
    }
}