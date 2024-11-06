<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update password if provided
    if (!empty($_POST['new_password'])) {
        $new_password = $_POST['new_password'];
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([':password' => $hashed_password, ':id' => $_SESSION['user_id']])) {
            $message .= "Password updated successfully. ";
        } else {
            $message .= "Failed to update password. ";
        }
    }

    // Update self-introduction
    if (!empty($_POST['self_introduction'])) {
        $self_introduction = $_POST['self_introduction'];
        $sql = "UPDATE users SET self_introduction = :self_introduction WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':self_introduction' => $self_introduction, ':id' => $_SESSION['user_id']]);
    }

    // Handle image upload
    if (!empty($_FILES["new_image"]["name"])) {
        $target_dir = "uploads/";
        $new_image = $target_dir . basename($_FILES["new_image"]["name"]);
        if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $new_image)) {
            $sql = "UPDATE users SET image = :image WHERE id = :id";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([':image' => $new_image, ':id' => $_SESSION['user_id']])) {
                $message .= "Profile image updated successfully. ";
            } else {
                $message .= "Failed to update profile image in the database.";
            }
        } else {
            $message .= "Failed to upload new image.";
        }
    }

    // Fetch updated user data to regenerate static profile page
    $sql = "SELECT username, self_introduction FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generate or update the static profile page
    $username = $user['username'];
    $profileHtml = "<html><head><title>$username's Profile</title></head><body>";
    $profileHtml .= "<h1>$username's Profile</h1>";
    $profileHtml .= "<p>Introduction: " . htmlspecialchars($user['self_introduction']) . "</p>";
    if (isset($new_image)) {
        $profileHtml .= "<img src='../$new_image' alt='Profile Image'>";
    }
    $profileHtml .= "</body></html>";
    file_put_contents("profiles/$username.html", $profileHtml);
}

$sql = "SELECT username, email, image, self_introduction FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <script src="<script src="https://cdn.tiny.cloud/1/o7yheknty8a77o0mfdruq6jh8ih9dl6wwk0jnnk08h5ik45k/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>"
    <script>
      tinymce.init({
        selector: '#self_introduction',
        menubar: false,
        plugins: 'link',
        toolbar: 'undo redo | bold italic underline | link'
      });
    </script>
</head>
<body>
    <h1>Update Profile</h1>
    <p><?php echo $message; ?></p>

    <?php if (!empty($user['image'])): ?>
        <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image" width="150"><br>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password"><br>

        <label for="new_image">New Profile Image:</label>
        <input type="file" name="new_image" accept="image/*"><br>

        <label for="self_introduction">Self Introduction:</label>
        <textarea id="self_introduction" name="self_introduction"><?php echo htmlspecialchars($user['self_introduction']); ?></textarea><br>

        <button type="submit">Update Profile</button>
    </form>

    <p><a href="home.php">Back to Home</a></p>
</body>
</html>
