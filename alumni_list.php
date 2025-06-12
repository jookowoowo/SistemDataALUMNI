<?php
// alumni_list.php

// Pastikan koneksi database dan session dimulai sebelum cek akses
include 'includes/koneksi.php';
session_start();

// Cek jika pengguna belum login atau bukan admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Redirect ke halaman login dengan pesan
    header("Location: login.php?message=Anda tidak memiliki akses ke halaman ini.");
    exit(); // Penting: Hentikan eksekusi script setelah redirect
}

// Lanjutkan dengan logika halaman jika admin terautentikasi
include 'includes/header.php'; // Menggunakan header yang sudah dimodifikasi

// Query untuk mengambil data alumni beserta nama jurusan dan admin
$sql_alumni = "SELECT a.id_alumni, a.nama, a.email, a.jenis_kelamin, a.alamat, a.foto, 
                j.nama_jurusan, ad.username AS admin_username
                FROM alumni a
                LEFT JOIN jurusan j ON a.id_jurusan = j.id_jurusan
                LEFT JOIN admin ad ON a.id_admin = ad.id_admin
                ORDER BY a.nama ASC";
$result_alumni = mysqli_query($conn, $sql_alumni);

if (!$result_alumni) {
    echo "<div class='container mx-auto p-4 mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
    echo "Error mengambil data alumni: " . mysqli_error($conn);
    echo "</div>";
    exit();
}

// Query untuk mengambil data siswa beserta nama jurusan dan admin
$sql_siswa = "SELECT s.id_siswa, s.nama, s.email, s.jenis_kelamin, s.kelas, s.alamat, s.foto,
              j.nama_jurusan, ad.username AS admin_username
              FROM siswa s
              LEFT JOIN jurusan j ON s.id_jurusan = j.id_jurusan
              LEFT JOIN admin ad ON s.id_admin = ad.id_admin
              ORDER BY s.nama ASC";
$result_siswa = mysqli_query($conn, $sql_siswa);

if (!$result_siswa) {
    echo "<div class='container mx-auto p-4 mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
    echo "Error mengambil data siswa: " . mysqli_error($conn);
    echo "</div>";
    exit();
}
?>

<div class="container mx-auto p-4 mt-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Daftar Alumni</h2>
    <div class="flex justify-end mb-4">
        <a href="alumni_create.php" class="btn btn-primary bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
            <i class="fas fa-plus mr-2"></i>Tambah Alumni Baru
        </a>
    </div>

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-6 card">
        <?php if (mysqli_num_rows($result_alumni) > 0): ?>
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        No.
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Foto
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jenis Kelamin
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jurusan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row_alumni = mysqli_fetch_assoc($result_alumni)): ?>
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo $no++; ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php if (!empty($row_alumni['foto'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row_alumni['foto']); ?>" alt="Foto Alumni" class="w-12 h-12 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo htmlspecialchars($row_alumni['nama']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo htmlspecialchars($row_alumni['email']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo ($row_alumni['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo htmlspecialchars($row_alumni['nama_jurusan']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="ganti_password_alumni.php?id=<?php echo $row_alumni['id_alumni']; ?>" class="text-yellow-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Ganti Password
                        </a>
                        <a href="alumni_edit.php?id=<?php echo $row_alumni['id_alumni']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="alumni_delete.php?id=<?php echo $row_alumni['id_alumni']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="text-center text-gray-600 py-8">Belum ada data alumni.</p>
        <?php endif; ?>
    </div>

    <!-- Section for Daftar User/Siswa -->
    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center mt-12">Daftar User atau Siswa</h2>
    <div class="flex justify-end mb-4">
        <a href="siswa_create.php" class="btn btn-primary bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
            <i class="fas fa-plus mr-2"></i>Tambah Siswa Baru
        </a>
    </div>

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-6 card">
        <?php if (mysqli_num_rows($result_siswa) > 0): ?>
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        No.
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Foto
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jenis Kelamin
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Kelas
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jurusan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $no_siswa = 1; while($row_siswa = mysqli_fetch_assoc($result_siswa)): ?>
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo $no_siswa++; ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php if (!empty($row_siswa['foto'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row_siswa['foto']); ?>" alt="Foto Siswa" class="w-12 h-12 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo htmlspecialchars($row_siswa['nama']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo htmlspecialchars($row_siswa['email']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo ($row_siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo htmlspecialchars($row_siswa['kelas']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <?php echo htmlspecialchars($row_siswa['nama_jurusan']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="ganti_password_siswa.php?id=<?php echo $row_siswa['id_siswa']; ?>" class="text-yellow-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Ganti Password
                        </a>
                        <a href="siswa_edit.php?id=<?php echo $row_siswa['id_siswa']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="siswa_delete.php?id=<?php echo $row_siswa['id_siswa']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="text-center text-gray-600 py-8">Belum ada data siswa.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
