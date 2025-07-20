<?php
$servername = "localhost";
$username = "sa";
$password = "123456";
$dbname = "ban_ve_du_lich";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
