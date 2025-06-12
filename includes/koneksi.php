<?php
// includes/koneksi.php
// File ini hanya untuk koneksi database. Tidak ada HTML, tidak ada session_start().

$servername = "localhost";
$username = "root"; // <-- PASTIKAN INI 'root'
$password = "";     // <-- PASTIKAN INI KOSONG (dua tanda kutip tanpa spasi di antaranya)
$dbname = "webalumni"; // Sesuaikan dengan nama database Anda

// Buat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
// Penting: Tidak ada tag penutup ?> di sini untuk mencegah output tak terlihat.
