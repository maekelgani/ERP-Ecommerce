# Cara berkolaborasi
Panduan melakukan kolaborasi menggunakan git dari clone hingga push ke repositori. Ikuti langkah demi langkah.

---

## Langkah 0 – Persiapan Awal

1. Buat **folder baru** di komputer kamu.  
2. **Buka terminal** di dalam folder tersebut (pastikan path-nya benar).  
3. Pastikan sudah menginstal:
   - **Git**
   - **Node.js** dan **npm**  
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
perintah ini akan menginstall paket yang diperlukan untuk menjalankan proyek (google OAuth API)
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
Note: default repo biasanya main, namun kasus di repo ini menggunakan master

---

## Langkah 4 – Edit File Proyek

Langkah ini silahkan mulai mengedit atau menambahkan file, bebas. Tetap hati hati jika menghapus file penting.
Karna proyek ini menggunakan Tailwind CSS, jalankan perintah berikut agar perubahan CSS langsung diterapkan:

```bash
npx tailwindcss -i ./src/input.css -o ./src/output.css --watch
```
Biarkan terminal tetap terbuka selama kamu mengedit agar Tailwind terus memantau perubahan.

---

## Langkah 5 – Simpan Perubahan (Commit)

Setelah selesai melakukan perubahan:
1. Tambahkan semua file yang diubah dengan perintah:
   ```bash
    git add .
   ```
2. Buat commit dengan pesan yang menjelaskan perubahan:
   ```bash
   git commit -m "deskripsi singkat perubahan"
   ```
Misalnya seperti `git commit -m "menambahkan module pembayaran"`

---

## Langkah 6 – Push ke Repository

Kirim (push) perubahanmu ke branch yang sudah kamu buat:
```bash
git push origin <nama-branch-kamu>
```

---
