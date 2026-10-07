<?php
require_once 'config.php';
require_once 'includes/shared_data.php';
$page_title = "Request Blood";
$base_path = "";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!isset($_SESSION['user_id']))
    {
        $error = "Please login to raise a blood request.";
    }
    else
    {
        $blood_group = $_POST['blood_group'];
        $units_needed = intval($_POST['units_needed']);
        $urgency = $_POST['urgency'];
        $hospital_name = trim($_POST['hospital_name']);
        $city = trim($_POST['city']);
        $user_id = $_SESSION['user_id'];

        if ($hospital_name == "" || $city == "")
        {
            $error = "Please fill in the hospital name and city.";
        }
        elseif ($units_needed < 1 || $units_needed > 20)
        {
            $error = "Units needed must be between 1 and 20.";
        }
        else
        {
            $stmt = mysqli_prepare($conn, "INSERT INTO requests (user_id, blood_group, units_needed, urgency, hospital_name, city) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isisss", $user_id, $blood_group, $units_needed, $urgency, $hospital_name, $city);
            mysqli_stmt_execute($stmt);

            $success = "Your blood request has been submitted. Matching donors will be notified.";
        }
    }
}

include 'includes/header.php';
?>

<div class="form-wrapper">
    <h2>Request Blood</h2>

    <?php if ($error != ""): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?> <?php if (!isset($_SESSION['user_id'])): ?><a href="login.php">Login</a><?php endif; ?></div>
    <?php endif; ?>

    <?php if ($success != ""): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="request_blood.php" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label>Blood Group Needed</label>
                <select name="blood_group" required>
                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                        <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Units Needed</label>
                <input type="number" name="units_needed" min="1" max="20" required>
            </div>
        </div>

        <div class="form-group">
            <label>Urgency Level</label>
            <select name="urgency" required>
                <option value="Normal">Normal</option>
                <option value="Urgent">Urgent</option>
                <option value="Critical">Critical</option>
            </select>
        </div>

        <div class="form-group">
            <label>Hospital Name</label>
            <input type="text" name="hospital_name" list="hospitalList" required placeholder="Start typing hospital name">
            <datalist id="hospitalList">
                <?php foreach ($common_hospitals as $h): ?>
                    <option value="<?php echo htmlspecialchars($h); ?>">
                <?php endforeach; ?>
            </datalist>
            <small class="field-hint">Pick from the list or type your hospital if not listed</small>
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" list="cityList" required placeholder="Start typing your city">
            <datalist id="cityList">
                <?php foreach ($indian_cities as $c): ?>
                    <option value="<?php echo htmlspecialchars($c); ?>">
                <?php endforeach; ?>
            </datalist>
        </div>

        <button type="submit" class="btn-submit">Submit Request</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
