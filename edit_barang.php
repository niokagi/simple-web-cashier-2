<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID barang tidak ditemukan!'); window.location.href='barang.php';</script>";
    exit();
}

$id_barang = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();

if (!$barang) {
    echo "<script>alert('Barang tidak ditemukan!'); window.location.href='barang.php';</script>";
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
            $gambar_file = $barang['gambar'];

            // Proses upload gambar jika ada file baru
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                $file_name = $_FILES['gambar']['name'];
                $file_tmp  = $_FILES['gambar']['tmp_name'];
                $file_size = $_FILES['gambar']['size'];
                $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if (!in_array($file_ext, $allowed_extensions)) {
                    $error = "Format gambar tidak diperbolehkan. Hanya jpg, jpeg, png, dan gif yang diperbolehkan.";
                } else {
                    $max_size = 2 * 1024 * 1024; // ukuran 2MB
                    if ($file_size > $max_size) {
                        $error = "Ukuran gambar terlalu besar, maksimal 2MB.";
                    } else {
                        // generate nama unik untuk file gambar baru
                        $new_file_name = uniqid("produk_", true) . "." . $file_ext;
                        $target_directory = "gambar_produk/";
                        if (!is_dir($target_directory)) {
                            mkdir($target_directory, 0755, true);
                        }
                        $target_file = $target_directory . $new_file_name;
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            // Hapus gambar lama jika ada
                            if (!empty($barang['gambar']) && file_exists($target_directory . $barang['gambar'])) {
                                unlink($target_directory . $barang['gambar']);
                            }
                            $gambar_file = $new_file_name;
                        } else {
                            $error = "Gagal mengupload gambar.";
                        }
                    }
                }
            }

            if (!isset($error)) {
                $harga = floatval($harga);
                $stok = intval($stok);
                $query = "UPDATE barang SET nama_barang = ?, harga = ?, stok = ?, gambar = ? WHERE id_barang = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sdisi", $nama_barang, $harga, $stok, $gambar_file, $id_barang);

                if ($stmt->execute()) {
                    echo "<script>
                            alert('Barang berhasil diperbarui!');
                            window.location.href = 'barang.php';
                          </script>";
                    exit();
                } else {
                    $error = "Gagal mengupdate barang: " . $conn->error;
                }
                $stmt->close();
            }
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
                    <h1 class="h3 mb-3">Edit Barang</h1>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Form Edit Barang</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($error)) : ?>
                                        <div class="alert alert-danger"><?= $error; ?></div>
                                    <?php endif; ?>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="nama_barang" class="form-label">Nama Barang</label>
                                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="harga" class="form-label">Harga</label>
                                            <input type="text" name="harga" id="harga" class="form-control" value="<?= htmlspecialchars($barang['harga']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stok" class="form-label">Stok</label>
                                            <input type="text" name="stok" id="stok" class="form-control" value="<?= htmlspecialchars($barang['stok']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gambar" class="form-label">Gambar Barang</label>
                                            <input type="file" name="gambar" id="gambar" class="form-control">
                                            <?php if (!empty($barang['gambar'])): ?>
                                                <img src="gambar_produk/<?= htmlspecialchars($barang['gambar']); ?>" alt="<?= htmlspecialchars($barang['nama_barang']); ?>" style="max-width: 150px; margin-top: 10px;">
                                            <?php endif; ?>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary" style="background-color: #3B1E54; border: none;">Simpan Perubahan</button>
                                        <a href="barang.php" class="btn btn-secondary">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php require 'layouts/footer.php'; ?>