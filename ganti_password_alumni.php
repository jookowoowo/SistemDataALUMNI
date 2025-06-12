<?php
// ganti_password_alumni.php

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

// Lanjutkan dengan logika halaman jika admin terautentikasi
$success_message = '';
$error_message = '';
$alumni_nama = '';
$id_alumni = '';

// Check if an ID is provided in the URL and fetch data for initial display
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_alumni = mysqli_real_escape_string($conn, $_GET['id']);
    $sql_fetch_alumni = "SELECT nama FROM alumni WHERE id_alumni = '$id_alumni'";
    $result_fetch_alumni = mysqli_query($conn, $sql_fetch_alumni);

    if ($result_fetch_alumni && mysqli_num_rows($result_fetch_alumni) > 0) {
        $row_alumni = mysqli_fetch_assoc($result_fetch_alumni);
        $alumni_nama = htmlspecialchars($row_alumni['nama']);
    } else {
        $error_message = "Alumni tidak ditemukan.";
        echo "<div class='container mx-auto p-4 mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
        echo htmlspecialchars($error_message);
        echo "</div>";
        exit(); 
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
     $error_message = "ID Alumni tidak disediakan.";
     echo "<div class='container mx-auto p-4 mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
     echo htmlspecialchars($error_message);
     echo "</div>";
     exit();
}


// Handle form submission to change password (POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_alumni_post'])) {
    $id_alumni_post = mysqli_real_escape_string($conn, $_POST['id_alumni_post']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Re-fetch alumni name in case of POST and ID was not in GET initially
    if (empty($alumni_nama)) {
        $sql_fetch_alumni_post = "SELECT nama FROM alumni WHERE id_alumni = '$id_alumni_post'";
        $result_fetch_alumni_post = mysqli_query($conn, $sql_fetch_alumni_post);
        if ($result_fetch_alumni_post && mysqli_num_rows($result_fetch_alumni_post) > 0) {
            $row_alumni_post = mysqli_fetch_assoc($result_fetch_alumni_post);
            $alumni_nama = htmlspecialchars($row_alumni_post['nama']);
        }
    }

    // Basic validation
    if (empty($new_password) || empty($confirm_password)) {
        $error_message = "Password baru dan konfirmasi password tidak boleh kosong.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Password baru dan konfirmasi password tidak cocok.";
    } else {
        // Hash the password before storing
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql_update_password = "UPDATE alumni SET password = '$hashed_password' WHERE id_alumni = '$id_alumni_post'";

        if (mysqli_query($conn, $sql_update_password)) {
            header("Location: alumni_list.php?status=success_password_change_alumni");
            exit(); 
        } else {
            $error_message = "Error memperbarui password: " . mysqli_error($conn);
        }
    }
}

include 'includes/header.php';
?>

<div class="container mx-auto p-4 mt-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Ganti Password Alumni: <?php echo $alumni_nama; ?></h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message && empty($success_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <form action="ganti_password_alumni.php" method="POST">
            <input type="hidden" name="id_alumni_post" value="<?php echo htmlspecialchars($id_alumni); ?>">
            
            <div class="mb-4">
                <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru:</label>
                <input type="password" name="new_password" id="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Ubah Password
                </button>
                <a href="alumni_list.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
