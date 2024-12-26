<?php
$host = 'localhost';
$dbname = 'task_manager';
$username = 'root';
$password = '';

try {
    $dsn = "mysql:host=$host;dbname=$dbname";
    $pdo = new PDO('mysql:host=localhost;dbname=task_manager', 'root', '');
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Koneksi Berhasil!";
} catch (PDOException $e) {
    echo "Gagal Terhubung: " . $e->getMessage();
}

$secret_key = "";