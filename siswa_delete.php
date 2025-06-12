<?php
// siswa_delete.php

// Sertakan koneksi database dan mulai sesi. INI HARUS DI BARIS PALING ATAS.
include 'includes/koneksi.php';
session_start();

// --- BLOK PEMERIKSAAN AKSES ADMIN ---
// Cek jika pengguna belum login atau bukan admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Alihkan (redirect) ke halaman index.php dengan pesan
    header("Location: index.php?message=Anda tidak memiliki akses ke halaman ini.");
    exit(); // Sangat penting: Hentikan eksekusi script setelah redirect
}
// --- AKHIR BLOK PEMERIKSAAN AKSES ADMIN ---

// Lanjutkan dengan logika penghapusan jika admin terautentikasi
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_siswa = mysqli_real_escape_string($conn, $_GET['id']);

    // Pertama, ambil nama file foto untuk dihapus dari server
    $sql_select_foto = "SELECT foto FROM siswa WHERE id_siswa = '$id_siswa'";
    $result_select_foto = mysqli_query($conn, $sql_select_foto);
    if ($result_select_foto && mysqli_num_rows($result_select_foto) > 0) {
        $row = mysqli_fetch_assoc($result_select_foto);
        $foto_filename = $row['foto'];

        // Hapus file foto dari server jika ada dan file tersebut benar-benar ada
        if (!empty($foto_filename) && file_exists("uploads/" . $foto_filename)) {
            unlink("uploads/" . $foto_filename); // Hapus file fisik
        }
    }

    // Kemudian, hapus data siswa dari database
    $sql_delete = "DELETE FROM siswa WHERE id_siswa = '$id_siswa'";
    if (mysqli_query($conn, $sql_delete)) {
        // Redirect kembali ke halaman daftar alumni (yang juga menampilkan siswa) dengan pesan sukses
        header("Location: alumni_list.php?status=success_delete_siswa");
        exit(); 
    } else {
        // Tampilkan pesan error jika terjadi masalah pada query database
        echo "<div class='container mx-auto p-4 mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
        echo "Error menghapus record: " . htmlspecialchars(mysqli_error($conn));
        echo "</div>";
        exit(); 
    }
} else {
    // Tampilkan pesan error jika ID siswa tidak disediakan atau tidak valid
    echo "<div class='container mx-auto p-4 mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
    echo "ID Siswa tidak valid atau tidak disediakan.";
    echo "</div>";
    exit(); 
}
// Tidak ada include header.php di sini karena script akan exit setelah operasi selesai.
?>
