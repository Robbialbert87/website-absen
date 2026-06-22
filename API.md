# API SIJAGA

Base URL: `https://sijaga.rsudkotajambi.id/api`

---

## Daftar Endpoint

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/pegawai` | Ambil data pegawai (digunakan untuk sinkronisasi oleh aplikasi HAMORA) |

---

## GET /api/pegawai

Ambil data pegawai beserta role pengguna. Data diambil dari tabel `pegawai` yang digabungkan dengan `users` dan `model_has_roles` (Spatie).

### Authentication

Opsional. Endpoint ini publik dapat diakses, namun bisa diamankan dengan mengirimkan `X-API-Key` pada header. Jika key tidak cocok dengan konfigurasi server, maka akan ditolak.

| Header | Tipe | Wajib | Deskripsi |
|--------|------|-------|-----------|
| `X-API-Key` | string | Tidak | API key untuk keamanan tambahan (dikonfigurasi di `.env` melalui `API_KEY_PEGAWAI`) |

### Query Parameters

| Parameter | Tipe | Wajib | Default | Deskripsi |
|-----------|------|-------|---------|-----------|
| `page` | integer | Tidak | `1` | Halaman data (pagination) |

### Response Sukses (200 OK)

```json
{
  "data": [
    {
      "nip": "198706072020121003",
      "nama": "Robbi Albert",
      "jabatan": "IT",
      "role_info": {
        "roles": ["admin", "super_admin"]
      }
    },
    {
      "nip": "199411302024212022",
      "nama": "dr. DWI NOVIA PUTRI",
      "jabatan": "ASN",
      "role_info": {
        "roles": ["user"]
      }
    }
  ],
  "pagination": {
    "last_page": 7
  }
}
```

#### Field Description

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `data[].nip` | string | Nomor Induk Pegawai |
| `data[].nama` | string | Nama lengkap pegawai |
| `data[].jabatan` | string | Jabatan pegawai (contoh: ASN, IT, PNS) |
| `data[].role_info.roles` | array of strings | Daftar role Spatie yang dimiliki user terkait (kosong jika pegawai belum memiliki akun user) |
| `pagination.last_page` | integer | Total halaman yang tersedia |

### Error Response

#### 401 — API Key tidak valid

Hanya terjadi jika `X-API-Key` dikirimkan tetapi nilainya tidak cocok dengan `API_KEY_PEGAWAI` di `.env`.

```json
{
  "message": "Unauthorized"
}
```

### Contoh Penggunaan

#### cURL
```bash
# Tanpa API Key
curl https://sijaga.rsudkotajambi.id/api/pegawai

# Dengan API Key
curl -H "X-API-Key: rahasia123" https://sijaga.rsudkotajambi.id/api/pegawai

# Halaman tertentu
curl "https://sijaga.rsudkotajambi.id/api/pegawai?page=2"
```

#### PHP (Guzzle) — seperti digunakan di HAMORA
```php
$response = Http::timeout(10)
    ->withoutVerifying()
    ->get('https://sijaga.rsudkotajambi.id/api/pegawai', [
        'page' => $page
    ]);

$pegawaiList = $response->json('data');
$pagination = $response->json('pagination');
$lastPage = $pagination['last_page'] ?? 1;
```

### Catatan

- Data hanya menampilkan pegawai dengan `status_aktif = true`
- Pagination: 100 data per halaman
- Role diambil dari relasi `Pegawai → User → Spatie Roles`
- Pegawai yang belum memiliki akun User tetap dikirim dengan `role_info.roles: []`
