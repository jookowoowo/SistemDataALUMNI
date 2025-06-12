<?php
    session_start();
    include "koneksi.php";

    $username = $_POST['username'];
    $password = $_POST['password'];
    $hakAkses = $_POST['hakAkses'];

    $sql = mysqli_query($koneksi, "SELECT * FROM admin WHERE status='$hakAkses' AND username='$username' AND password='$password'");
    $jumlahData = mysqli_num_rows($sql);
    
    if($hakAkses == 1 && $jumlahData > 0){ // angka 1 untuk admin
        $_SESSION['username'] = $username;
        $_SESSION['hak'] = $hakAkses;
        header("location:dashboardAdmin.php");
    }else if($hakAkses == 2 && $jumlahData > 0){ // angka 2 untuk alumni
        $_SESSION['username'] = $username;
        $_SESSION['hak'] = $hakAkses;
        header("location:dashboardAlumni.php");
    }else if($hakAkses == 3 && $jumlahData > 0){ // angka 3 untuk siswa
        $_SESSION['username'] = $username;
        $_SESSION['hak'] = $hakAkses;
        header("location:dashboardSiswa.php");
    }else{
        header("location:login.php");
    }
?>