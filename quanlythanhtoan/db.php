<?php
$conn = new mysqli("localhost", "root", "", "ban_ve_du_lich");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
