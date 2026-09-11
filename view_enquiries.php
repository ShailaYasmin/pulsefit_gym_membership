<?php
/**
 * view_enquiries.php
 * ---------------------------------------------------
 * A simple staff-only page to view messages submitted
 * through the Contact Us form.
 *
 * NOTE: This uses a basic shared password as a placeholder.
 * If a teammate builds full user authentication with roles,
 * replace the check below with: if ($_SESSION['role'] !== 'admin')
 *
 * Individual contribution: Trishna
 * Feature: Access control (read-only admin view) + CRUD (Read)
 * ---------------------------------------------------
 */

session_start();
require_once "db_connect.php";

$staff_password = "pulsefit2025"; // placeholder — replace with real auth once available
$authenticated = isset($_SESSION['staff_authenticated']) && $_SESSION['staff_authenticated'] === true;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff_password'])) {
    if ($_POST['staff_password'] === $staff_password) {
        $_SESSION['staff_authenticated'] = true;
        $authenticated = true;
    } else {
        $login_error = "Incorrect password.";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: view_enquiries.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff | Contact Enquiries | PulseFit Gym</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="css/style.css">
<style>
  .enq-table { width:100%; border-collapse: collapse; margin-top: 20px; }
  .enq-table th, .enq-table td { text-align:left; padding:10px 14px; border-bottom:1px solid #ddd; font-size:0.9rem; }
  .enq-table th { background:#f4f4f4; }
  .login-box { max-width:340px; margin:80px auto; padding:30px; border:1px solid #ddd; border-radius:10px; }
</style>
</head>
<body>
<div class="container" style="padding:40px 20px;">

<?php if (!$authenticated): ?>
    <div class="login-box">
        <h2>Staff Login</h2>
        <p style="font-size:0.9rem;">Enter the staff password to view enquiries.</p>
        <?php if (!empty($login_error)): ?>
            <p style="color:#a12626;"><?= htmlspecialchars($login_error) ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="password" name="staff_password" placeholder="Staff password" required style="width:100%;padding:10px;margin:12px 0;">
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>
    </div>

<?php else: ?>
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Contact Enquiries</h2>
        <a href="?logout=1" class="btn btn-ghost btn-sm">Log Out</a>
    </div>

    <?php
    // Read all enquiries, most recent first
    $stmt = $pdo->query("SELECT * FROM contact_enquiries ORDER BY submitted_at DESC");
    $enquiries = $stmt->fetchAll();
    ?>

    <?php if (empty($enquiries)): ?>
        <p>No enquiries have been submitted yet.</p>
    <?php else: ?>
        <table class="enq-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enquiries as $row): ?>
                <tr>
                    <td><?= htmlspecialchars(date("d M Y, g:ia", strtotime($row['submitted_at']))) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['enquiry_type']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

</div>
</body>
</html>
