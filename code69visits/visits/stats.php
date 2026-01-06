<?php
// code69visits/visits/stats.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../../config.php';

$stats = [
    'total_visits' => getTotalVisits($conn),
    'today_visits' => getTodayVisits($conn),
    'unique_visitors' => getUniqueVisitors($conn),
    'server_time' => date('Y-m-d H:i:s'),
    'status' => 'success'
];

echo json_encode($stats, JSON_PRETTY_PRINT);
$conn->close();
?>