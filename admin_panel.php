<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Check if the logged-in user is an admin
$sql = "SELECT is_admin FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $_SESSION['user_id']]);
$is_admin = $stmt->fetchColumn();

if ($is_admin != 1) {
    // Redirect non-admin users to the homepage or an error page
    header("Location: home.php");
    exit();
}

// Fetch all users for display in the admin panel
$users = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <script>
        // Function to send AJAX request to toggle user suspension
        function toggleSuspend(userId) {
            fetch('toggle_suspend.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status text in the table
                    document.getElementById(`status-${userId}`).innerText = data.newStatus;
                } else {
                    alert("Error toggling user status");
                }
            });
        }
    </script>
</head>
<body>
    <h1>Admin Panel</h1>
    <p>Welcome to the admin panel. Only admin users can see this content.</p>

    <!-- User Table -->
    <table border="1" cellpadding="10">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td id="status-<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['status']); ?></td>
                <td>
                    <button onclick="toggleSuspend(<?php echo $user['id']; ?>)">
                        <?php echo $user['status'] === 'active' ? 'Suspend' : 'Unsuspend'; ?>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
