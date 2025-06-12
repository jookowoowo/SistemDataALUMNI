<?php
// alumni_edit.php

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
$alumni_data = null;

// Bagian ini untuk mengambil data alumni yang akan diedit SAAT HALAMAN PERTAMA KALI DIMUAT
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_alumni = mysqli_real_escape_string($conn, $_GET['id']);
    $sql_fetch = "SELECT * FROM alumni WHERE id_alumni = '$id_alumni'";
    $result_fetch = mysqli_query($conn, $sql_fetch);

    if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
        $alumni_data = mysqli_fetch_assoc($result_fetch);
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


// Logika untuk memproses update formulir (saat POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_alumni'])) {
    $id_alumni = mysqli_real_escape_string($conn, $_POST['id_alumni']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $id_jurusan = mysqli_real_escape_string($conn, $_POST['id_jurusan']);
    
    // Handle photo upload
    $foto_update = '';
    $current_foto = $_POST['current_foto'] ?? ''; 

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if($check !== false) {
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $error_message .= "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.<br>";
            } else {
                // Hapus foto lama jika ada dan berhasil upload foto baru
                if (!empty($current_foto) && file_exists("uploads/" . $current_foto)) {
                    unlink("uploads/" . $current_foto);
                }
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $foto = basename($_FILES["foto"]["name"]);
                } else {
                    $error_message .= "Maaf, terjadi kesalahan saat mengunggah file baru Anda.<br>";
                }
            }
        } else {
            $error_message .= "File bukan gambar.<br>";
        }
    }

    // Attempt update query if no file upload errors
    if (empty($error_message)) {
        $sql_update = "UPDATE alumni SET 
                       nama = '$nama', 
                       jenis_kelamin = '$jenis_kelamin', 
                       email = '$email', 
                       alamat = '$alamat', 
                       id_jurusan = '$id_jurusan'
                       $foto_update 
                       WHERE id_alumni = '$id_alumni'";
        
        if (mysqli_query($conn, $sql_update)) {
            header("Location: alumni_list.php?status=success_edit_alumni");
            exit(); 
        } else {
            $error_message = "Error: " . $sql_update . "<br>" . mysqli_error($conn);
        }
    }
    
    // Jika ada error setelah POST, re-fetch data untuk menampilkan form dengan data terbaru/input user
    $sql_fetch = "SELECT * FROM alumni WHERE id_alumni = '$id_alumni'";
    $result_fetch = mysqli_query($conn, $sql_fetch);
    $alumni_data = mysqli_fetch_assoc($result_fetch); 
}

// Fetch available jurusan (needed for both initial load and post-submission)
$jurusan_query = "SELECT id_jurusan, nama_jurusan FROM jurusan ORDER BY nama_jurusan ASC";
$jurusan_result = mysqli_query($conn, $jurusan_query);

if (!$jurusan_result) {
    echo "<div class='container mx-auto p-4 mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
    echo "Error mengambil data jurusan: " . mysqli_error($conn);
    echo "</div>";
    exit();
}

include 'includes/header.php'; // Sertakan bagian header HTML
?>

<div class="container mx-auto p-4 mt-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Edit Data Alumni</h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($alumni_data): // Pastikan data alumni sudah terambil sebelum menampilkan form ?>
    <div class="bg-white shadow-lg rounded-lg p-6">
        <form action="alumni_edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_alumni" value="<?php echo htmlspecialchars($alumni_data['id_alumni']); ?>">
            <input type="hidden" name="current_foto" value="<?php echo htmlspecialchars($alumni_data['foto']); ?>">

            <div class="mb-4">
                <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
                <input type="text" name="nama" id="nama" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($alumni_data['nama']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="jenis_kelamin" class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin:</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="L" <?php echo ($alumni_data['jenis_kelamin'] == 'L' ? 'selected' : ''); ?>>Laki-laki</option>
                    <option value="P" <?php echo ($alumni_data['jenis_kelamin'] == 'P' ? 'selected' : ''); ?>>Perempuan</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($alumni_data['email']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat:</label>
                <textarea name="alamat" id="alamat" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="4" required><?php echo htmlspecialchars($alumni_data['alamat']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="id_jurusan" class="block text-gray-700 text-sm font-bold mb-2">Jurusan:</label>
                <select name="id_jurusan" id="id_jurusan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <?php 
                    // Reset pointer result set jika sudah pernah digunakan
                    mysqli_data_seek($jurusan_result, 0); 
                    while($row_jurusan = mysqli_fetch_assoc($jurusan_result)): ?>
                        <option value="<?php echo htmlspecialchars($row_jurusan['id_jurusan']); ?>" <?php echo ($alumni_data['id_jurusan'] == $row_jurusan['id_jurusan'] ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($row_jurusan['nama_jurusan']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="foto" class="block text-gray-700 text-sm font-bold mb-2">Foto (Biarkan kosong jika tidak ingin mengubah):</label>
                <?php if (!empty($alumni_data['foto'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($alumni_data['foto']); ?>" alt="Foto Saat Ini" class="w-24 h-24 object-cover rounded-full mb-2">
                    <p class="text-xs text-gray-600 mb-2">File saat ini: <?php echo htmlspecialchars($alumni_data['foto']); ?></p>
                <?php endif; ?>
                <input type="file" name="foto" id="foto" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Alumni
                </button>
                <a href="alumni_list.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; // Sertakan bagian footer HTML ?>
