<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle sorting and filtering
$sort_by = $_GET['sort_by'] ?? 'submitted_at';
$filter_name = $_GET['filter_name'] ?? '';
$filter_rating = $_GET['filter_rating'] ?? '';

$sql = "SELECT * FROM feedback WHERE 1";
if ($filter_name != '') {
    $sql .= " AND name LIKE '%" . $conn->real_escape_string($filter_name) . "%'";
}
if ($filter_rating != '') {
    $sql .= " AND rating = " . intval($filter_rating);
}
$sql .= " ORDER BY " . $conn->real_escape_string($sort_by) . " DESC";

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
    <title>Admin - Feedback</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <h2>Feedback Dashboard</h2>

        <div class="controls">
            <form method="GET" class="filters-form">
                <select name="sort_by" class="control-select">
                    <option value="submitted_at" <?php echo ($sort_by == 'submitted_at') ? 'selected' : ''; ?>>Date</option>
                    <option value="rating" <?php echo ($sort_by == 'rating') ? 'selected' : ''; ?>>Rating</option>
                </select>
                <input type="text" name="filter_name" placeholder="Filter by Name" value="<?php echo htmlspecialchars($filter_name); ?>" class="control-input">
                <select name="filter_rating" class="control-select">
                    <option value="">All Ratings</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($filter_rating == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="control-button">Apply</button>
            </form>
        </div>

        <div class="admin-layout">
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
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['rating']; ?></td>
                            <td><?php echo htmlspecialchars($row['comment']); ?></td>
                            <td><?php echo $row['submitted_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

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
