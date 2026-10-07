<?php
require_once 'config.php';
require_once 'includes/shared_data.php';
$page_title = "Register";
$base_path = "";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $phone = trim($_POST['phone']);
    $city = trim($_POST['city']);

    // --- Server-side validation (never trust the browser alone) ---
    if ($full_name == "" || $email == "" || $password == "" || $phone == "" || $city == "")
    {
        $error = "Please fill in all required fields.";
    }
    elseif (!is_valid_email_format($email))
    {
        $error = "Please enter a valid email address (e.g. name@example.com).";
    }
    elseif (!is_valid_phone($phone))
    {
        $error = "Phone number must be exactly 10 digits, numbers only.";
    }
    elseif (strlen($password) < 6)
    {
        $error = "Password must be at least 6 characters long.";
    }
    elseif ($role == 'donor' && (intval($_POST['age']) < 18 || intval($_POST['age']) > 65))
    {
        $error = "Donor age must be between 18 and 65.";
    }
    elseif ($role == 'donor' && intval($_POST['weight']) < 45)
    {
        $error = "Donor weight must be at least 45 kg to safely donate blood.";
    }
    else
    {
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        $check_result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($check_result) > 0)
        {
            $error = "An account with this email already exists.";
        }
        else
        {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password, role, phone, city) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $full_name, $email, $hashed_password, $role, $phone, $city);
            mysqli_stmt_execute($stmt);
            $new_user_id = mysqli_insert_id($conn);

            if ($role == 'donor')
            {
                $blood_group = $_POST['blood_group'];
                $age = intval($_POST['age']);
                $weight = intval($_POST['weight']);

                $stmt2 = mysqli_prepare($conn, "INSERT INTO donors (user_id, blood_group, age, weight) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, "isii", $new_user_id, $blood_group, $age, $weight);
                mysqli_stmt_execute($stmt2);
            }

            $success = "Registration successful! You can now log in.";
        }
    }
}

include 'includes/header.php';
?>

<div class="form-wrapper">
    <h2>Create Your Account</h2>

    <?php if ($error != ""): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success != ""): ?>
        <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Login here</a></div>
    <?php endif; ?>

    <form method="POST" action="register.php" id="registerForm" novalidate>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required minlength="2" maxlength="100"
                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required
                   placeholder="name@example.com"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <small class="field-hint">Must be a valid email address (name@domain.com)</small>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required minlength="6">
            <small class="field-hint">At least 6 characters</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" required pattern="[0-9]{10}" maxlength="10"
                       inputmode="numeric" placeholder="10 digit mobile number"
                       title="Enter exactly 10 digits, numbers only"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                <small class="field-hint">Exactly 10 digits, numbers only</small>
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" list="cityList" required
                       placeholder="Start typing your city"
                       value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                <datalist id="cityList">
                    <?php foreach ($indian_cities as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-group">
            <label>Register As</label>
            <select name="role" id="roleSelect" onchange="toggleDonorFields()">
                <option value="donor">Donor</option>
                <option value="recipient">Recipient / Hospital Representative</option>
            </select>
        </div>

        <div id="donorFields">
            <div class="form-row">
                <div class="form-group">
                    <label>Blood Group</label>
                    <select name="blood_group">
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" min="18" max="65" placeholder="18-65">
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="number" name="weight" min="45" placeholder="Min 45">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">Register</button>
    </form>

    <p class="form-footer">Already have an account? <a href="login.php">Login</a></p>
</div>

<script>
function toggleDonorFields() {
    var role = document.getElementById("roleSelect").value;
    var donorFields = document.getElementById("donorFields");
    donorFields.style.display = (role === "donor") ? "block" : "none";
}

// Extra client-side safety net: block non-digit typing in phone field
document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    }

    var form = document.getElementById('registerForm');
    form.addEventListener('submit', function (e) {
        var phone = phoneInput ? phoneInput.value : '';
        var email = document.querySelector('input[name="email"]').value;
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!/^[0-9]{10}$/.test(phone)) {
            alert('Phone number must be exactly 10 digits.');
            e.preventDefault();
            return false;
        }
        if (!emailPattern.test(email)) {
            alert('Please enter a valid email address.');
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
