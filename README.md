# CRUD Karyawan — Versi Sederhana

Aplikasi CRUD (Create, Read, Update, Delete) data karyawan dalam bentuk paling sederhana: **PHP + HTML ditulis campur dalam satu file per halaman**, terhubung ke **PostgreSQL**, dan tampilan memakai **Tailwind CSS via CDN**.

Setiap halaman langsung menjalankan query database di bagian atas file, lalu merender HTML di bawahnya — tidak ada pemisahan API/frontend seperti di versi `karyawan-fullstack/`.

**Fitur:**

- Daftar, tambah, edit, dan hapus data karyawan
- Validasi server-side (kolom wajib, format email, gaji angka ≥ 0) + atribut `required` di form
- Format gaji otomatis menjadi rupiah, misal `Rp 8.500.000`
- Pesan sukses/gagal lewat parameter URL `?status=`
- Aman dari SQL injection (prepared statements) dan XSS (`htmlspecialchars`)

---

## Arsitektur & Alur Kerja

```
Browser
   │  form POST / klik link
   ▼
Halaman PHP (index / tambah / edit / hapus)
   │  PDO + prepared statement
   ▼
PostgreSQL (database "karyawan", tabel "karyawan")
   │
   ▼
Halaman PHP merender HTML + data → redirect / tampil
```

Alur tiap operasi dari sisi pengguna:

| Operasi | Alur |
|---|---|
| **Read** | `index.php` query semua data langsung saat halaman dimuat → baris tabel dirender PHP |
| **Create** | `tambah.php` diisi dan disubmit → POST ke `tambah.php` itu sendiri → validasi → `INSERT` → redirect `index.php?status=sukses` |
| **Update** | Tombol Edit → `edit.php?id=1` → form terisi dari query `SELECT` → disubmit → `UPDATE` → redirect ke index |
| **Delete** | Tombol Hapus → dialog `confirm()` JavaScript → link ke `hapus.php?id=1` → `DELETE` → redirect ke index |

Komunikasi antar halaman memakai **redirect berparameter**: setelah operasi berhasil/gagal, halaman dialihkan ke `index.php?status=sukses` atau `index.php?status=gagal`, dan index menampilkan alert sesuai isinya.

---

## Struktur Folder

```
karyawan/
├── config.php       → Koneksi database PostgreSQL
├── index.php        → Halaman daftar karyawan (Read)
├── tambah.php       → Halaman form tambah (Create)
├── edit.php         → Halaman form edit (Update)
├── hapus.php        → Script hapus data (Delete, tanpa tampilan)
├── database.sql     → Script pembuatan tabel + data contoh
└── README.md        → Dokumentasi ini
```

---

## Penjelasan Setiap File

### `config.php`
Menyimpan konfigurasi koneksi database dan membuat objek PDO `$koneksi` yang dipakai semua halaman (`require 'config.php'` di bagian atas tiap file).

| Variabel | Isi | Keterangan |
|---|---|---|
| `$db_url` | `pgsql:host=localhost;port=5432;dbname=karyawan` | Alamat (DSN) server PostgreSQL |
| `$db_user` | `postgres` | Username database |
| `$db_pass` | `root` | Password database — **sesuaikan dengan punya Anda** |

Dua hal yang diatur di sini:

- `PDO::ATTR_ERRMODE = PDO::ERRMODE_EXCEPTION` — error SQL dilempar sebagai exception, bukan diam-diam.
- Jika koneksi gagal, skrip berhenti (`die()`) dan menampilkan pesan error koneksi.

### `index.php` — Read
Halaman utama. Query `SELECT * FROM karyawan ORDER BY id DESC` (data terbaru di atas) dijalankan di bagian atas file, lalu hasilnya dirender ke tabel.

Bagian penting:

- **Alert status** — membaca `?status=` dari URL: `sukses` menampilkan alert hijau "Data berhasil disimpan.", nilai lain menampilkan alert merah "Terjadi kesalahan."
- **Tabel 6 kolom** (No, Nama, Jabatan, Email, Gaji, Aksi). Jika data kosong, tampil satu baris "Belum ada data."
- **Format gaji** — `number_format($gaji, 0, ',', '.')` dengan prefiks `Rp `, menghasilkan misal `Rp 8.500.000`.
- **Keamanan output** — semua data dari database dibungkus `htmlspecialchars()` saat dicetak, mencegah **XSS**.
- **Tombol aksi** — Edit menuju `edit.php?id=`, Hapus menuju `hapus.php?id=` dengan `onclick="return confirm('Yakin hapus data ini?')"` agar meminta konfirmasi dulu.

### `tambah.php` — Create
Satu file dengan dua peran: **memproses form** (jika method `POST`) dan **menampilkan form** (jika GET / validasi gagal).

Proses di bagian atas file:

