<?php
require_once 'config.php';
require_once 'includes/shared_data.php';
$page_title = "Login";
$base_path = "";

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email == "" || $password == "")
    {
        $error = "Please enter both email and password.";
    }
    elseif (!is_valid_email_format($email))
    {
        $error = "Please enter a valid email address.";
    }
    else
    {
        $stmt = mysqli_prepare($conn, "SELECT user_id, full_name, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1)
        {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password']))
            {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'admin')
                {
                    header("Location: admin/dashboard.php");
                }
                else
                {
                    header("Location: dashboard.php");
                }
                exit();
            }
            else
            {
                $error = "Incorrect password. Please try again.";
            }
        }
        else
        {
            $error = "No account found with this email.";
        }
    }
}

include 'includes/header.php';
?>

<div class="form-wrapper">
    <h2>Login to SaveASoul</h2>

    <?php if ($error != ""): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="name@example.com"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-submit">Login</button>
    </form>

    <p class="form-footer">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>
