<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $nama_barang = trim($_POST['nama_barang']);
    $harga = trim($_POST['harga']);
    $stok = trim($_POST['stok']);

    if (empty($nama_barang) || empty($harga) || empty($stok)) {
        $error = "Semua field wajib diisi!";
    } else {
        if (!is_numeric($harga) || !is_numeric($stok)) {
            $error = "Harga dan Stok harus berupa angka!";
        } else {
            $harga = floatval($harga);
            $stok = intval($stok);
            $query = "INSERT INTO barang (nama_barang, harga, stok) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sdi", $nama_barang, $harga, $stok);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Barang berhasil ditambahkan!');
                        window.location.href = 'barang.php';
                      </script>";
                exit();
            } else {
                $error = "Gagal menambahkan barang: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<body>
    <div class="wrapper">
        <?php require './layouts/sidebar.php'; ?>
        <div class="main">
            <?php require './layouts/nav.php'; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <h1 class="h3 mb-3">Tambah Barang</h1>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Form Tambah Barang</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($error)) : ?>
                                        <div class="alert alert-danger"><?= $error; ?></div>
                                    <?php endif; ?>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="nama_barang" class="form-label">Nama Barang</label>
                                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="harga" class="form-label">Harga</label>
                                            <input type="text" name="harga" id="harga" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stok" class="form-label">Stok</label>
                                            <input type="text" name="stok" id="stok" class="form-control" required>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary" style="background-color: #3B1E54; border: none;">Tambah Barang</button>
                                        <a href="barang.php" class="btn btn-secondary">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php require 'layouts/footer.php'; ?>