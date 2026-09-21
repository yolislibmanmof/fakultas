<?php
// JALANKAN SEKALI SAJA, LALU HAPUS FILE INI!
$pdo = new PDO('mysql:host=localhost;dbname=fakultas', 'root', '');
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$hash]);
echo "SUKSES! Password admin sudah di-set menjadi: admin123";