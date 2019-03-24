* jika belum pernah melakukan git sebelumnya atau belum menginstall git
 - sudo apt install git -y
 - git config --global user.name "DimasPradana"
 - git config --global user.email "d******b**k@gmail.com"
 - git config --global core.editor "vim"
 - git config --list

 * login dan buat repository terlebih dahulu di github.com
 - masuk ke root folder yang akan di upload ke github
 - git init "samakan-dengan-nama-repositori-di-github" -> tanpa tanda
   kutip, atau
 - git init .
 - setelah perintah tersebut akan menambah folder dengan nama ".git"
   yang berguna untuk menyimpan history dari git(version control)

 * menyimpan revisi / log untuk di upload di github.com, ada 3 state
   dalam git:
 - 1 Modified
   (kondisi dimana revisi atau perubahan sudah
   dilakukan, tetapi belum ditandai dan belum disimpan di version
   control git, ditandai warna merah di git status)
 - 2 Staged
   (kondisi dimana revisi sudah ditandai, namun belum disimpan di
   version control git, merubah kondisi dari modified ke staged yaitu
   menggunakan perintah "git add 'namafile.ekstensi'" atau "git add ."
   untuk semua file dan folder)
 - 3 Commited
   (kondisi dimana revisi sudah disimpan di version
   control, perintah untuk mengubah kondisi file dari staged ke commited
   ada "git commit -m 'komentar nya apa'")
 - perintah "git status" untuk mengecek kondisi version control

 * mengupload / push file dari folder git ke github
 - buka halaman github repository yang sudah dibuat sebelumnya dan
   copy-kan remote repository url(dengan akhiran .git) nya misal
   "https://github.com/DimasPradana/pendaftaran.git"
 - buka terminal dan arahkan ke root folder project
 - git remote add origin "remote repository url" misal
   "git remote add origin https://github.com/DimasPradana/pendaftaran.git"
 - git remote -v (verifikasi remote baru di github)
 - git push origin master (upload file dan folder di komputer ke storage
   github.com)
