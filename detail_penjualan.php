<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID penjualan tidak ditemukan!'); window.location.href = 'penjualan.php';</script>";
    exit();
}

$id_penjualan = intval($_GET['id']);

// Ambil data penjualan
$stmt = $conn->prepare("SELECT * FROM penjualan WHERE id_penjualan = ?");
$stmt->bind_param("i", $id_penjualan);
$stmt->execute();
$result = $stmt->get_result();
if (!$penjualan = $result->fetch_assoc()) {
    echo "<script>alert('Penjualan tidak ditemukan!'); window.location.href = 'penjualan.php';</script>";
    exit();
}
$stmt->close();

// Ambil detail penjualan beserta data barang
$stmt = $conn->prepare("SELECT dp.*, b.nama_barang, b.harga FROM detail_penjualan dp JOIN barang b ON dp.id_barang = b.id_barang WHERE dp.id_penjualan = ?");
$stmt->bind_param("i", $id_penjualan);
$stmt->execute();
$result_details = $stmt->get_result();
$details = [];
while ($row = $result_details->fetch_assoc()) {
    $details[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Detail Penjualan #<?= $penjualan['id_penjualan']; ?></title>
    <link href="css/app.css" rel="stylesheet">
    <style>
        .detail-container {
            width: 90%;
            margin: auto;
            padding: 20px;
        }

        .detail-header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php require 'layouts/sidebar.php'; ?>
        <div class="main">
            <?php require 'layouts/nav.php'; ?>
            <main class="content">
                <div class="container-fluid p-0 detail-container">
                    <h1 class="detail-header">Detail Penjualan #<?= $penjualan['id_penjualan']; ?></h1>
                    <p><strong>Tanggal:</strong> <?= date('d-m-Y', strtotime($penjualan['tanggal_penjualan'])); ?></p>
                    <p><strong>Nama Pelanggan:</strong> <?= htmlspecialchars($penjualan['nama_pelanggan']); ?></p>
                    <p><strong>Total Harga:</strong> Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></p>
                    <?php if (isset($penjualan['uang_dibayar'])): ?>
                        <p><strong>Uang Dibayarkan:</strong> Rp <?= number_format($penjualan['uang_dibayar'], 0, ',', '.'); ?></p>
                        <p><strong>Kembalian:</strong> Rp <?= number_format($penjualan['kembalian'], 0, ',', '.'); ?></p>
                    <?php endif; ?>
                    <h2>Detail Barang</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($details as $detail): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($detail['nama_barang']); ?></td>
                                    <td>Rp <?= number_format($detail['harga'], 0, ',', '.'); ?></td>
                                    <td><?= $detail['jumlah_barang']; ?></td>
                                    <td>Rp <?= number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="penjualan.php" class="btn btn-secondary btn-back">Kembali ke Daftar Penjualan</a>
                </div>
            </main>
            <?php require 'layouts/footer.php'; ?>