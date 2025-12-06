# API Backend Inventaris Buku - Responsi 2 Mobile Paket 3

Backend API untuk aplikasi Flutter inventaris buku menggunakan CodeIgniter 4.

## Setup Database

1. Buka phpMyAdmin atau MySQL CLI
2. Buat database: `CREATE DATABASE responsi_mobile_buku;`
3. Jalankan migration: `php spark migrate`

## Endpoints API

Base URL: `http://localhost/responsi2_mobile_paket3_h1d023083/public/api`

### Authentication

#### 1. Registrasi User
- **URL**: `POST /api/registrasi`
- **Body**:
```json
{
  "username": "john_doe",
  "email": "john@example.com", 
  "password": "password123"
}
```
- **Response**:
```json
{
  "code": 200,
  "status": true,
  "data": "Registrasi Berhasil"
}
```

#### 2. Login User
- **URL**: `POST /api/login`
- **Body**:
```json
{
  "username": "john_doe",
  "password": "password123"
}
```
- **Response**:
```json
{
  "code": 200,
  "status": true,
  "data": {
    "token": "abc123xyz...",
    "user": {
      "id": 1,
      "username": "john_doe",
      "email": "john@example.com"
    }
  }
}
```

### Inventaris Buku (Protected - Requires Token)

**Header untuk semua request buku:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### 3. Tambah Buku Baru
- **URL**: `POST /api/buku/create`
- **Body**:
```json
{
  "judul": "Pemrograman Flutter",
  "harga": 150000,
  "jumlah": 25,
  "tanggal_masuk": "2024-12-06",
  "volume": 1,
  "penulis": "John Developer",
  "penerbit": "Tech Books"
}
```

#### 4. Lihat Semua Buku
- **URL**: `GET /api/buku/list`
- **Response**: Array of books

#### 5. Detail Buku
- **URL**: `GET /api/buku/detail/{id}`
- **Response**: Single book object

#### 6. Update Buku
- **URL**: `POST /api/buku/ubah/{id}`
- **Body**: Same as create (all fields)

#### 7. Hapus Buku
- **URL**: `POST /api/buku/hapus/{id}`

#### 8. Statistik Inventaris
- **URL**: `GET /api/buku/statistik`
- **Response**:
```json
{
  "code": 200,
  "status": true,
  "data": {
    "total_judul": 50,
    "total_buku": 1250,
    "total_nilai": 15000000
  }
}
```

## Database Schema

### Table: users
- id (int, primary key, auto increment)
- username (varchar 100, unique)
- email (varchar 100, unique)
- password (varchar 255, hashed)
- created_at (datetime)
- updated_at (datetime)

### Table: user_token
- id (int, primary key, auto increment)
- user_id (int, foreign key to users.id)
- auth_key (varchar 255)
- created_at (datetime)
- updated_at (datetime)

### Table: inventaris_buku
- id (int, primary key, auto increment)
- user_id (int, foreign key to users.id)
- judul (varchar 255)
- harga (int)
- jumlah (int)
- tanggal_masuk (date)
- volume (int)
- penulis (varchar 255)
- penerbit (varchar 255)
- created_at (datetime)
- updated_at (datetime)

## Error Responses

Error responses menggunakan format:
```json
{
  "code": 400/401/404/500,
  "status": false,
  "data": "Error message"
}
```

## Testing dengan Postman/Flutter

1. Registrasi user baru
2. Login untuk mendapatkan token
3. Gunakan token untuk operasi CRUD buku
4. Token disimpan dalam header Authorization dengan format: `Bearer {token}`

## CORS

API sudah dikonfigurasi dengan CORS untuk menerima request dari Flutter app. Headers yang diizinkan:
- Content-Type
- Authorization
- X-Requested-With
- Accept
- Origin

Methods yang diizinkan: GET, POST, PUT, DELETE, OPTIONS, PATCH