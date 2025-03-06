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

$stmt = $conn->prepare("SELECT dp.*, b.nama_barang, b.harga FROM detail_penjualan dp JOIN barang b ON dp.id_barang = b.id_barang WHERE dp.id_penjualan = ?");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan #<?= $penjualan['id_penjualan']; ?></title>
    <link href="css/app.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .nota-container {
            width: 60%;
            background: #fff;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .nota-header,
        .nota-footer {
            text-align: center;
            padding: 10px 0;
        }

        .nota-header h2 {
            margin: 0;
            color: #3B1E54;
        }

        .nota-details,
        .nota-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .nota-details th,
        .nota-details td,
        .nota-items th,
        .nota-items td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .nota-items th {
            background-color: #3B1E54;
            color: #fff;
        }

        .print-btn {
            text-align: center;
            margin-top: 20px;
        }

        .print-btn button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .print-btn button:hover {
            background-color: #218838;
        }

        @media print {
            .print-btn {
                display: none;
            }

            .nota-container {
                width: 100%;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>
    <div class="nota-container">
        <div class="nota-header">
            <h2>Nota Penjualan</h2>
            <p>Nomor: #<?= $penjualan['id_penjualan']; ?> | Tanggal: <?= date('d-m-Y', strtotime($penjualan['tanggal_penjualan'])); ?></p>
        </div>

        <table class="nota-details">
            <tr>
                <th>Nama Pelanggan</th>
                <td><?= htmlspecialchars($penjualan['nama_pelanggan']); ?></td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>Rp <?= number_format($penjualan['total_harga'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Uang Dibayarkan</th>
                <td>Rp <?= number_format($penjualan['uang_dibayar'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Kembalian</th>
                <td>Rp <?= number_format($penjualan['kembalian'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <h3>Detail Barang</h3>
        <table class="nota-items">
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
                <?php foreach ($details as $index => $detail): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($detail['nama_barang']); ?></td>
                        <td>Rp <?= number_format($detail['harga'], 0, ',', '.'); ?></td>
                        <td><?= $detail['jumlah_barang']; ?></td>
                        <td>Rp <?= number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="nota-footer">
            <p><strong>Terima kasih telah berbelanja!</strong></p>
        </div>
    </div>

    <div class="print-btn">
        <button onclick="window.print();">Cetak Nota</button>
    </div>
</body>

</html>