<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM penjualan ORDER BY id_penjualan DESC";
$result = mysqli_query($conn, $query);
?>

<body>
    <div class="wrapper">
        <?php require './layouts/sidebar.php'; ?>

        <div class="main">
            <?php require './layouts/nav.php'; ?>

            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3">Manajemen Penjualan</h1>
                    <br>

                    <?php
                    // bisa nambahin kalau rolenya kasir
                    if ($_SESSION['level'] == 'kasir') {
                        echo '<a href="tambah_penjualan.php" class="btn btn-primary mb-3 px-4 py-2" style="background-color: #3B1E54; border: none;">Tambah Penjualan</a>';
                    }
                    ?>
                    <div class="row">
                        <div class="col-12 d-flex">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daftar Penjualan</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tanggal</th>
                                                <th>Nama Pelanggan</th>
                                                <th>Total Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['id_penjualan']); ?></td>
                                                    <td><?= htmlspecialchars($row['tanggal_penjualan']); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                                                    <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <!-- Tombol Detail -->
                                                        <a href="detail_penjualan.php?id=<?= $row['id_penjualan']; ?>"
                                                            class="btn btn-info btn-sm">Detail</a>

                                                        <!-- Tombol Cetak Nota -->
                                                        <a href="cetak_nota.php?id=<?= $row['id_penjualan']; ?>"
                                                            class="btn btn-success btn-sm"
                                                            target="_blank">Cetak</a>

                                                        <!-- Tombol Hapus -->
                                                        <a href="hapus_penjualan.php?id=<?= $row['id_penjualan']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Yakin ingin menghapus penjualan ini?');">
                                                            Hapus
                                                        </a>
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