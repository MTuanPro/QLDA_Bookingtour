<?php
include 'db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM users WHERE id = $id");

header("Location: admin_users.php");
exit;