1. Ambil semua `$_POST`, `trim` tiap nilai.
2. Validasi berantai (`elseif`) — berhenti di error pertama yang ditemukan:
   - Semua kolom wajib diisi → *"Semua kolom wajib diisi."*
   - Email diperiksa dengan `FILTER_VALIDATE_EMAIL` → *"Format email tidak valid."*
   - Gaji harus `is_numeric` dan ≥ 0 → *"Gaji harus berupa angka."*
3. Jika lolos, `INSERT` dengan **prepared statement**, lalu redirect ke `index.php?status=sukses` dan `exit`.

Fitur **sticky form**: jika validasi gagal, form tetap tampil dengan nilai yang sebelumnya diketik pengguna (`value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"`), jadi input tidak hilang. Error ditampilkan sebagai alert merah di atas form.

Input form: semua `required`; kolom gaji bertipe `number` dengan `step="0.01" min="0"`. Ada tombol **Kembali** menuju index.

### `edit.php` — Update
Gabungan `tambah.php` + pengambilan data lama. Alurnya:

1. Baca `?id=` dari URL (di-cast ke integer), query karyawan tersebut.
2. Jika data tidak ditemukan → langsung redirect `index.php?status=gagal`.
3. Jika form disubmit (`POST`), jalankan validasi yang sama dengan `tambah.php`, lalu `UPDATE ... WHERE id = ?` (prepared statement) dan redirect `index.php?status=sukses`.
4. Jika validasi gagal, form tetap ditampilkan — datanya diganti dengan nilai yang baru diketik pengguna (sticky form), dan error ditampilkan di atasnya.
5. Jika belum disubmit, form diisi dari hasil query database.

Judul tombol submit adalah **Update** (bukan Simpan).

### `hapus.php` — Delete
Satu-satunya halaman tanpa tampilan HTML — hanya memproses lalu redirect:

1. Baca `?id=` (di-cast ke integer).
2. Jika `id > 0`: `DELETE FROM karyawan WHERE id = ?` (prepared statement) → redirect `index.php?status=sukses`.
3. Jika tidak: redirect `index.php?status=gagal`.

Dialog konfirmasi hapus tidak ada di file ini — ditangani `onclick="return confirm(...)"` di `index.php`.

### `database.sql`
Membuat tabel `karyawan` beserta 3 data contoh. **File ini tidak membuat database-nya** — database `karyawan` harus dibuat dulu (misal lewat pgAdmin: klik kanan *Databases → Create*), baru `database.sql` dijalankan di Query Tool database tersebut.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `SERIAL PRIMARY KEY` | Auto increment |
| `nama` | `VARCHAR(100) NOT NULL` | Nama karyawan |
| `jabatan` | `VARCHAR(50) NOT NULL` | Jabatan |
| `email` | `VARCHAR(100) NOT NULL` | Email |
| `gaji` | `NUMERIC(12,2) NOT NULL` | Gaji per bulan |
| `created_at` | `TIMESTAMP` | Terisi otomatis `CURRENT_TIMESTAMP` saat data dibuat |

### Tampilan — Tailwind CSS
Semua styling memakai **Tailwind via CDN** (`<script src="https://cdn.tailwindcss.com">`) sehingga **butuh koneksi internet** agar tampilan terbentuk. Kelas yang dipakai antara lain: kartu `bg-white rounded-xl shadow`, tabel dengan header `bg-gray-50` dan pemisah `divide-y`, tombol biru (`bg-blue-600`) untuk Simpan/Tambah, oranye (`bg-amber-500`) untuk Edit, merah (`bg-red-600`) untuk Hapus, alert hijau/merah, serta efek focus biru (`focus:ring-2`) pada input.

---

## Cara Menjalankan

1. Pastikan **Apache** (XAMPP) dan **PostgreSQL** sudah berjalan, dan ekstensi **`pdo_pgsql`** PHP sudah aktif (cek/hapus `;` pada `extension=pdo_pgsql` dan `extension=pgsql` di `php.ini`, lalu restart Apache).
2. Buat database `karyawan` di pgAdmin (klik kanan *Databases → Create → Database*, nama: `karyawan`).
3. Query Tool di database `karyawan` → jalankan `database.sql`.
4. Sesuaikan password PostgreSQL di `config.php` bila perlu.
5. Buka `http://localhost/web-dasar/karyawan/index.php`

## Perbandingan dengan Versi Fullstack (`karyawan-fullstack/`)

| Aspek | `karyawan/` (versi ini) | `karyawan-fullstack/` |
|---|---|---|
| Backend | PHP + HTML campur per halaman | API JSON terpusat di satu file |
| Frontend | HTML di dalam PHP, Tailwind CDN | HTML/CSS/JS murni terpisah |
| Komunikasi | Form POST biasa + redirect | `fetch()` + JSON |
| Format balikan | Halaman HTML | JSON + HTTP status code |
| Validasi | `elseif` — satu pesan error per submit | Bisa menampilkan beberapa error sekaligus |
| Keamanan | Prepared statement + `htmlspecialchars` | Sama, ditambah CORS untuk pemisahan domain |
