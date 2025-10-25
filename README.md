<br># NOTE!!
<br>
<br>## == Cara berkolaborasi di GitHub ========
<br>
<br>### 0. SetupAwal
<br>buat folder baru, lalu buka terminal di vscode atau terminal bawaan
<br>
<br>### 1. Lakukan Clone repositori
<br>`git clone <url repositori>`
<br>Perintah "git clone" digunakan untuk mengunduh code yang ada pada repository.
<br>
<br>### 2. Lakukan Checkout
<br>`git checkout master` 

<br>perintah untuk pindah ke branch master, namun karna di repo cuman ada 1 brach bisa langsung longkap ke step 3
<br>
<br>### 3. Buat Branch baru
<br>`git checkout -b <nama-branch>`

<br>perintah ini akan membuatkan branch baru, dengan flag -b yang artinya _buat branch baru lalu langsung pindah ke sana._
<br>
<br>### 4. Ngedit file nya
<br>otak atik aja, tambah file baru
<br>
<br>### 5. simpan perubahan
<br>Setiap perubahan lakuin commit dengan command:
<br>`git add .`
<br>`git commit -m "menambahkan halaman login`
<br>
<br>perintah tsb membuat snapshot perubahan
<br>
<br>### 6. kirim ke github
<br>Agar yang lain bisa melihat dan di gabungkan 
`git push origin (nama branch)`
<br>
<br>### 7. buat Pull Request (PR)

<br>Masuk ke halaman repo → akan muncul tombol “Compare & pull request”.
Klik itu untuk mengajukan agar perubahanmu digabung ke branch utama `(master)`

<br>=================================
<br>jangan lupa build ulang tailwindnya:
<br>
<br> _npx tailwindcss -i ./src/input.css -o ./src/output.css --watch_




