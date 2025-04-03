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

// Handle sorting and filtering
$sort_by = $_GET['sort_by'] ?? 'submitted_at'; // Default to sorting by date
$filter_name = $_GET['filter_name'] ?? '';
$filter_rating = $_GET['filter_rating'] ?? '';

// SQL query with dynamic sorting and filtering
$sql = "SELECT * FROM feedback WHERE 1";

if ($filter_name != '') {
    $sql .= " AND name LIKE '%" . $filter_name . "%'";
}
if ($filter_rating != '') {
    $sql .= " AND rating = " . $filter_rating;
}

$sql .= " ORDER BY " . $sort_by . " DESC";  // Sort by selected field

$result = $conn->query($sql);

$ratings = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$feedbacks = [];

while ($row = $result->fetch_assoc()) {
    $ratings[$row['rating']]++;
    $feedbacks[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Feedback</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <h2>Feedback Dashboard</h2>

        <!-- Simplified Sorting and Filtering Controls -->
        <div class="controls">
            <form method="GET" class="filters-form">
                <!-- Sorting and Filter Inputs in a Small Compact Layout -->
                <select name="sort_by" class="control-select">
                    <option value="submitted_at" <?php echo ($sort_by == 'submitted_at') ? 'selected' : ''; ?>>Date</option>
                    <option value="rating" <?php echo ($sort_by == 'rating') ? 'selected' : ''; ?>>Rating</option>
                </select>
                <input type="text" name="filter_name" placeholder="Filter by Name" value="<?php echo $filter_name; ?>" class="control-input">
                <select name="filter_rating" class="control-select">
                    <option value="">All Ratings</option>
                    <option value="1" <?php echo ($filter_rating == '1') ? 'selected' : ''; ?>>1</option>
                    <option value="2" <?php echo ($filter_rating == '2') ? 'selected' : ''; ?>>2</option>
                    <option value="3" <?php echo ($filter_rating == '3') ? 'selected' : ''; ?>>3</option>
                    <option value="4" <?php echo ($filter_rating == '4') ? 'selected' : ''; ?>>4</option>
                    <option value="5" <?php echo ($filter_rating == '5') ? 'selected' : ''; ?>>5</option>
                </select>
                <button type="submit" class="control-button">Apply</button>
            </form>
        </div>

        <div class="admin-layout">
            <!-- Left: Feedback Table -->
            <div class="feedback-table">
                <h3>All Feedback</h3>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                    <?php foreach ($feedbacks as $row): ?>
                        <tr class="<?php echo ($row['rating'] >= 4) ? 'rating-positive' : (($row['rating'] <= 2) ? 'rating-negative' : ''); ?>">
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['rating']; ?></td>
                            <td><?php echo $row['comment']; ?></td>
                            <td><?php echo $row['submitted_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Right: Graph -->
            <div class="chart-container">
                <h3>Rating Distribution</h3>
                <canvas id="feedbackChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('feedbackChart').getContext('2d');
        var feedbackChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
                datasets: [{
                    label: 'Number of Feedbacks',
                    data: [<?php echo implode(",", $ratings); ?>],
                    backgroundColor: ['#e74c3c', '#e67e22', '#f1c40f', '#2ecc71', '#3498db']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>

<?php $conn->close(); ?>
