<?php
// visits-tracker.php - JSON-based tracker (no database needed)
$data_file = __DIR__ . '/visits_data.json';

// Initialize or load data
if (!file_exists($data_file)) {
    $data = [
        'total_visits' => 0,
        'unique_visits' => 0,
        'daily_visits' => [],
        'last_reset' => date('Y-m-d')
    ];
} else {
    $data = json_decode(file_get_contents($data_file), true);
}

// Track visit
$today = date('Y-m-d');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Initialize daily counter if not exists
if (!isset($data['daily_visits'][$today])) {
    $data['daily_visits'][$today] = [
        'count' => 0,
        'ips' => []
    ];
}

// Check if IP visited today
if (!in_array($ip, $data['daily_visits'][$today]['ips'])) {
    $data['daily_visits'][$today]['ips'][] = $ip;
    $data['unique_visits']++;
}

// Increment counters
$data['daily_visits'][$today]['count']++;
$data['total_visits']++;

// Clean old data (keep last 30 days)
$thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
foreach ($data['daily_visits'] as $date => $day_data) {
    if ($date < $thirty_days_ago) {
        unset($data['daily_visits'][$date]);
    }
}

// Save data
file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT));

// Return stats
return [
    'total' => $data['total_visits'],
    'unique' => $data['unique_visits'],
    'today' => $data['daily_visits'][$today]['count'] ?? 0
];
?>