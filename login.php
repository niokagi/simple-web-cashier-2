<?php
session_start();

require './config/conn.php';

if (isset($_SESSION['id_petugas'])) {
	header("Location: index.php");
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<title>Sign In | Cashier</title>
	<link href="css/app.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<main class="d-flex w-100" style="background-color: #D4BEE4;">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h1">Halaman Login</h1>
							<p class="lead">
								Silahkan masuk sebagai admin/kasir
							</p>
						</div>

						<div class="card" style="background-color:#fafafa;">
							<div class="card-body">
								<div class="m-sm-3">
									<form action="./function/proses_login.php" method="POST">
										<div class="mb-3">
											<label class="form-label">Username</label>
											<input class="form-control form-control-lg" type="text" name="username" placeholder="Enter your username" required />
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" required />
										</div>
										<div class="d-grid gap-2 mt-3">
											<button class="btn btn-lg btn-primary py-2 mt-3" type="submit" style="background-color: #3B1E54; border: none; font-size: 18px">Sign in</button>
										</div>
									</form>
									<?php
									if (isset($_SESSION['error'])) {
										echo "<div class='alert alert-danger mt-3'>" . $_SESSION['error'] . "</div>";
										unset($_SESSION['error']);
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>

	<script src="js/app.js"></script>

</body>

</html>