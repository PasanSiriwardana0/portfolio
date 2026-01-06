<?php
// setup_database.php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - Visit Tracker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; padding: 10px; background: #e8f5e8; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #ffebee; border-radius: 5px; margin: 10px 0; }
        .sql { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; margin: 20px 0; }
        .btn { background: #4CAF50; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Setup for Visit Tracker</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
            // Database credentials from form
            $host = $_POST['host'] ?? 'localhost';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $database = $_POST['database'] ?? '';
            
            // Try to connect
            $conn = new mysqli($host, $username, $password, $database);
            
            if ($conn->connect_error) {
                echo '<div class="error">Connection failed: ' . $conn->connect_error . '</div>';
            } else {
                echo '<div class="success">✓ Connected to database successfully!</div>';
                
                // SQL to create table
                $sql = "CREATE TABLE IF NOT EXISTS visits (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    ip_address VARCHAR(45) NOT NULL,
                    user_agent TEXT,
                    page_url VARCHAR(500),
                    referrer VARCHAR(500),
                    visit_time DATETIME NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_time (visit_time),
                    INDEX idx_ip (ip_address)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                
                if ($conn->query($sql) === TRUE) {
                    echo '<div class="success">✓ Table "visits" created successfully!</div>';
                    
                    // Update config.php with credentials
                    $config_content = '<?php
// config.php - Auto-generated
$host = \'' . addslashes($host) . '\';
$username = \'' . addslashes($username) . '\';
$password = \'' . addslashes($password) . '\';
$database = \'' . addslashes($database) . '\';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>';
                    
                    if (file_put_contents('../../config.php', $config_content)) {
                        echo '<div class="success">✓ config.php updated with database credentials!</div>';
                        echo '<p>You can now delete this setup file for security.</p>';
                    } else {
                        echo '<div class="error">✗ Could not update config.php. Please check file permissions.</div>';
                    }
                } else {
                    echo '<div class="error">✗ Error creating table: ' . $conn->error . '</div>';
                }
                
                $conn->close();
            }
        }
        ?>
        
        <form method="POST">
            <h3>Enter Database Credentials:</h3>
            <p>
                <label>Host:</label><br>
                <input type="text" name="host" value="localhost" required style="width: 100%; padding: 8px; margin: 5px 0;">
            </p>
            <p>
                <label>Username:</label><br>
                <input type="text" name="username" required style="width: 100%; padding: 8px; margin: 5px 0;">
            </p>
            <p>
                <label>Password:</label><br>
                <input type="password" name="password" style="width: 100%; padding: 8px; margin: 5px 0;">
            </p>
            <p>
                <label>Database Name:</label><br>
                <input type="text" name="database" required style="width: 100%; padding: 8px; margin: 5px 0;">
            </p>
            <button type="submit" name="setup" class="btn">Setup Database</button>
        </form>
        
        <hr>
        <h3>Manual SQL Setup:</h3>
        <p>If the auto-setup fails, run this SQL in phpMyAdmin:</p>
        <div class="sql">
CREATE TABLE visits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    page_url VARCHAR(500),
    referrer VARCHAR(500),
    visit_time DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_time (visit_time),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        </div>
    </div>
</body>
</html>