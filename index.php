<?php
require 'config.php';

$rows = $koneksi->query('SELECT * FROM karyawan ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Karyawan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-5xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Data Karyawan</h2>
    <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
      + Tambah Karyawan
    </a>
  </div>

  <?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] === 'sukses'): ?>
      <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
        Data berhasil disimpan.
      </div>
    <?php else: ?>
      <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
        Terjadi kesalahan.
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-16">No</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jabatan</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Gaji</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-36">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if (count($rows) === 0): ?>
          <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php $no = 1; foreach ($rows as $row): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-500"><?= $no++ ?></td>
            <td class="px-4 py-3 text-sm font-medium text-gray-800"><?= htmlspecialchars($row['nama']) ?></td>
            <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($row['jabatan']) ?></td>
            <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($row['email']) ?></td>
            <td class="px-4 py-3 text-sm text-gray-600">Rp <?= number_format((float) $row['gaji'], 0, ',', '.') ?></td>
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <a href="edit.php?id=<?= $row['id'] ?>"
                   class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg">Edit</a>
                <a href="hapus.php?id=<?= $row['id'] ?>"
                   class="bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg"
                   onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
