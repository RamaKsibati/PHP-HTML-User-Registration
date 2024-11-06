<?php
require 'db.php';

$limit = 5; // Number of users per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch users with pagination
$sql = "SELECT * FROM users LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPages = ceil($totalUsers / $limit);
?>

<!DOCTYPE html>
<html>
<head><title>User List</title></head>
<body>
    <h1>User List</h1>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?php echo htmlspecialchars($user['username']); ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Pagination Links -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="user_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</body>
</html>
