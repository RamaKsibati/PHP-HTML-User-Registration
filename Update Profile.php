<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle password update
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([':password' => $new_password, ':id' => $_SESSION['user_id']])) {
            $message .= "Password updated successfully. ";
        } else {
            $errorInfo = $stmt->errorInfo();
            $message .= "Failed to update password: " . $errorInfo[2] . ". ";
        }
    }

    // Handle image upload
    if (!empty($_FILES["new_image"]["name"])) {
        $target_dir = "uploads/";
        $new_image = $target_dir . basename($_FILES["new_image"]["name"]);
        
        // Check if file was uploaded and move it
        if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $new_image)) {
            $sql = "UPDATE users SET image = :image WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([':image' => $new_image, ':id' => $_SESSION['user_id']])) {
                $message .= "Profile image updated successfully.";
            } else {
                $errorInfo = $stmt->errorInfo();
                $message .= "Failed to update profile image in the database: " . $errorInfo[2] . ".";
            }
        } else {
            $message .= "Failed to upload new image.";
        }
    }
}

// Fetch current image
$sql = "SELECT image FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
</head>
<body>
    <h1>Update Profile</h1>
    <p><?php echo $message; ?></p>

    <?php if (!empty($user['image'])): ?>
        <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image" width="150"><br>
    <?php else: ?>
        <p>No profile image uploaded.</p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password"><br>
        
        <label for="new_image">New Profile Image:</label>
        <input type="file" name="new_image" accept="image/*"><br>
        
        <button type="submit">Update</button>
    </form>
    <p><a href="home.php">Back to Home</a></p>
</body>
</html>
