<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';
// Retrieve user data from the database
$sql = "SELECT username, email, image FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <?php if ($user['image']): ?>
        <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image" width="150">
    <?php else: ?>
        <p>No profile image uploaded.</p>
    <?php endif; ?>

    <a href="Update Profile.php">Change Password or Image</a><br>
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
