<?php
session_start();
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: ./login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_petugas = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM petugas WHERE id_petugas = ?");
    $stmt->bind_param("i", $id_petugas);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt_delete = $conn->prepare("DELETE FROM petugas WHERE id_petugas = ?");
        $stmt_delete->bind_param("i", $id_petugas);

        if ($stmt_delete->execute()) {
            echo "<script>
                alert('Petugas berhasil dihapus!');
                window.location.href = 'petugas.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menghapus petugas!');
                window.location.href = 'petugas.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Petugas tidak ditemukan!');
            window.location.href = 'petugas.php';
        </script>";
    }
} else {
    echo "<script>
        alert('ID tidak valid!');
        window.location.href = 'petugas.php';
    </script>";
}
