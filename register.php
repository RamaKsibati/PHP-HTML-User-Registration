<?php
require 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $self_introduction = $_POST['self_introduction'];

    // Password confirmation and hashing
    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user data into the database
        $sql = "INSERT INTO users (username, email, password, self_introduction) VALUES (:username, :email, :password, :self_introduction)";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':self_introduction' => $self_introduction
        ])) {
            // Generate a static profile page
            $profileHtml = "<html><head><title>$username's Profile</title></head><body>";
            $profileHtml .= "<h1>$username's Profile</h1>";
            $profileHtml .= "<p>Introduction: " . htmlspecialchars($self_introduction) . "</p>";
            $profileHtml .= "</body></html>";
            file_put_contents("profiles/$username.html", $profileHtml);

            $message = "Registration successful! <a href='login.php'>Login here</a>.";
        } else {
            $message = "Error during registration.";
        }
    } else {
        $message = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <script src="https://cdn.tiny.cloud/1/o7yheknty8a77o0mfdruq6jh8ih9dl6wwk0jnnk08h5ik45k/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
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
    <h1>Register</h1>
    <p><?php echo $message; ?></p>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>

        <label for="self_introduction">Self Introduction:</label>
        <textarea id="self_introduction" name="self_introduction"></textarea><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
