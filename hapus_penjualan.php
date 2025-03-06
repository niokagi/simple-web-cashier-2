<?php
session_start();
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_penjualan = intval($_GET['id']);

    // Mulai transaksi
    $conn->begin_transaction();

    // Ambil detail penjualan untuk mengembalikan stok barang
    $stmt = $conn->prepare("SELECT id_barang, jumlah_barang FROM detail_penjualan WHERE id_penjualan = ?");
    if (!$stmt) {
        $conn->rollback();
        echo "<script>
                alert('Terjadi kesalahan: " . $conn->error . "');
                window.location.href = 'penjualan.php';
              </script>";
        exit();
    }
    $stmt->bind_param("i", $id_penjualan);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $id_barang = $row['id_barang'];
        $jumlah_barang = $row['jumlah_barang'];

        // Kembalikan stok barang dengan menambahkan kembali jumlah yang terjual
        $stmt_update = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
        if ($stmt_update) {
            $stmt_update->bind_param("ii", $jumlah_barang, $id_barang);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }
    $stmt->close();

    // Hapus detail penjualan
    $stmt_del_detail = $conn->prepare("DELETE FROM detail_penjualan WHERE id_penjualan = ?");
    if (!$stmt_del_detail) {
        $conn->rollback();
        echo "<script>
                alert('Gagal menghapus detail penjualan: " . $conn->error . "');
                window.location.href = 'penjualan.php';
              </script>";
        exit();
    }
    $stmt_del_detail->bind_param("i", $id_penjualan);
    $stmt_del_detail->execute();
    $stmt_del_detail->close();

    // Hapus data penjualan utama
    $stmt_del_penjualan = $conn->prepare("DELETE FROM penjualan WHERE id_penjualan = ?");
    if (!$stmt_del_penjualan) {
        $conn->rollback();
        echo "<script>
                alert('Gagal menghapus penjualan: " . $conn->error . "');
                window.location.href = 'penjualan.php';
              </script>";
        exit();
    }
    $stmt_del_penjualan->bind_param("i", $id_penjualan);
    if ($stmt_del_penjualan->execute()) {
        $conn->commit();
        echo "<script>
                alert('Penjualan berhasil dihapus!');
                window.location.href = 'penjualan.php';
              </script>";
    } else {
        $conn->rollback();
        echo "<script>
                alert('Gagal menghapus penjualan!');
                window.location.href = 'penjualan.php';
              </script>";
    }
    $stmt_del_penjualan->close();
} else {
    echo "<script>
            alert('ID tidak valid!');
            window.location.href = 'penjualan.php';
          </script>";
}
