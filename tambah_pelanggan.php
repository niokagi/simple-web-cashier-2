<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

// cegah petugas dengan level admin masuk ke halaman ini
if ($_SESSION['level'] == 'admin') {
    echo "<script>
            alert('Akses ditolak: hanya untuk petugas kasir.');
            window.location.href = 'pelanggan.php';
          </script>";
    exit();
}

if (isset($_POST['submit'])) {
    $nama_pelanggan = trim($_POST['nama_pelanggan']);
    $nomor_telepon = trim($_POST['nomor_telepon']);
    $alamat = trim($_POST['alamat']);

    if (empty($nama_pelanggan) || empty($nomor_telepon) || empty($alamat)) {
        $error = "Semua field wajib diisi!";
    } else {
        $stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, nomor_telepon, alamat) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama_pelanggan, $nomor_telepon, $alamat);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Pelanggan berhasil ditambahkan!');
                    window.location.href = 'pelanggan.php';
                  </script>";
            exit();
        } else {
            $error = "Gagal menambahkan pelanggan: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<body>
    <div class="wrapper">
        <?php require './layouts/sidebar.php'; ?>
        <div class="main" style="background-color: #EEEEEE;">
            <?php require './layouts/nav.php'; ?>
            <main class="content" style="background-color: #EEEEEE;">
                <div class="container-fluid p-0">
                    <h1 class="h3 mb-3">Tambah Pelanggan</h1>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Form Tambah Pelanggan</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($error)) : ?>
                                        <div class="alert alert-danger"><?= $error; ?></div>
                                    <?php endif; ?>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                                            <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                            <input type="number" name="nomor_telepon" id="nomor_telepon" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <textarea name="alamat" id="alamat" class="form-control" required></textarea>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary" style="background-color: #3B1E54; border: none;">Tambah Pelanggan</button>
                                        <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php require 'layouts/footer.php'; ?>