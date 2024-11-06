<?php
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation checks
    if (!preg_match("/^[a-zA-Z0-9]{4,}$/", $username)) {
        $message = "Username must be at least 4 characters long and contain only alphanumeric characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{10,}$/", $password)) {
        $message = "Password must be at least 10 characters long, include an uppercase letter, a lowercase letter, and a number.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Image upload
        $image_path = NULL;
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "uploads/";
            $image_path = $target_dir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }

        $sql = "INSERT INTO users (username, email, password, image) VALUES (:username, :email, :password, :image)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([':username' => $username, ':email' => $email, ':password' => $password_hash, ':image' => $image_path])) {
            $message = "Successfully registered!";
            header("Location: login.php");
        } else {
            $message = "Registration failed. Username or email may already exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <p><?php echo $message; ?></p>
    <form method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>

        <label for="image">Profile Image:</label>
        <input type="file" name="image"><br>

        <button type="submit">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Login here</a>.</p>
</body>
</html>