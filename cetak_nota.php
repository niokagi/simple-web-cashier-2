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
$penjualan = $result->fetch_assoc();
if (!$penjualan) {
    echo "<script>alert('Data penjualan tidak ditemukan!'); window.location.href = 'penjualan.php';</script>";
    exit();
}
$stmt->close();

// Ambil detail penjualan
$stmt = $conn->prepare("
    SELECT dp.*, b.nama_barang, b.harga 
    FROM detail_penjualan dp 
    JOIN barang b ON dp.id_barang = b.id_barang 
    WHERE dp.id_penjualan = ?
");
$stmt->bind_param("i", $id_penjualan);
$stmt->execute();
$result_details = $stmt->get_result();
$details = $result_details->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan #<?= $penjualan['id_penjualan']; ?></title>
    <link href="css/app.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .nota-container {
            width: 300px;
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .nota-header,
        .nota-footer {
            text-align: center;
            margin-bottom: 10px;
        }

        .nota-header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .nota-header p {
            margin: 3px 0;
            font-size: 14px;
        }

        .items {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .items p {
            margin: 5px 0;
            font-size: 14px;
        }

        .items p span {
            float: right;
        }

        .nota-total {
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .nota-total p {
            margin: 3px 0;
        }

        .nota-total p span {
            float: right;
        }

        .nota-footer p {
            margin: 5px 0;
            font-size: 14px;
        }

        .print-btn {
            text-align: center;
            margin: 10px auto;
            width: 300px;
        }

        .print-btn button,
        .print-btn a {
            margin: 5px;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .print-btn button {
            background-color: #3B1E54;
        }

        .print-btn a {
            background-color: #6c757d;
        }

        @media print {
            .print-btn {
                display: none;
            }

            .nota-container {
                width: auto;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>

<body>

    <div class="nota-container">
        <div class="nota-header">
            <h2>TOKO SEMBAKO</h2>
            <p>Jl. Contoh No. 123, Jakarta</p>
            <p>Telp: 0812-3456-7890</p>
        </div>
        <hr>

        <div class="items">
            <?php foreach ($details as $detail) : ?>
                <p>
                    <?= htmlspecialchars($detail['nama_barang']); ?>
                    x<?= $detail['jumlah_barang']; ?>
                    <span>Rp <?= number_format($detail['harga'], 0, ',', '.'); ?></span>
                </p>
            <?php endforeach; ?>
        </div>
        <hr>

        <!-- Total, Bayar, Kembalian -->
        <div class="nota-total">
            <p>Total: <span>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></span></p>
            <p>Bayar: <span>Rp <?= number_format($penjualan['uang_dibayar'], 0, ',', '.'); ?></span></p>
            <p>Kembalian: <span>Rp <?= number_format($penjualan['kembalian'], 0, ',', '.'); ?></span></p>
        </div>
        <hr>

        <div class="nota-footer">
            <p>Terima kasih atas kunjungan Anda</p>
            <p>
                Tanggal:
                <?= date('d-m-Y H:i:s', strtotime($penjualan['tanggal_penjualan'])); ?>
            </p>
        </div>
    </div>

    <div class="print-btn">
        <button onclick="window.print();">Cetak Struk</button>
        <a href="index.php">Kembali ke Dashboard</a>
    </div>

</body>

</html>