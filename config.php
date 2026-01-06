<?php
// config.php - Netlify Compatible Version

// Database configuration
$host = 'localhost';
$username = 'u269378_tQfByEipbb';
$password = 'bJOvZdxG..cz7V0uJHGTKUzq';
$database = 's269378_PSTechDB';

// Error handling for Netlify
function handleError($message) {
    error_log("Visit Tracker Error: " . $message);
    // Don't die in production, just log
    return false;
}

// Create database connection
try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        // Don't die, just log and continue without database
        error_log("Database connection failed: " . $conn->connect_error);
        $conn = null;
    }
} catch (Exception $e) {
    error_log("Database exception: " . $e->getMessage());
    $conn = null;
}

// Function to log visits
function logVisit($conn) {
    if (!$conn) {
        return false; // No database connection
    }
    
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $page_url = $_SERVER['REQUEST_URI'] ?? '/';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $visit_time = date('Y-m-d H:i:s');
        
        // Clean inputs
        $ip_address = filter_var($ip_address, FILTER_VALIDATE_IP) ? $ip_address : '0.0.0.0';
        $user_agent = substr($user_agent, 0, 500);
        $page_url = substr($page_url, 0, 500);
        $referrer = substr($referrer, 0, 500);
        
        // Insert visit
        $sql = "INSERT INTO visits (ip_address, user_agent, page_url, referrer, visit_time) 
                VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $ip_address, $user_agent, $page_url, $referrer, $visit_time);
            $stmt->execute();
            $stmt->close();
            return true;
        }
    } catch (Exception $e) {
        error_log("Log visit error: " . $e->getMessage());
    }
    
    return false;
}

// Function to get visit statistics (fallback to file if DB fails)
function getVisitStats($conn) {
    if (!$conn) {
        return getFallbackStats();
    }
    
    try {
        $stats = [
            'total_visits' => 0,
            'today_visits' => 0,
            'unique_visitors' => 0
        ];
        
        // Get total visits
        $result = $conn->query("SELECT COUNT(*) as total FROM visits");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_visits'] = $row['total'] ?? 0;
            $result->free();
        }
        
        // Get today's visits
        $today = date('Y-m-d');
        $stmt = $conn->prepare("SELECT COUNT(*) as today FROM visits WHERE DATE(visit_time) = ?");
        if ($stmt) {
            $stmt->bind_param("s", $today);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stats['today_visits'] = $row['today'] ?? 0;
            $stmt->close();
        }
        
        // Get unique visitors
        $result = $conn->query("SELECT COUNT(DISTINCT ip_address) as unique_visitors FROM visits");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['unique_visitors'] = $row['unique_visitors'] ?? 0;
            $result->free();
        }
        
        return $stats;
    } catch (Exception $e) {
        error_log("Get stats error: " . $e->getMessage());
        return getFallbackStats();
    }
}

// Fallback statistics using JSON file
function getFallbackStats() {
    $file = __DIR__ . '/visits_data.json';
    
    if (!file_exists($file)) {
        return [
            'total_visits' => 0,
            'today_visits' => 0,
            'unique_visitors' => 0,
            'using_fallback' => true
        ];
    }
    
    $data = json_decode(file_get_contents($file), true);
    return $data ?? [
        'total_visits' => 0,
        'today_visits' => 0,
        'unique_visitors' => 0,
        'using_fallback' => true
    ];
}

// Update fallback stats
function updateFallbackStats() {
    $file = __DIR__ . '/visits_data.json';
    $stats = getFallbackStats();
    
    // Increment counters
    $stats['total_visits'] = ($stats['total_visits'] ?? 0) + 1;
    $today = date('Y-m-d');
    
    if (!isset($stats['daily'][$today])) {
        $stats['daily'][$today] = 0;
    }
    $stats['daily'][$today] += 1;
    $stats['today_visits'] = $stats['daily'][$today];
    
    // Estimate unique visitors (very basic)
    $stats['unique_visitors'] = min($stats['total_visits'], $stats['total_visits'] * 0.7);
    
    file_put_contents($file, json_encode($stats, JSON_PRETTY_PRINT));
    return $stats;
}
?>