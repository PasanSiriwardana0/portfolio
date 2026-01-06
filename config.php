<?php
// config.php
// Enable error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
$host = 'localhost';
$username = 'u269378_tQfByEipbb';
$password = 'bJOvZdxG..cz7V0uJHGTKUzq';
$database = 's269378_PSTechDB';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to safely log visits (with duplicate prevention)
function logVisit($conn) {
    // Get visitor information
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $page_url = $_SERVER['REQUEST_URI'] ?? '/';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $visit_time = date('Y-m-d H:i:s');
    
    // Clean the inputs
    $ip_address = filter_var($ip_address, FILTER_VALIDATE_IP) ? $ip_address : '0.0.0.0';
    $user_agent = substr($user_agent, 0, 500);
    $page_url = substr($page_url, 0, 500);
    $referrer = substr($referrer, 0, 500);
    
    // Prevent counting the same visitor within 30 minutes
    $thirty_min_ago = date('Y-m-d H:i:s', strtotime('-30 minutes'));
    
    // Check if this IP visited recently
    $check_sql = "SELECT id FROM visits 
                  WHERE ip_address = ? 
                  AND visit_time > ? 
                  LIMIT 1";
    
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("ss", $ip_address, $thirty_min_ago);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        $recent_visit = $check_stmt->num_rows > 0;
        $check_stmt->close();
        
        // Only log if no recent visit
        if (!$recent_visit) {
            $insert_sql = "INSERT INTO visits (ip_address, user_agent, page_url, referrer, visit_time) 
                           VALUES (?, ?, ?, ?, ?)";
            
            if ($stmt = $conn->prepare($insert_sql)) {
                $stmt->bind_param("sssss", $ip_address, $user_agent, $page_url, $referrer, $visit_time);
                $stmt->execute();
                $stmt->close();
                return true;
            }
        }
    }
    
    return false;
}

// Function to get total visits
function getTotalVisits($conn) {
    $sql = "SELECT COUNT(*) as total FROM visits";
    if ($result = $conn->query($sql)) {
        $row = $result->fetch_assoc();
        $result->free();
        return $row['total'] ?? 0;
    }
    return 0;
}

// Function to get today's visits
function getTodayVisits($conn) {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as today_count FROM visits WHERE DATE(visit_time) = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['today_count'] ?? 0;
    }
    return 0;
}

// Function to get unique visitors
function getUniqueVisitors($conn) {
    $sql = "SELECT COUNT(DISTINCT ip_address) as unique_visitors FROM visits";
    if ($result = $conn->query($sql)) {
        $row = $result->fetch_assoc();
        $result->free();
        return $row['unique_visitors'] ?? 0;
    }
    return 0;
}

// Function to get top pages
function getTopPages($conn, $limit = 10) {
    $sql = "SELECT page_url, COUNT(*) as visits 
            FROM visits 
            GROUP BY page_url 
            ORDER BY visits DESC 
            LIMIT ?";
    
    $pages = [];
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $pages[] = $row;
        }
        
        $stmt->close();
    }
    
    return $pages;
}

// Function to get recent visits
function getRecentVisits($conn, $limit = 20) {
    $sql = "SELECT ip_address, user_agent, page_url, referrer, visit_time 
            FROM visits 
            ORDER BY visit_time DESC 
            LIMIT ?";
    
    $visits = [];
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $visits[] = $row;
        }
        
        $stmt->close();
    }
    
    return $visits;
}

// Function to get visits by day (last 30 days)
function getVisitsByDay($conn, $days = 30) {
    $sql = "SELECT 
                DATE(visit_time) as visit_date,
                COUNT(*) as visits,
                COUNT(DISTINCT ip_address) as unique_visitors
            FROM visits 
            WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(visit_time)
            ORDER BY visit_date DESC";
    
    $stats = [];
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }
        
        $stmt->close();
    }
    
    return $stats;
}

// Function to get browser statistics
function getBrowserStats($conn) {
    $sql = "SELECT 
                CASE 
                    WHEN user_agent LIKE '%Chrome%' THEN 'Chrome'
                    WHEN user_agent LIKE '%Firefox%' THEN 'Firefox'
                    WHEN user_agent LIKE '%Safari%' AND user_agent NOT LIKE '%Chrome%' THEN 'Safari'
                    WHEN user_agent LIKE '%Edge%' THEN 'Edge'
                    WHEN user_agent LIKE '%Opera%' THEN 'Opera'
                    WHEN user_agent LIKE '%MSIE%' OR user_agent LIKE '%Trident%' THEN 'Internet Explorer'
                    ELSE 'Other/Unknown'
                END as browser,
                COUNT(*) as count
            FROM visits 
            GROUP BY browser
            ORDER BY count DESC";
    
    $browsers = [];
    
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $browsers[] = $row;
        }
        $result->free();
    }
    
    return $browsers;
}

// Function to get hourly statistics for today
function getHourlyStats($conn) {
    $sql = "SELECT 
                HOUR(visit_time) as hour,
                COUNT(*) as visits
            FROM visits 
            WHERE DATE(visit_time) = CURDATE()
            GROUP BY HOUR(visit_time)
            ORDER BY hour";
    
    $hours = array_fill(0, 24, 0);
    
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $hours[$row['hour']] = $row['visits'];
        }
        $result->free();
    }
    
    return $hours;
}

// Helper function to format time difference
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return $diff . ' second' . ($diff != 1 ? 's' : '') . ' ago';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes != 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
    } else {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months != 1 ? 's' : '') . ' ago';
    }
}

// Helper function to get country from IP (approximate)
function getCountryFromIP($ip) {
    // Simple IP-based country detection (you can implement more complex logic)
    $private_ips = ['127.', '10.', '192.168.', '172.16.', '172.31.'];
    
    foreach ($private_ips as $private_ip) {
        if (strpos($ip, $private_ip) === 0) {
            return 'Local';
        }
    }
    
    return 'Unknown';
}
?>