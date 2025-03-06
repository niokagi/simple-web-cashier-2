<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if ($_SESSION['level'] == 'kasir') {
    echo "<script>
            alert('Akses ditolak.');
            window.location.href = 'petugas.php';
          </script>";
    exit();
}

if (isset($_POST['submit'])) {
    $nama_petugas = trim($_POST['nama_petugas']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $alamat = trim($_POST['alamat']);
    $no_hp = trim($_POST['no_hp']);
    $level = isset($_POST['level']) ? trim($_POST['level']) : '';

    if (empty($nama_petugas) || empty($username) || empty($password) || empty($alamat) || empty($no_hp) || empty($level)) {
        $error = "Semua field wajib diisi!";
    } else {
        $allowed_levels = ['admin', 'kasir'];
        if (!in_array($level, $allowed_levels)) {
            $error = "Level tidak valid!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO petugas (nama_petugas, username, password, alamat, no_hp, level) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                $error = "Prepare failed: " . mysqli_error($conn);
            } else {
                mysqli_stmt_bind_param($stmt, "ssssss", $nama_petugas, $username, $hashed_password, $alamat, $no_hp, $level);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Petugas berhasil ditambahkan!";
                } else {
                    $error = "Gagal menambahkan petugas: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
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
                    <h1 class="h3 mb-3">Tambah/Registrasikan Petugas</h1>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Form Registrasi Petugas</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Notifikasi -->
                                    <?php if (isset($success)) : ?>
                                        <div class="alert alert-success"><?= $success; ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($error)) : ?>
                                        <div class="alert alert-danger"><?= $error; ?></div>
                                    <?php endif; ?>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="nama_petugas" class="form-label">Nama Petugas</label>
                                            <input type="text" name="nama_petugas" id="nama_petugas" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" name="username" id="username" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" name="password" id="password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <input type="text" name="alamat" id="alamat" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="no_hp" class="form-label">Nomor Telepon</label>
                                            <input type="text" name="no_hp" id="no_hp" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="level" class="form-label">Level</label>
                                            <select name="level" id="level" class="form-control" required>
                                                <option value="">Pilih Level</option>
                                                <option value="admin">Admin</option>
                                                <option value="kasir">Kasir (Petugas biasa)</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary" style="background-color: #3B1E54; border: none;">Tambah Petugas</button>
                                        <a href="petugas.php" class="btn btn-secondary">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php require 'layouts/footer.php'; ?>