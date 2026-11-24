<?php
require 'config.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $koneksi->prepare('SELECT * FROM karyawan WHERE id = ?');
$stmt->execute([$id]);
$karyawan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$karyawan) {
    header('Location: index.php?status=gagal');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $gaji    = trim($_POST['gaji'] ?? '');

    if ($nama === '' || $jabatan === '' || $email === '' || $gaji === '') {
        $errors[] = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    } elseif (!is_numeric($gaji) || $gaji < 0) {
        $errors[] = 'Gaji harus berupa angka.';
    }

    if (empty($errors)) {
        $stmt = $koneksi->prepare('UPDATE karyawan SET nama = ?, jabatan = ?, email = ?, gaji = ? WHERE id = ?');
        $stmt->execute([$nama, $jabatan, $email, $gaji, $id]);

        header('Location: index.php?status=sukses');
        exit;
    }

    $karyawan = ['nama' => $nama, 'jabatan' => $jabatan, 'email' => $email, 'gaji' => $gaji];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Karyawan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-lg mx-auto px-4 py-8">
  <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Karyawan</h2>

  <?php foreach ($errors as $error): ?>
    <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endforeach; ?>

  <div class="bg-white rounded-xl shadow p-6">
    <form method="post">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" name="nama"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               value="<?= htmlspecialchars($karyawan['nama']) ?>" required>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
        <input type="text" name="jabatan"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               value="<?= htmlspecialchars($karyawan['jabatan']) ?>" required>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="text" name="email"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               value="<?= htmlspecialchars($karyawan['email']) ?>" required>
      </div>
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Gaji</label>
        <input type="number" name="gaji" step="0.01" min="0"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               value="<?= htmlspecialchars($karyawan['gaji']) ?>" required>
      </div>
      <div class="flex gap-2">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Update</button>
        <a href="index.php"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">Kembali</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
