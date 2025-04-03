<?php
session_start();
require '../config/db.php';

$admin_user = "admin";
$admin_pass = "password123";

if ($_POST['username'] == $admin_user && $_POST['password'] == $admin_pass) {
    $_SESSION['loggedin'] = true;
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
?>
    <div class="admin-login">
        <h2>Admin Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username">
            <input type="password" name="password" placeholder="Enter Password">
            <button type="submit">Login</button>
        </form>
    </div>
<?php
    exit();
}

$sql = "SELECT * FROM feedback ORDER BY submitted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Feedback</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Feedback Submissions</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Date</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="<?php echo ($row['rating'] >= 4) ? 'rating-positive' : (($row['rating'] <= 2) ? 'rating-negative' : ''); ?>">
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['rating']; ?></td>
                    <td><?php echo $row['comment']; ?></td>
                    <td><?php echo $row['submitted_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
