# API Manajemen Tugas dengan Autentikasi Pengguna Aman  
Sebuah API RESTful yang ringan dan handal untuk mengelola tugas dengan autentikasi pengguna menggunakan JWT. Proyek ini dirancang untuk developer yang membutuhkan solusi backend yang efisien dan modular.  

---

## Fitur  
- **Autentikasi Pengguna**: Registrasi dan login pengguna dengan password yang di-hash dan autentikasi berbasis token (JWT).  
- **Manajemen Tugas**: Membuat, membaca, memperbarui, dan menghapus tugas dengan fitur filter, pagination, dan sorting.  
- **Validasi**: Validasi menyeluruh untuk menjaga integritas data.  
- **Keamanan Terjamin**: Data sensitif dikelola menggunakan praktik terbaik, termasuk hashing password dan token yang memiliki masa berlaku.  

---

## Instalasi  

1. **Clone Repository**  
   ```bash
   git clone https://github.com/username-anda/api-manajemen-tugas.git
   cd api-manajemen-tugas
   ```

2. **Setup Database**  
   - Buat database MySQL baru.  
   - Import file `schema.sql` yang disediakan untuk membuat tabel yang diperlukan.  

3. **Konfigurasi Lingkungan**  
   - Ubah nama file `config/db.example.php` menjadi `config/db.php`.  
   - Sesuaikan kredensial database (`host`, `username`, `password`, dan `dbname`).  
   - Tambahkan secret key JWT di `config/jwt.php`.  

4. **Install Dependency**  
   Install dependency PHP menggunakan Composer:  
   ```bash
   composer install
   ```

5. **Jalankan Server**  
   Jika menggunakan server bawaan PHP:  
   ```bash
   php -S localhost:8000
   ```

---

## Endpoints  

### **Autentikasi**  
- **Registrasi Pengguna Baru**  
  `POST /api/register.php`  
  ```json
  {
      "username": "username_anda",
      "password": "password_anda"
  }
  ```

- **Login Pengguna**  
  `POST /api/login.php`  
  ```json
  {
      "username": "username_anda",
      "password": "password_anda"
  }
  ```

---

### **Manajemen Tugas**  
- **Buat Tugas Baru**  
  `POST /api/tasks.php`  
  ```json
  {
      "name": "Nama Tugas",
      "description": "Deskripsi Tugas",
      "due_date": "2024-12-31"
  }
  ```

- **Ambil Semua Tugas (dengan filter)**  
  `GET /api/tasks.php`  
  Contoh Query Parameters:  
  `?status=completed&page=1&limit=10&sort_by=due_date&order=asc`  

- **Perbarui Tugas**  
  `PUT /api/tasks.php?id=1`  
  ```json
  {
      "name": "Nama Tugas Baru",
      "description": "Deskripsi Baru",
      "due_date": "2024-12-31"
  }
  ```

- **Hapus Tugas**  
  `DELETE /api/tasks.php?id=1`  

---

## Kontribusi  

Kontribusi sangat disambut baik! Berikut cara untuk berkontribusi:  
1. Fork proyek ini.  
2. Buat branch baru untuk fitur Anda:  
   ```bash
   git checkout -b fitur/fitur-baru-anda
   ```
3. Commit perubahan Anda:  
   ```bash
   git commit -m "Menambahkan fitur baru"
   ```
4. Push branch Anda:  
   ```bash
   git push origin fitur/fitur-baru-anda
   ```
5. Ajukan Pull Request.  

---

## Lisensi  
Proyek ini dilisensikan di bawah MIT License. Anda bebas menggunakan, memodifikasi, dan mendistribusikannya sesuai dengan ketentuan lisensi.  

---

## Penghargaan  
Terima kasih kepada komunitas open-source dan kontributor yang telah membantu mempermudah pengembangan setiap harinya. Mari membangun sesuatu yang luar biasa bersama-sama! 🚀  
```
