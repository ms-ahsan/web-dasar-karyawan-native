<?php
require 'config.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $koneksi->prepare('DELETE FROM karyawan WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: index.php?status=sukses');
    exit;
}

header('Location: index.php?status=gagal');
exit;
