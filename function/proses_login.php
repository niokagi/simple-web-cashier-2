<?php
session_start();
require '../config/conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id_petugas, username, password, nama_petugas, level FROM petugas WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // compare pw
        if (password_verify($password, $row['password'])) {
            // buat sesi jika pw benar
            $_SESSION['id_petugas'] = $row['id_petugas'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama_petugas'] = $row['nama_petugas'];
            $_SESSION['level'] = $row['level'];

            echo "<script>
                    alert('Login Berhasil!');
                    window.location.href = '../index.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Username atau password salah!');
                    window.location.href = '../login.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Username atau password salah!');
                window.location.href = '../login.php';
              </script>";
        exit();
    }
}
