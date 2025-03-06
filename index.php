<?php
session_start();
require 'layouts/header.php';
require './config/conn.php';

if (!isset($_SESSION['id_petugas'])) {
	header("Location: login.php");
	exit();
}

// Mengambil Data guna Statistik Penjualan
$queryPenjualan = "SELECT COUNT(*) AS total_penjualan, SUM(total_harga) AS total_revenue FROM penjualan";
$resultPenjualan = mysqli_query($conn, $queryPenjualan);
$dataPenjualan = mysqli_fetch_assoc($resultPenjualan);
$totalPenjualan = $dataPenjualan['total_penjualan'];
// $totalRevenue = $dataPenjualan['total_revenue'];

// Data Statistik Barang
$queryBarang = "SELECT COUNT(*) AS total_barang, SUM(stok) AS total_stock FROM barang";
$resultBarang = mysqli_query($conn, $queryBarang);
$dataBarang = mysqli_fetch_assoc($resultBarang);
// $totalBarang = $dataBarang['total_barang'];
$totalStock = $dataBarang['total_stock'];
?>

<body>
	<div class="wrapper">
		<?php require './layouts/sidebar.php'; ?>

		<div class="main" style="background-color: #EEEEEE;">
			<?php require './layouts/nav.php'; ?>

			<main class="content" style="background-color: #EEEEEE;">
				<div class="container-fluid p-0">
					<h1 class="h1 mb-4">Dashboard Administrator</h1>
					<div class="row">
						<!-- Card: Total Penjualan -->
						<div class="col-xl-6 col-xxl-6 d-flex">
							<div class="card flex-fill">
								<div class="card-body">
									<div class="row">
										<div class="col mt-0">
											<h3 class="card-title" style="font-size: 20px;">Total Penjualan</h3>
										</div>
										<div class="col-auto">
											<i class="align-middle" data-feather="shopping-cart"></i>
										</div>
									</div>
									<h1 class="mt-3 mb-3"><?= number_format($totalPenjualan); ?></h1>
									<div class="mb-0">
										<span class="text-muted">Transaksi</span>
									</div>
								</div>
							</div>
						</div>
						<!-- Card: Total Stok Barang -->
						<div class="col-xl-6 col-xxl-6 d-flex">
							<div class="card flex-fill">
								<div class="card-body">
									<div class="row">
										<div class="col mt-0">
											<h5 class="card-title" style="font-size: 20px;">Total Stok Barang</h5>
										</div>
										<div class="col-auto">
											<div class="">
												<i class="align-middle" data-feather="layers"></i>
											</div>
										</div>
									</div>
									<h1 class="mt-3 mb-3"><?= number_format($totalStock); ?></h1>
									<div class="mb-0">
										<span class="text-muted">Unit</span>
									</div>
								</div>
							</div>
						</div>
						<!-- Tambah Penjualan -->
						<?php
						if ($_SESSION['level'] == 'kasir') {
							echo '<a href="tambah_penjualan.php" class="col-xl-6 col-xxl-6 d-flex" style="text-decoration: none;">';
							echo '<div class="card flex-fill" style="background-color: #D4BEE4;">';
							echo '<div class="card-body">';
							echo '<div class="row">';
							echo '<div class="col-auto">';
							echo '<div style="text-weight: bold; color: #3B1E54;">';
							echo '<i class="align-middle" data-feather="shopping-cart"></i>';
							echo '</div>';
							echo '</div>';
							echo '</div>';
							echo '<h2 class="mt-3 mb-3" style=" font-weight: bold; color: #3B1E54;">Tambah Transaksi Penjualan (Baru)</h2>';
							echo '</div>';
							echo '</div>';
							echo '</a>';
						}
						?>
					</div>
					<!-- Bagian Statistik akhir -->
				</div>
			</main>

			<?php require 'layouts/footer.php'; ?>
		</div>
	</div>
</body>

</html>