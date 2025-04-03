<?php
require '../config/db.php';

$result = $conn->query("SELECT rating, COUNT(*) as count FROM feedback GROUP BY rating");

$data = [0, 0, 0, 0, 0];
while ($row = $result->fetch_assoc()) {
    $data[$row['rating'] - 1] = $row['count'];
}

echo json_encode($data);
?>
