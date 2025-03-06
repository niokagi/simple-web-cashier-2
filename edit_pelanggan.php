<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID pelanggan tidak ditemukan!'); window.location.href = 'pelanggan.php';</script>";
    exit();
}

$id_pelanggan = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();
$pelanggan = $result->fetch_assoc();

if (!$pelanggan) {
    echo "<script>alert('Pelanggan tidak ditemukan!'); window.location.href = 'pelanggan.php';</script>";
    exit();
}

if (isset($_POST['submit'])) {
    $nama_pelanggan = trim($_POST['nama_pelanggan']);
    $alamat = trim($_POST['alamat']);
    $telepon = trim($_POST['telepon']);

    if (empty($nama_pelanggan) || empty($alamat) || empty($telepon)) {
        $error = "Semua field wajib diisi!";
    } else {
        $query = "UPDATE pelanggan SET nama_pelanggan = ?, alamat = ?, nomor_telepon = ? WHERE id_pelanggan = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $nama_pelanggan, $alamat, $telepon, $id_pelanggan);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Pelanggan berhasil diperbarui!');
                    window.location.href = 'pelanggan.php';
                  </script>";
            exit();
        } else {
            $error = "Gagal mengupdate pelanggan: " . $conn->error;
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
                    <h1 class="h3 mb-3">Edit Data Pelanggan</h1>
                    <br>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Form Edit Pelanggan</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Notifikasi -->
                                    <?php if (isset($error)) : ?>
                                        <div class="alert alert-danger"><?= $error; ?></div>
                                    <?php endif; ?>

                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                                            <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" value="<?= htmlspecialchars($pelanggan['nama_pelanggan']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <input type="text" name="alamat" id="alamat" class="form-control" value="<?= htmlspecialchars($pelanggan['alamat']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="telepon" class="form-label">Telepon</label>
                                            <input type="text" name="telepon" id="telepon" class="form-control" value="<?= htmlspecialchars($pelanggan['nomor_telepon']); ?>" required>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary" style="background-color: #3B1E54; border: none;">Simpan Perubahan</button>
                                        <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php require 'layouts/footer.php'; ?>