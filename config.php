<?php
$db_url  = 'pgsql:host=localhost;port=5432;dbname=karyawan';
$db_user = 'postgres';
$db_pass = 'root';

try {
    $koneksi = new PDO($db_url, $db_user, $db_pass);
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}
