<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$role = $_POST['role'];

// Kiểm tra email đã tồn tại chưa
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Email trùng, chuyển về lại kèm lỗi
    header("Location: admin_users.php?error=Email đã tồn tại");
    exit;
}

// Thêm người dùng mới
$stmt = $conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $role);
$stmt->execute();

header("Location: admin_users.php?success=Thêm thành công");
exit;
