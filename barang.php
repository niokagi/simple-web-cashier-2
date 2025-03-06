<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM barang");
$stmt->execute();
$result = $stmt->get_result();
?>

<body>
    <div class="wrapper">
        <?php require './layouts/sidebar.php'; ?>

        <div class="main">
            <?php require './layouts/nav.php'; ?>

            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3">Manajemen Barang</h1>
                    <br>

                    <a href="tambah_barang.php" class="btn btn-primary mb-3 px-4 py-2" style="background-color: #3B1E54; border: none;">Tambah Barang</a>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daftar Barang</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Gambar</th>
                                                <th>Nama Barang</th>
                                                <th>Stok</th>
                                                <th>Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($barang = $result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($barang['id_barang']); ?></td>
                                                    <td>
                                                        <?php if (!empty($barang['gambar']) && file_exists("gambar_produk/" . $barang['gambar'])): ?>
                                                            <img src="gambar_produk/<?= htmlspecialchars($barang['gambar']); ?>" alt="<?= htmlspecialchars($barang['nama_barang']); ?>" style="max-width: 100px;">
                                                        <?php else: ?>
                                                            Tidak ada gambar
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($barang['nama_barang']); ?></td>
                                                    <td><?= htmlspecialchars($barang['stok']); ?></td>
                                                    <td>Rp <?= number_format($barang['harga'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <a href="edit_barang.php?id=<?= $barang['id_barang']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <a href="hapus_barang.php?id=<?= $barang['id_barang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus barang ini?');">Hapus</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php require 'layouts/footer.php'; ?>