<?php
session_start();
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_barang = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $stmt->bind_param("i", $id_barang);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt_delete = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
        $stmt_delete->bind_param("i", $id_barang);

        if ($stmt_delete->execute()) {
            echo "<script>
                    alert('Barang berhasil dihapus!');
                    window.location.href = 'barang.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menghapus barang!');
                    window.location.href = 'barang.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Barang tidak ditemukan!');
                window.location.href = 'barang.php';
              </script>";
    }
} else {
    echo "<script>
            alert('ID tidak valid!');
            window.location.href = 'barang.php';
          </script>";
}
