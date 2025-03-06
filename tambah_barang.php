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
            // Inisialisasi variabel untuk nama file gambar
            $gambar_file = "";
            // Cek apakah file gambar diupload tanpa error
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                $file_name = $_FILES['gambar']['name'];
                $file_tmp  = $_FILES['gambar']['tmp_name'];
                $file_size = $_FILES['gambar']['size'];
                $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Validasi ekstensi file
                if (!in_array($file_ext, $allowed_extensions)) {
                    $error = "Format gambar tidak diperbolehkan. Hanya jpg, jpeg, png, dan gif yang diperbolehkan.";
                } else {
                    // Maksimal ukuran file 2MB
                    $max_size = 2 * 1024 * 1024;
                    if ($file_size > $max_size) {
                        $error = "Ukuran gambar terlalu besar, maksimal 2MB.";
                    } else {
                        // Membuat nama file baru yang unik
                        $new_file_name = uniqid("produk_", true) . "." . $file_ext;
                        $target_directory = "gambar_produk/";
                        // Pastikan folder tujuan ada
                        if (!is_dir($target_directory)) {
                            mkdir($target_directory, 0755, true);
                        }
                        $target_file = $target_directory . $new_file_name;
                        // Pindahkan file yang diupload ke folder tujuan
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            $gambar_file = $new_file_name;
                        } else {
                            $error = "Gagal mengupload gambar.";
                        }
                    }
                }
            }

            // Lanjutkan jika tidak ada error
            if (!isset($error)) {
                $harga = floatval($harga);
                $stok = intval($stok);
                $query = "INSERT INTO barang (nama_barang, harga, stok, gambar) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sdis", $nama_barang, $harga, $stok, $gambar_file);

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
                                    <!-- Perhatikan penambahan enctype untuk upload file -->
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="nama_barang" class="form-label">Nama Barang</label>
                                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="harga" class="form-label">Harga</label>
                                            <input type="number" min="0" name="harga" id="harga" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stok" class="form-label">Stok</label>
                                            <input type="text" name="stok" id="stok" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gambar" class="form-label">Gambar Barang</label>
                                            <input type="file" name="gambar" id="gambar" class="form-control">
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