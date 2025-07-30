<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM users WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý người dùng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f5f5f5; }
    .container { margin-top: 40px; }
    .table th { background: #007bff; color: white; }
  </style>
</head>
<body>
<div class="container">
  <h2 class="text-center mb-3">Quản lý người dùng</h2>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php elseif (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
  <?php endif; ?>

  <!-- Form tìm kiếm -->
  <form method="GET" class="row mb-3">
    <div class="col-md-10">
      <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Tìm kiếm</button>
    </div>
  </form>

  <!-- Nút thêm -->
  <div class="mb-3 text-end">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Thêm người dùng</button>
  </div>

  <!-- Bảng người dùng -->
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>ID</th><th>Tên</th><th>Email</th><th>Quyền</th><th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['role']) ?></td>
        <td>
          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Sửa</button>
          <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
        </td>
      </tr>

      <!-- Modal sửa -->
      <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
          <form method="POST" action="update_user.php">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Sửa người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="mb-3">
                  <label>Họ tên</label>
                  <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                </div>
                <div class="mb-3">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
                </div>
                <div class="mb-3">
                  <label>Quyền</label>
                  <select name="role" class="form-control">
                    <option value="user" <?= $row['role'] == 'user' ? 'selected' : '' ?>>Người dùng</option>
                    <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Quản trị</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php } ?>
    </tbody>
  </table>
</div>

<!-- Modal thêm người dùng -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="add_user.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thêm người dùng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Quyền</label>
            <select name="role" class="form-control">
              <option value="user">Người dùng</option>
              <option value="admin">Quản trị</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Thêm</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
