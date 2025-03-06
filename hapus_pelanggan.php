<?php
session_start();
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: ./login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
    $stmt->bind_param("i", $id_pelanggan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt_delete = $conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
        $stmt_delete->bind_param("i", $id_pelanggan);

        if ($stmt_delete->execute()) {
            echo "<script>
                alert('Pelanggan berhasil dihapus!');
                window.location.href = 'pelanggan.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menghapus pelanggan!');
                window.location.href = 'pelanggan.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Pelanggan tidak ditemukan!');
            window.location.href = 'pelanggan.php';
        </script>";
    }
} else {
    echo "<script>
        alert('ID tidak valid!');
        window.location.href = 'pelanggan.php';
    </script>";
}
