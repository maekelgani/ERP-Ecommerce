# Cara berkolaborasi

Panduan melakukan kolaborasi menggunakan git dari clone hingga push ke repositori. Ikuti langkah demi langkah.

---

## Langkah 0 – Persiapan Awal

1. Buat **folder baru** di komputer kamu.
2. **Buka terminal** di dalam folder tersebut (pastikan path-nya benar).
3. Pastikan sudah menginstal:

   * **Git**
   * **Node.js** (disarankan versi LTS / >= 18)
   * **npm**
     (dibutuhkan agar Tailwind CSS bisa dijalankan)

---

## Langkah 1 – Clone Repositori

Gunakan perintah berikut untuk menyalin seluruh isi repositori ke folder lokal:

```bash
git clone <url-repo> .
```

---

## Langkah 2 – Install Dependencies

Setelah proses clone selesai, jalankan perintah:

```bash
npm install
```

Perintah ini akan menginstal semua paket yang diperlukan untuk menjalankan proyek (termasuk konfigurasi Tailwind CSS).

```bash
composer require google/apiclient
```

Perintah ini akan menginstal paket yang diperlukan untuk menjalankan proyek (Google OAuth API).

---

## Langkah 3 – Setup Branch

Sebelum mulai mengedit file, buat branch baru agar perubahanmu tidak langsung mengubah branch utama (main atau master):

```bash
git checkout -b <nama-branch-baru>
```

Atau, jika branch sudah ada dan kamu ingin memperbarui isinya:

```bash
git pull origin master
```

> **Catatan:** Default branch biasanya `main`, namun pada repo ini menggunakan `master`.

---

## Langkah 4 – Edit File Proyek

Silakan mulai mengedit atau menambahkan file sesuai kebutuhan. Tetap berhati-hati jika menghapus file penting.

Karena proyek ini menggunakan **Tailwind CSS**, jalankan perintah berikut agar perubahan CSS langsung diterapkan:

```bash
npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
```

Biarkan terminal tetap terbuka selama kamu mengedit agar Tailwind terus memantau perubahan.

---

## ⚠️ Troubleshooting Tailwind CSS (PENTING)

Jika muncul error seperti:

```text
npm error could not determine executable to run
```

Itu berarti proyek menggunakan **Tailwind CSS versi 4**, di mana **CLI tidak ter-install otomatis**.

### ✅ Solusi

Jalankan perintah berikut **sekali saja** di root project:

```bash
npm install -D @tailwindcss/cli
```

Setelah itu, jalankan kembali Tailwind:

```bash
npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
```

### 🔒 Alternatif (lebih stabil di Windows)

Tambahkan script berikut ke `package.json`:

```json
{
  "scripts": {
    "dev:css": "tailwindcss -i ./src/input.css -o ./src/output.css --watch"
  }
}
```

Lalu jalankan:

```bash
npm run dev:css
```

---

## Langkah 5 – Simpan Perubahan (Commit)

Setelah selesai melakukan perubahan:

1. Tambahkan semua file yang diubah:

   ```bash
   git add .
   ```
2. Buat commit dengan pesan yang menjelaskan perubahan:

   ```bash
   git commit -m "deskripsi singkat perubahan"
   ```

Contoh:

```bash
git commit -m "menambahkan modul pembayaran"
```

---

## Langkah 6 – Push ke Repository

Kirim (push) perubahanmu ke branch yang sudah kamu buat:

```bash
git push origin <nama-branch-kamu>
```

---

Jika mengalami error lain di luar panduan ini, silakan diskusikan terlebih dahulu dengan tim sebelum melakukan perubahan lebih lanjut.
