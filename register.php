<?php
include("includes/db.php"); // your PDO connection
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $mobile = trim($_POST['mobile']);
    $role = $_POST['role'];

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $check->bindParam(':email', $email);
    $check->execute();

    if ($check->rowCount() > 0) {
        $error = "Email already registered.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, mobile, role) 
                                VALUES (:fullname, :email, :password, :mobile, :role)");
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':mobile', $mobile);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            // Get the last inserted user ID
            $user_id = $conn->lastInsertId();

            if ($role == 'job_seeker') {
                $stmt2 = $conn->prepare("INSERT INTO job_seekers (job_seeker_id, fullname, email, mobile) 
                                         VALUES (:job_seeker_id, :fullname, :email, :mobile)");
                $stmt2->bindParam(':job_seeker_id', $user_id);
                $stmt2->bindParam(':fullname', $fullname);
                $stmt2->bindParam(':email', $email);
                $stmt2->bindParam(':mobile', $mobile);
                $stmt2->execute();
            }elseif ($role == 'recruiter') {
                $stmt3 = $conn->prepare("INSERT INTO recruiters (user_id, fullname, email, mobile) 
                                         VALUES (:user_id, :fullname, :email, :mobile)");
                $stmt3->bindParam(':user_id', $user_id);
                $stmt3->bindParam(':fullname', $fullname);
                $stmt3->bindParam(':email', $email);
                $stmt3->bindParam(':mobile', $mobile);
                $stmt3->execute();
            }

            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>


<?php include("includes/header.php"); ?>

<div class="register-wrapper">
    <div class="register-box">
        <h2>Registration form</h2>
        <p class="subtitle">Register to apply for jobs of your choice all over the world</p>

        <?php if ($success): ?>
            <div class="success-msg"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Full name*</label>
            <input type="text" name="fullname" required placeholder="Enter your full name">

            <label>Email ID*</label>
            <input type="email" name="email" required placeholder="Enter your email id">
            <small>Job notifications will be sent to this email id</small>

            <label>Password*</label>
            <input type="password" name="password" required minlength="6" placeholder="(Minimum 6 characters)">
            <small>Remember your password</small>

            <label>Mobile number*</label>
            <input type="text" name="mobile" required placeholder="Enter your mobile number">
            <small>Recruiters will contact you on this number</small>
            <label>User Role*</label>
<div class="role-options">
  <label class="role-radio">
    <input type="radio" name="role" value="job_seeker" checked>
    <span class="custom-circle"></span> Job Seeker
  </label>
  <label class="role-radio">
    <input type="radio" name="role" value="recruiter">
    <span class="custom-circle"></span> Recruiter
  </label>
</div>



            <label class="checkbox-line">
                <input type="checkbox" checked>
                Send me important updates & promotions via SMS, email, and WhatsApp
            </label>

            <button type="submit" class="register-btn">Register now</button>

            <div class="or-line">or signup with</div>
            <div class="social-icons">
                <img src="assets/img/google.svg">
                <img src="assets/img/facebook.svg">
                <img src="assets/img/linkedin.svg">
            </div>
        </form>
    </div>
</div>


