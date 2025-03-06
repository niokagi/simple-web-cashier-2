<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM pelanggan";
$result = mysqli_query($conn, $query);
?>

<body>
    <div class="wrapper">
        <?php require './layouts/sidebar.php'; ?>

        <div class="main" style="background-color: #EEEEEE;">
            <?php require './layouts/nav.php'; ?>

            <main class="content" style="background-color: #EEEEEE;">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3">Manajemen Pelanggan</h1>
                    <br>
                    <?php
                    // bisa nambahin kalau rolenya kasir
                    if ($_SESSION['level'] == 'kasir') {
                        echo '<a href="tambah_pelanggan.php" class="btn btn-primary mb-3 px-4 py-2" style="background-color: #3B1E54; border: none;">Tambah Pelanggan</a>';
                    }
                    ?>
                    <div class="row">
                        <div class="col-12 d-flex">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daftar Pelanggan</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover my-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Alamat</th>
                                                <th>Nomor telepon</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['id_pelanggan']); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                                                    <td><?= htmlspecialchars($row['alamat']); ?></td>
                                                    <td><?= htmlspecialchars($row['nomor_telepon']); ?></td>
                                                    <td>
                                                        <a href="edit_pelanggan.php?id=<?= $row['id_pelanggan']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <a href="hapus_pelanggan.php?id=<?= $row['id_pelanggan']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pelanggan ini?');">Hapus</a>
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