<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

// cegah petugas dengan level admin masuk ke halaman ini
if ($_SESSION['level'] == 'admin') {
    echo "<script>
            alert('Akses ditolak: hanya untuk petugas kasir.');
            window.location.href = 'pelanggan.php';
          </script>";
    exit();
}

$error = "";

if (isset($_POST['submit'])) {
    $id_pelanggan_input = $_POST['id_pelanggan'];
    $tanggal_penjualan = $_POST['tanggal_penjualan'];
    $uang_dibayar = floatval($_POST['uang_dibayar']);

    // Data detail penjualan (berupa array)
    $barang_ids = $_POST['barang'];
    $jumlahs = $_POST['jumlah'];

    if (empty($tanggal_penjualan) || empty($barang_ids)) {
        $error = "Tanggal penjualan dan detail barang harus diisi!";
    }

    $conn->begin_transaction();

    // penentuan mau pakai data pelanggan baru atau yang sudah ada
    if ($id_pelanggan_input === 'new') {
        $nama_pelanggan_baru = trim($_POST['nama_pelanggan_baru']);
        $alamat_baru = trim($_POST['alamat_baru']);
        $nomor_telepon_baru = trim($_POST['nomor_telepon_baru']);

        if (empty($nama_pelanggan_baru) || empty($alamat_baru) || empty($nomor_telepon_baru)) {
            $error = "Untuk pelanggan baru, semua field wajib diisi!";
        } else {
            $stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, alamat, nomor_telepon) VALUES (?, ?, ?)");
            if (!$stmt) {
                $error = "Prepare Error (INSERT pelanggan): " . $conn->error;
            } else {
                $stmt->bind_param("ssi", $nama_pelanggan_baru, $alamat_baru, $nomor_telepon_baru);
                if ($stmt->execute()) {
                    $id_pelanggan = $conn->insert_id;
                    $nama_pelanggan = $nama_pelanggan_baru;
                } else {
                    $error = "Gagal menambahkan pelanggan baru: " . $conn->error;
                }
                $stmt->close();
            }
        }
    } else {
        $id_pelanggan = intval($id_pelanggan_input);
        $stmt = $conn->prepare("SELECT nama_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
        if (!$stmt) {
            $error = "Prepare Error (SELECT pelanggan): " . $conn->error;
        } else {
            $stmt->bind_param("i", $id_pelanggan);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $nama_pelanggan = $row['nama_pelanggan'];
            } else {
                $error = "Pelanggan tidak ditemukan!";
            }
            $stmt->close();
        }
    }

    if (empty($error)) {
        $total_harga = 0;
        $detail_data = array();

        // Proses setiap detail penjualan
        for ($i = 0; $i < count($barang_ids); $i++) {
            $id_barang = $barang_ids[$i];
            $jumlah = intval($jumlahs[$i]);
            if ($jumlah <= 0) continue;

            $stmt = $conn->prepare("SELECT harga, stok FROM barang WHERE id_barang = ?");
            if (!$stmt) {
                $error = "Prepare Error (SELECT barang): " . $conn->error;
                break;
            }
            $stmt->bind_param("i", $id_barang);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($barang_row = $result->fetch_assoc()) {
                $harga = floatval($barang_row['harga']);
                $stok = intval($barang_row['stok']);
                if ($jumlah > $stok) {
                    $error = "Stok untuk barang ID $id_barang tidak mencukupi!";
                    $stmt->close();
                    break;
                }
            } else {
                $stmt->close();
                continue; // Lewati jika barang tidak ditemukan
            }
            $stmt->close();

            $subtotal = $harga * $jumlah;
            $total_harga += $subtotal;
            $detail_data[] = array(
                'id_barang' => $id_barang,
                'jumlah'    => $jumlah,
                'subtotal'  => $subtotal
            );
        }

        // Validasi pembayaran
        if ($uang_dibayar < $total_harga) {
            $error = "Uang yang dibayarkan tidak mencukupi!";
        }

        if (empty($error)) {
            $kembalian = $uang_dibayar - $total_harga;
            $stmt = $conn->prepare("INSERT INTO penjualan (total_harga, id_pelanggan, nama_pelanggan, tanggal_penjualan, uang_dibayar, kembalian) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                $error = "Prepare Error (INSERT penjualan): " . $conn->error;
            } else {
                $stmt->bind_param("dissdd", $total_harga, $id_pelanggan, $nama_pelanggan, $tanggal_penjualan, $uang_dibayar, $kembalian);
                if ($stmt->execute()) {
                    $id_penjualan = $conn->insert_id;
                    $stmt->close();

                    // Simpan detail penjualan dan update stok barang
                    foreach ($detail_data as $detail) {
                        $stmt = $conn->prepare("INSERT INTO detail_penjualan (id_penjualan, id_barang, jumlah_barang, subtotal) VALUES (?, ?, ?, ?)");
                        if ($stmt) {
                            $stmt->bind_param("iiid", $id_penjualan, $detail['id_barang'], $detail['jumlah'], $detail['subtotal']);
                            $stmt->execute();
                            $stmt->close();
                        }
                        $stmt = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
                        if ($stmt) {
                            $stmt->bind_param("ii", $detail['jumlah'], $detail['id_barang']);
                            $stmt->execute();
                            $stmt->close();
                        }
                    }

                    $conn->commit();
                    echo "<script>
                            alert('Penjualan berhasil ditambahkan!\\nTotal: Rp " . number_format($total_harga, 0, ',', '.') .
                        "\\nUang Dibayarkan: Rp " . number_format($uang_dibayar, 0, ',', '.') .
                        "\\nKembalian: Rp " . number_format($kembalian, 0, ',', '.') . "');
                            window.location.href = 'penjualan.php';
                          </script>";
                    exit();
                } else {
                    $error = "Gagal menambahkan penjualan: " . $conn->error;
                }
            }
        } else {
            $conn->rollback();
        }
    } else {
        $conn->rollback();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penjualan</title>
    <link href="css/app.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #newCustomerFields {
            display: none;
        }

        #totalDisplay {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php require './layouts/sidebar.php'; ?>
        <div class="main">
            <?php require './layouts/nav.php'; ?>
            <main class="content">
                <div class="container-fluid" style="background-color: white; padding: 40px; border-radius: 10px;">
                    <h1 class="h3 mb-3">Tambah Penjualan</h1>
                    <br>
                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <!-- Data Utama Penjualan -->
                        <div class="mb-3">
                            <label for="id_pelanggan" class="form-label">Pilih Pelanggan</label>
                            <select name="id_pelanggan" id="id_pelanggan" class="form-control" required>
                                <option value="">Pilih Pelanggan</option>
                                <option value="new">Pelanggan Baru</option>
                                <?php
                                $query = "SELECT * FROM pelanggan";
                                $result_pelanggan = mysqli_query($conn, $query);
                                while ($row = mysqli_fetch_assoc($result_pelanggan)) {
                                    echo '<option value="' . $row['id_pelanggan'] . '">' . htmlspecialchars($row['nama_pelanggan']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div id="newCustomerFields">
                            <div class="mb-3">
                                <label for="nama_pelanggan_baru" class="form-label">Nama Pelanggan Baru</label>
                                <input type="text" name="nama_pelanggan_baru" id="nama_pelanggan_baru" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="alamat_baru" class="form-label">Alamat</label>
                                <input type="text" name="alamat_baru" id="alamat_baru" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="nomor_telepon_baru" class="form-label">Nomor Telepon</label>
                                <input type="text" name="nomor_telepon_baru" id="nomor_telepon_baru" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_penjualan" class="form-label">Tanggal Penjualan</label>
                            <input type="date" name="tanggal_penjualan" id="tanggal_penjualan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="uang_dibayar" class="form-label">Uang Dibayarkan</label>
                            <input type="number" step="0.01" name="uang_dibayar" id="uang_dibayar" class="form-control" required>
                        </div>
                        <!-- Hidden field untuk total harga -->
                        <input type="hidden" name="totalHarga" id="totalHarga" value="0">
                        <div class="mb-3">
                            <label for="kembalian" class="form-label">Kembalian</label>
                            <input type="number" step="0.01" name="kembalian" id="kembalian" class="form-control" readonly>
                        </div>
                        <hr>
                        <!-- Detail Penjualan -->
                        <h4>Detail Penjualan</h4>
                        <table class="table table-bordered" id="detailTable">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="barang[]" class="form-control" required>
                                            <option value="">Pilih Barang</option>
                                            <?php
                                            $query = "SELECT * FROM barang";
                                            $result_barang = mysqli_query($conn, $query);
                                            while ($row = mysqli_fetch_assoc($result_barang)) {
                                                $disabled = ($row['stok'] <= 0) ? "disabled" : "";
                                                $label = htmlspecialchars($row['nama_barang']) . " - Rp " . number_format($row['harga'], 0, ',', '.');
                                                if ($row['stok'] <= 0) {
                                                    $label .= " (habis)";
                                                }
                                                echo '<option value="' . $row['id_barang'] . '" data-price="' . $row['harga'] . '" ' . $disabled . '>' . $label . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control" min="1" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removeRow">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" id="addRow" class="btn btn-secondary">Tambah Item</button>
                        <br><br>
                        <!--  -->
                        <div id="totalDisplay" style="font-size: 20px;">Total yang harus dibayarkan: Rp 0</div>
                        <br>
                        <button type="submit" name="submit" class="btn btn-primary px-4 py-2" style="background-color: #3B1E54; border: none;">Simpan Penjualan</button>
                        <a href="penjualan.php" class="btn btn-secondary px-4 py-2">Batal</a>
                    </form>
                </div>
            </main>
            <?php require 'layouts/footer.php'; ?>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function hitungTotal() {
                var total = 0;
                $('#detailTable tbody tr').each(function() {
                    var jumlah = parseFloat($(this).find('input[name=\"jumlah[]\"]').val()) || 0;
                    var harga = parseFloat($(this).find('select[name=\"barang[]\"] option:selected').data('price')) || 0;
                    total += (harga * jumlah);
                });
                return total;
            }

            function updateTotalDisplay() {
                var total = hitungTotal();
                $('#totalDisplay').text('Total yang harus dibayarkan: Rp ' + total.toLocaleString('id-ID'));
                $('#totalHarga').val(total);
                var uang = parseFloat($('#uang_dibayar').val()) || 0;
                $('#kembalian').val((uang - total).toFixed(2));
            }

            $(document).on('input change', 'input[name=\"jumlah[]\"], select[name=\"barang[]\"]', function() {
                updateTotalDisplay();
            });

            $('#uang_dibayar').on('input', function() {
                updateTotalDisplay();
            });

            $('#addRow').click(function() {
                var newRow = '<tr>' +
                    '<td>' +
                    '<select name="barang[]" class="form-control" required>' +
                    '<option value="">Pilih Barang</option>';
                <?php
                $result_barang = mysqli_query($conn, "SELECT * FROM barang");
                while ($row = mysqli_fetch_assoc($result_barang)) {
                    $disabled = ($row['stok'] <= 0) ? "disabled" : "";
                    $label = htmlspecialchars($row['nama_barang']) . " - Rp " . number_format($row['harga'], 0, ',', '.');
                    if ($row['stok'] <= 0) {
                        $label .= " (habis)";
                    }
                    echo "newRow += '<option value=\"{$row['id_barang']}\" data-price=\"{$row['harga']}\" $disabled>$label</option>';";
                }
                ?>
                newRow += '</select>' +
                    '</td>' +
                    '<td><input type="number" name="jumlah[]" class="form-control" min="1" required></td>' +
                    '<td><button type="button" class="btn btn-danger removeRow">Hapus</button></td>' +
                    '</tr>';
                $('#detailTable tbody').append(newRow);
                updateTotalDisplay();
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                updateTotalDisplay();
            });

            $('#id_pelanggan').change(function() {
                if ($(this).val() == 'new') {
                    $('#newCustomerFields').show();
                    $('#nama_pelanggan_baru, #alamat_baru, #nomor_telepon_baru').attr('required', true);
                } else {
                    $('#newCustomerFields').hide();
                    $('#nama_pelanggan_baru, #alamat_baru, #nomor_telepon_baru').removeAttr('required');
                }
            });

            updateTotalDisplay();
        });
    </script>
</body>

</html>