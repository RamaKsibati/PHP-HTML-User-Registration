<?php
require 'db.php';
$users = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <script>
        function toggleSuspend(userId) {
            fetch(`toggle_suspend.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
    <table>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td id="status-<?php echo $user['id']; ?>"><?php echo $user['status']; ?></td>
                <td><button onclick="toggleSuspend(<?php echo $user['id']; ?>)">Toggle Suspend</button></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
