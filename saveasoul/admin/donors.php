<?php
require_once '../config.php';
$page_title = "Manage Donors";
$active = "donors";

$message = "";

if (isset($_GET['action']) && isset($_GET['id']))
{
    $id = intval($_GET['id']);

    if ($_GET['action'] == 'verify')
    {
        $note = isset($_GET['note']) ? trim($_GET['note']) : "";
        $stmt = mysqli_prepare($conn, "UPDATE users SET is_verified = 1, verification_note = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $note, $id);
        mysqli_stmt_execute($stmt);
    }
    elseif ($_GET['action'] == 'unverify')
    {
        mysqli_query($conn, "UPDATE users SET is_verified = 0, verification_note = NULL WHERE user_id = $id");
    }
    elseif ($_GET['action'] == 'log_donation')
    {
        // Find this donor's record (donor_id + blood_group + eligibility needed)
        $donor_result = mysqli_query($conn, "SELECT donor_id, blood_group, next_eligible_date FROM donors WHERE user_id = $id");
        $donor_row = mysqli_fetch_assoc($donor_result);

        if ($donor_row)
        {
            $donor_id = $donor_row['donor_id'];
            $blood_group = $donor_row['blood_group'];
            $next_eligible = $donor_row['next_eligible_date'];
            $units_donated = 1; // standard single-unit donation

            // SERVER-SIDE GUARD: block logging a donation if this donor already
            // donated recently and hasn't reached their 3-month eligibility date yet.
            // This is the actual fix for the "double-click adds units twice" bug -
            // the check happens here regardless of what the UI shows or allows.
            if ($next_eligible !== null && $next_eligible > date('Y-m-d'))
            {
                $_SESSION['flash_error'] = "This donor is not eligible to donate again until " . date('d M Y', strtotime($next_eligible)) . ". No units were added.";
            }
            else
            {
                // 1. Log the donation record
                $stmt = mysqli_prepare($conn, "INSERT INTO donations (donor_id, donation_date, units) VALUES (?, CURDATE(), ?)");
                mysqli_stmt_bind_param($stmt, "ii", $donor_id, $units_donated);
                mysqli_stmt_execute($stmt);

                // 2. Update donor's last donation date and next eligible date (3 month gap)
                mysqli_query($conn, "UPDATE donors SET last_donation_date = CURDATE(), next_eligible_date = DATE_ADD(CURDATE(), INTERVAL 3 MONTH) WHERE donor_id = $donor_id");

                // 3. Increase blood bank inventory for this blood group, since a real unit was collected
                $stmt2 = mysqli_prepare($conn, "UPDATE blood_inventory SET units_available = units_available + ? WHERE blood_group = ?");
                mysqli_stmt_bind_param($stmt2, "is", $units_donated, $blood_group);
                mysqli_stmt_execute($stmt2);

                $_SESSION['flash_message'] = "Donation logged successfully. $blood_group inventory increased by $units_donated unit. Donor is now ineligible until " . date('d M Y', strtotime('+3 months')) . ".";
            }
        }
    }

    header("Location: donors.php");
    exit();
}

$error = "";
if (isset($_SESSION['flash_message']))
{
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
if (isset($_SESSION['flash_error']))
{
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

$result = mysqli_query($conn, "SELECT u.user_id, u.full_name, u.email, u.phone, u.city, u.is_verified, u.verification_note,
                                d.blood_group, d.age, d.next_eligible_date, d.is_available
                                FROM users u
                                LEFT JOIN donors d ON u.user_id = d.user_id
                                WHERE u.role = 'donor'
                                ORDER BY u.is_verified ASC, u.created_at DESC");

include 'admin_header.php';
?>

<h2 class="section-title" style="text-align:left;">Manage Donors</h2>
<p class="section-subtitle" style="text-align:left;">Verify donor accounts and log donations</p>

<?php if ($message != ""): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error != ""): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Blood Group</th>
            <th>City</th>
            <th>Contact</th>
            <th>Verified</th>
            <th>Verification Note</th>
            <th>Next Eligible</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo $row['blood_group']; ?></td>
                <td><?php echo htmlspecialchars($row['city']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']) . " / " . htmlspecialchars($row['email']); ?></td>
                <td>
                    <?php if ($row['is_verified']): ?>
                        <span class="badge badge-available">Verified</span>
                    <?php else: ?>
                        <span class="badge badge-unavailable">Pending</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['is_verified'] && !empty($row['verification_note'])): ?>
                        <span class="verification-note" title="<?php echo htmlspecialchars($row['verification_note']); ?>">
                            📝 <?php echo htmlspecialchars(mb_strimwidth($row['verification_note'], 0, 30, '...')); ?>
                        </span>
                    <?php elseif ($row['is_verified']): ?>
                        <span class="field-hint">No note added</span>
                    <?php else: ?>
                        <span class="field-hint">&mdash;</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                        if ($row['next_eligible_date'] && $row['next_eligible_date'] > date('Y-m-d'))
                        {
                            echo date('d M Y', strtotime($row['next_eligible_date']));
                        }
                        else
                        {
                            echo "Eligible Now";
                        }
                    ?>
                </td>
                <td>
                    <?php if ($row['is_verified']): ?>
                        <a href="donors.php?action=unverify&id=<?php echo $row['user_id']; ?>">Unverify</a> |
                    <?php else: ?>
                        <a href="#" onclick="verifyWithNote(<?php echo $row['user_id']; ?>); return false;">Verify</a> |
                    <?php endif; ?>
                    <?php
                        $is_eligible = !($row['next_eligible_date'] && $row['next_eligible_date'] > date('Y-m-d'));
                    ?>
                    <?php if ($is_eligible): ?>
                        <a href="donors.php?action=log_donation&id=<?php echo $row['user_id']; ?>" onclick="return confirm('Log a new donation for this donor? This will add 1 unit to the ' + '<?php echo $row['blood_group']; ?>' + ' inventory.');">Log Donation</a>
                    <?php else: ?>
                        <span class="field-hint" title="Not eligible again until <?php echo date('d M Y', strtotime($row['next_eligible_date'])); ?>">Log Donation (not eligible yet)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
function verifyWithNote(userId) {
    var note = prompt("How did you verify this donor? (optional — leave blank if you prefer not to add one)", "");
    if (note === null) {
        // user clicked Cancel, do nothing
        return;
    }
    var url = "donors.php?action=verify&id=" + userId + "&note=" + encodeURIComponent(note);
    window.location.href = url;
}
</script>

<?php include 'admin_footer.php'; ?>
