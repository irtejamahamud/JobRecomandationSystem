<?php
session_start();
include '../includes/db.php'; // Assuming PDO connection

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Hash the password first (because stored in DB as MD5)
    $password_hashed = md5($password);

    // Prepare statement
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username AND password = :password");
    $stmt->execute([
        ':username' => $username,
        ':password' => $password_hashed
    ]);

    if ($stmt->rowCount() == 1) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        // Redirect to admin dashboard
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - NextWorkX</title>
  <link rel="stylesheet" href="../assets/css/admin_dashboard.css"> <!-- Correct CSS now -->
</head>
<body>

<div class="admin-login-container">
  <h2>Admin Login</h2>

  <?php if (!empty($error)) : ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form action="" method="post" class="admin-login-form">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="login">Login</button>
  </form>
</div>

</body>
</html>
