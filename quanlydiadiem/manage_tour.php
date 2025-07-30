<?php
$conn = new mysqli("localhost", "root", "", "du_lich");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$where = $search ? "WHERE ten_tour LIKE '%$search%' OR dia_diem LIKE '%$search%'" : '';

$total = $conn->query("SELECT COUNT(*) AS total FROM tours $where")->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

$tours = $conn->query("SELECT * FROM tours $where ORDER BY id DESC LIMIT $limit OFFSET $offset");

if (isset($_POST['save'])) {
    $id = $_POST['id'] ?? null;
    $ten = $_POST['ten_tour'];
    $dia_diem = $_POST['dia_diem'];
    $gia = $_POST['gia'];
    $thoi_gian = $_POST['thoi_gian'];
    $mo_ta = $_POST['mo_ta'];

    $hinh_anh = $_FILES['hinh_anh']['name'] ?? '';
    $hinh_tmp = $_FILES['hinh_anh']['tmp_name'] ?? '';

    if ($hinh_anh) {
        $path = 'uploads/' . basename($hinh_anh);
        move_uploaded_file($hinh_tmp, $path);
    }

    if ($id) {
        if ($hinh_anh) {
            $stmt = $conn->prepare("UPDATE tours SET ten_tour=?, dia_diem=?, gia=?, thoi_gian=?, mo_ta=?, hinh_anh=? WHERE id=?");
            $stmt->bind_param("ssdsssi", $ten, $dia_diem, $gia, $thoi_gian, $mo_ta, $hinh_anh, $id);
        } else {
            $stmt = $conn->prepare("UPDATE tours SET ten_tour=?, dia_diem=?, gia=?, thoi_gian=?, mo_ta=? WHERE id=?");
            $stmt->bind_param("ssdssi", $ten, $dia_diem, $gia, $thoi_gian, $mo_ta, $id);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO tours (ten_tour, dia_diem, gia, thoi_gian, mo_ta, hinh_anh) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsss", $ten, $dia_diem, $gia, $thoi_gian, $mo_ta, $hinh_anh);
    }
    $stmt->execute();
    header("Location: manage_tour.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tours WHERE id = $id");
    header("Location: manage_tour.php");
    exit;
}

$tour_edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM tours WHERE id = $id");
    $tour_edit = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tour Du Lịch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Trang Quản Lý Tour Du Lịch</h2>

    <form class="row g-3 mb-3" method="GET">
        <div class="col-md-10">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Tìm theo tên hoặc địa điểm...">
        </div>
        <div class="col-md-2 text-end">
            <button type="submit" class="btn btn-secondary">Tìm kiếm</button>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white"><?= $tour_edit ? "Sửa Tour" : "Thêm Tour" ?></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($tour_edit): ?>
                    <input type="hidden" name="id" value="<?= $tour_edit['id'] ?>">
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Tên Tour</label>
                        <input type="text" name="ten_tour" class="form-control" value="<?= $tour_edit['ten_tour'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label>Địa điểm</label>
                        <input type="text" name="dia_diem" class="form-control" value="<?= $tour_edit['dia_diem'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Giá (VND)</label>
                        <input type="number" name="gia" class="form-control" value="<?= $tour_edit['gia'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Thời gian</label>
                        <input type="text" name="thoi_gian" class="form-control" value="<?= $tour_edit['thoi_gian'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Hình ảnh</label>
                        <input type="file" name="hinh_anh" class="form-control">
                        <?php if (!empty($tour_edit['hinh_anh'])): ?>
                            <img src="uploads/<?= $tour_edit['hinh_anh'] ?>" width="100" class="mt-2">
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" class="form-control"><?= $tour_edit['mo_ta'] ?? '' ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="save" class="btn btn-success"><?= $tour_edit ? "Cập nhật" : "Thêm Tour" ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">Danh Sách Tour</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên Tour</th>
                        <th>Địa điểm</th>
                        <th>Giá</th>
                        <th>Thời gian</th>
                        <th>Mô tả</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $tours->fetch_assoc()): ?>
                    <tr>
                        <td><?php if ($row['hinh_anh']): ?><img src="uploads/<?= $row['hinh_anh'] ?>" width="80"><?php endif; ?></td>
                        <td><?= htmlspecialchars($row['ten_tour']) ?></td>
                        <td><?= htmlspecialchars($row['dia_diem']) ?></td>
                        <td><?= number_format($row['gia'], 0, ',', '.') ?> đ</td>
                        <td><?= $row['thoi_gian'] ?></td>
                        <td><?= $row['mo_ta'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Xóa tour này?')" class="btn btn-danger btn-sm">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
</body>
</html>
