<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include "config/database.php";
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        echo $username;

        $query = "SELECT * FROM admin WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        $jumlah = mysqli_num_rows($result);
        if($jumlah > 0){
            echo "berhasil";
        }else{
            $query = "SELECT * FROM alumni WHERE email = '$username'";
            $result2 = mysqli_query($conn, $query);
            $jumlah = mysqli_num_rows($result2);
            if($jumlah){
                echo "berhasil";
            }else{
                echo "kedua gagal";
            }
        }
    
    }
?>