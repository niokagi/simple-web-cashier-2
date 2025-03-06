<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar" style="background-color: #3B1E54;">
        <a class="sidebar-brand" href="index.php">
            <span class="align-middle">[Nama Web Kasir]</span>
        </a>
        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Pages
            </li>
            <li class="sidebar-item <?= ($current_page == 'index.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="index.php" style="background-color: #3B1E54;">
                    <i class="align-middle" data-feather="sliders"></i>
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item <?= ($current_page == 'petugas.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="petugas.php" style="background-color: #3B1E54;">
                    <i class="align-middle" data-feather="user"></i>
                    <span class="align-middle">Petugas</span>
                </a>
            </li>
            <li class="sidebar-item <?= ($current_page == 'pelanggan.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="pelanggan.php" style="background-color: #3B1E54;">
                    <i class="align-middle" data-feather="user"></i>
                    <span class="align-middle">Pelanggan</span>
                </a>
            </li>
            <li class="sidebar-item <?= ($current_page == 'barang.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="barang.php" style="background-color: #3B1E54;">
                    <i class="align-middle" data-feather="package"></i>
                    <span class="align-middle">Barang</span>
                </a>
            </li>
            <li class="sidebar-item <?= ($current_page == 'penjualan.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="penjualan.php" style="background-color: #3B1E54;">
                    <i class="align-middle" data-feather="book"></i>
                    <span class="align-middle">Penjualan</span>
                </a>
            </li>
        </ul>
    </div>
</nav>