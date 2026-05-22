# Dokumentasi Deployment Aplikasi Website Absen

Panduan ini menjelaskan langkah-langkah untuk memindahkan dan melakukan deployment aplikasi website dari repositori GitHub ke server lokal (localhost) atau server produksi (seperti Hostinger).

## Prasyarat
Sebelum memulai, pastikan kode aplikasi Anda sudah diunggah (upload/push) ke repositori GitHub.

## Langkah-langkah Deployment

1. **Clone Repositori**
   Buka terminal (CMD atau PowerShell) lalu lakukan *clone* repositori aplikasi Anda:
   ```bash
   git clone https://github.com/Robbialbert87/website-absen.git
   ```

2. **Buka Direktori Proyek**
   Masuk ke dalam folder proyek yang baru saja di-*clone* untuk menjalankannya di localhost:
   ```bash
   cd website-absen
   ```

3. **Buka Proyek di Code Editor**
   Setelah folder tersedia, buka proyek menggunakan *code editor* pilihan Anda, seperti **Visual Studio Code (VS Code)** atau **Antigravity**.

4. **Install Dependensi dan Konfigurasi Environment**
   Jalankan perintah berikut pada terminal editor Anda untuk menginstal semua dependensi PHP dan JavaScript:
   ```bash
   composer install
   npm install
   ```
   Selanjutnya, siapkan file `.env` (jika belum ada, salin dari `.env.example`). Buka file `.env` lalu **sesuaikan konfigurasinya**. Pastikan untuk mengatur koneksi database (seperti `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) agar mengarah ke database server lokal atau Hostinger Anda.

5. **Generate Application Key**
   Jalankan perintah berikut untuk menghasilkan *Application Key* yang dibutuhkan oleh framework (Laravel):
   ```bash
   php artisan key:generate
   ```

6. **Build Aset Frontend**
   Terakhir, jalankan perintah berikut untuk melakukan kompilasi / *build* aset *frontend* (CSS dan JavaScript):
   ```bash
   npm run build
   ```

---
*Catatan: Setelah semua langkah di atas selesai, Anda dapat menjalankan aplikasi menggunakan server bawaan (seperti `php artisan serve` untuk localhost) atau memastikan root directory web server mengarah ke folder `public`.*
