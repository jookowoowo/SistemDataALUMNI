<?php
// includes/auth_admin.php

// Pastikan session sudah dimulai (karena koneksi.php sudah punya session_start())
// include 'includes/koneksi.php'; // Jika belum di-include di halaman utama

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    // Jika tidak login atau bukan admin, arahkan ke halaman login
    header("Location: login.php");
    exit();
}
// Jika sudah login dan role-nya admin, script akan melanjutkan eksekusi halaman utama
?>