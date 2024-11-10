<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user data, including admin status, from the database
$sql = "SELECT username, email, image, is_admin FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user is an admin
$is_admin = $user['is_admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <!-- Display Profile Image if available -->
    <?php if (!empty($user['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image" width="150">
    <?php else: ?>
        <p>No profile image uploaded.</p>
    <?php endif; ?>

    <!-- Display link to the Admin Panel if user is an admin -->
    <?php if ($is_admin == 1): ?>
        <p><a href="admin_panel.php">Go to Admin Panel</a></p>
    <?php endif; ?>

    <!-- Option to update profile or change password -->
    <p><a href="update_profile.php">Update Profile or Change Password</a></p>

    <!-- Logout button -->
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>

