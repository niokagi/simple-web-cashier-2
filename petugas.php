<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM petugas";
$result = mysqli_query($conn, $query);
?>

<body>
    <div class="wrapper">
        <?php require './layouts/sidebar.php'; ?>
        <div class="main">
            <?php require './layouts/nav.php'; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h1 mb-3">Manajemen Petugas</h1>
                    <br>
                    <?php if ($_SESSION['level'] == 'admin') : ?>
                        <a href="tambah_petugas.php" class="btn btn-primary mb-3 px-4 py-2" style="background-color: #3B1E54; border: none;">Tambah Petugas</a>
                    <?php endif ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daftar Petugas</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Level</th>
                                                <th>Alamat</th>
                                                <th>No HP</th>
                                                <?php if ($_SESSION['level'] == 'admin') {
                                                    echo '<th>Aksi</th>';
                                                } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['id_petugas']); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_petugas']); ?></td>
                                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                                    <td><?= htmlspecialchars($row['level']); ?></td>
                                                    <td><?= htmlspecialchars($row['alamat']); ?></td>
                                                    <td><?= htmlspecialchars($row['no_hp']); ?></td>
                                                    <?php if ($_SESSION['level'] == 'admin') : ?>
                                                        <td>
                                                            <a href="edit_petugas.php?id=<?= $row['id_petugas']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                            <a href="hapus_petugas.php?id=<?= $row['id_petugas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus petugas ini?');">Hapus</a>
                                                        </td>
                                                    <?php endif; ?>
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