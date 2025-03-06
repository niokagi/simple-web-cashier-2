<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['level'] == 'kasir') {
    echo "<script>
            alert('Akses ditolak.');
            window.location.href = 'petugas.php';
          </script>";
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID petugas tidak ditemukan!'); window.location.href = 'petugas.php';</script>";
    exit();
}

$id_petugas = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM petugas WHERE id_petugas = ?");
$stmt->bind_param("i", $id_petugas);
$stmt->execute();
$result = $stmt->get_result();
$petugas = $result->fetch_assoc();

if (!$petugas) {
    echo "<script>alert('Petugas tidak ditemukan!'); window.location.href = 'petugas.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $nama_petugas = trim($_POST['nama_petugas']);
    $username = trim($_POST['username']);
    $alamat = trim($_POST['alamat']);
    $no_hp = trim($_POST['no_hp']);
    $password = trim($_POST['password']);
    $level = trim($_POST['level']);

    if (empty($nama_petugas) || empty($username) || empty($alamat) || empty($no_hp) || empty($level)) {
        $error = "Semua field wajib diisi kecuali password!";
    } else {
        $allowed_levels = ['admin', 'kasir'];
        if (!in_array($level, $allowed_levels)) {
            $error = "Level tidak valid!";
        } else {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE petugas SET nama_petugas = ?, username = ?, alamat = ?, no_hp = ?, password = ?, level = ? WHERE id_petugas = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssssi", $nama_petugas, $username, $alamat, $no_hp, $hashed_password, $level, $id_petugas);
            } else {
                $query = "UPDATE petugas SET nama_petugas = ?, username = ?, alamat = ?, no_hp = ?, level = ? WHERE id_petugas = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssssi", $nama_petugas, $username, $alamat, $no_hp, $level, $id_petugas);
            }

            if ($stmt->execute()) {
                echo "<script>
                        alert('Petugas berhasil diperbarui!');
                        window.location.href = 'petugas.php';
                      </script>";
                exit();
            } else {
                $error = "Gagal mengupdate petugas: " . $conn->error;
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
                    <h1 class="h3 mb-3">Edit Data Petugas</h1>
                    <br>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Form Edit Petugas</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Notifikasi -->
                                    <?php if (isset($error)) : ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                                    <?php endif; ?>

                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="nama_petugas" class="form-label">Nama Petugas</label>
                                            <input type="text" name="nama_petugas" id="nama_petugas" class="form-control" value="<?= htmlspecialchars($petugas['nama_petugas']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($petugas['username']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <input type="text" name="alamat" id="alamat" class="form-control" value="<?= htmlspecialchars($petugas['alamat']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="no_hp" class="form-label">Nomor Telepon</label>
                                            <input type="text" name="no_hp" id="no_hp" class="form-control" value="<?= htmlspecialchars($petugas['no_hp']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password (Kosongkan jika tidak ingin diubah)</label>
                                            <input type="password" name="password" id="password" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label for="level" class="form-label">Level</label>
                                            <select name="level" id="level" class="form-control" required>
                                                <option value="admin" <?= ($petugas['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                <option value="kasir" <?= ($petugas['level'] == 'kasir') ? 'selected' : ''; ?>>Kasir (Petugas biasa)</option>
                                            </select>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary" style="background-color: #3B1E54; border: none;">Simpan Perubahan</button>
                                        <a href="petugas.php" class="btn btn-secondary">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php require 'layouts/footer.php'; ?>