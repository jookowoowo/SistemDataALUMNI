<?php
// alumni_create.php

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape user inputs for security
    // Mengambil id_admin dari sesi yang login
    $id_admin = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; 

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $id_jurusan = mysqli_real_escape_string($conn, $_POST['id_jurusan']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $foto = ''; // Default empty foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if($check !== false) {
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $error_message .= "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.<br>";
            } else {
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $foto = basename($_FILES["foto"]["name"]);
                } else {
                    $error_message .= "Maaf, terjadi kesalahan saat mengunggah file Anda.<br>";
                }
            }
        } else {
            $error_message .= "File bukan gambar.<br>";
        }
    }

    // Attempt insert query if no file upload errors
    if (empty($error_message)) {
        // Hash password sebelum menyimpan ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO alumni (id_admin, id_jurusan, nama, jenis_kelamin, email, alamat, foto, password) 
                VALUES ('$id_admin', '$id_jurusan', '$nama', '$jenis_kelamin', '$email', '$alamat', '$foto', '$hashed_password')";
        
        if (mysqli_query($conn, $sql)) {
            // Redirect setelah sukses menambah alumni
            header("Location: alumni_list.php?status=success_add_alumni");
            exit(); 
        } else {
            $error_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Ambil data jurusan yang tersedia dari database untuk dropdown
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
    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Tambah Alumni Baru</h2>

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

    <div class="bg-white shadow-lg rounded-lg p-6">
        <form action="alumni_create.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
                <input type="text" name="nama" id="nama" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
            </div>
            <div class="mb-4">
                <label for="jenis_kelamin" class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin:</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="L" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="mb-4">
                <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat:</label>
                <textarea name="alamat" id="alamat" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="4" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
            </div>
            <div class="mb-4">
                <label for="id_jurusan" class="block text-gray-700 text-sm font-bold mb-2">Jurusan:</label>
                <select name="id_jurusan" id="id_jurusan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Pilih Jurusan</option>
                    <?php 
                    // Reset pointer result set jika sudah pernah digunakan
                    if (mysqli_num_rows($jurusan_result) > 0) {
                        mysqli_data_seek($jurusan_result, 0); 
                    }
                    while($row_jurusan = mysqli_fetch_assoc($jurusan_result)): ?>
                        <option value="<?php echo htmlspecialchars($row_jurusan['id_jurusan']); ?>" <?php echo (isset($_POST['id_jurusan']) && $_POST['id_jurusan'] == $row_jurusan['id_jurusan']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row_jurusan['nama_jurusan']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="foto" class="block text-gray-700 text-sm font-bold mb-2">Foto (Opsional):</label>
                <input type="file" name="foto" id="foto" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Tambahkan Alumni
                </button>
                <a href="alumni_list.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; // Sertakan bagian footer HTML ?>
