<?php
namespace App\Controllers;
use App\Models\UserModel;
use App\Models\MLogin;
use Config\Services;

class AuthController extends RestfulController
{
    public function registrasi()
    {
        $request = Services::request();
        $data = [
            'username' => $request->getVar('username'),
            'email' => $request->getVar('email'),
            'password' => password_hash($request->getVar('password'), PASSWORD_DEFAULT)
        ];
        
        $model = new UserModel();
        
        // Check if email already exists
        $existingUser = $model->where('email', $data['email'])->first();
        if ($existingUser) {
            return $this->responseHasil(400, false, "Email sudah terdaftar");
        }
        
        // Check if username already exists
        $existingUsername = $model->where('username', $data['username'])->first();
        if ($existingUsername) {
            return $this->responseHasil(400, false, "Username sudah terdaftar");
        }
        
        $model->save($data);
        return $this->responseHasil(200, true, "Registrasi Berhasil");
    }

    public function login()
    {
        $request = Services::request();
        $username = $request->getVar('username');
        $password = $request->getVar('password');
        
        $model = new UserModel();
        $user = $model->where(['username' => $username])
                     ->orWhere(['email' => $username])
                     ->first();
                     
        if (!$user) {
            return $this->responseHasil(400, false, "Username/Email tidak ditemukan");
        }
        
        if (!password_verify($password, $user['password'])) {
            return $this->responseHasil(400, false, "Password tidak valid");
        }
        
        $login = new MLogin();
        $auth_key = $this->RandomString();
        $login->save([
            'user_id' => $user['id'],
            'auth_key' => $auth_key
        ]);
        
        $data = [
            'token' => $auth_key,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
            ]
        ];
        
        return $this->responseHasil(200, true, $data);
    }
    
    private function RandomString($length = 100)
    {
        $karakter = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang_karakter = strlen($karakter);
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= $karakter[rand(0, $panjang_karakter - 1)];
        }
        return $str;
    }
    
    public function testToken()
    {
        $request = Services::request();
        $authKey = $request->getHeaderLine('Authorization');
        if (!$authKey) {
            $authKey = $request->getVar('token');
        }
        
        return $this->responseHasil(200, true, [
            'received_token' => $authKey,
            'message' => 'Token received successfully'
        ]);
    }
}