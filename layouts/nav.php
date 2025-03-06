<nav class="navbar navbar-expand navbar-light navbar-bg" style="background-color:#fbfbfb;">
    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>
                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                    <img src="img/avatar/female_avatar.png" class="avatar img-fluid rounded-5 me-3" alt="Avatar" />
                    <span class="text-dark">
                        <?php
                        echo $_SESSION['nama_petugas'];
                        echo " (" . $_SESSION['level'] . ")";
                        ?>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" style="color: red;" href="./function/logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?');">
                        Log out
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>