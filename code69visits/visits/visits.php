<?php
// code69visits/visits/visits.php
session_start();
require_once '../../config.php';

// Log this visit to the dashboard
logVisit($conn);

// Get all statistics
$total_visits = getTotalVisits($conn);
$today_visits = getTodayVisits($conn);
$unique_visitors = getUniqueVisitors($conn);
$top_pages = getTopPages($conn, 15);
$recent_visits = getRecentVisits($conn, 50);
$daily_stats = getVisitsByDay($conn, 30);
$browser_stats = getBrowserStats($conn);
$hourly_stats = getHourlyStats($conn);

// Calculate additional stats
$average_daily = $total_visits > 0 ? round($total_visits / max(1, count($daily_stats))) : 0;
$busiest_hour = array_search(max($hourly_stats), $hourly_stats);
$busiest_hour_count = max($hourly_stats);

// Get first visit date
$first_visit_sql = "SELECT MIN(visit_time) as first_visit FROM visits";
$first_visit_result = $conn->query($first_visit_sql);
$first_visit_row = $first_visit_result->fetch_assoc();
$first_visit_date = $first_visit_row['first_visit'] ?? date('Y-m-d');
$days_since_first = floor((time() - strtotime($first_visit_date)) / (60 * 60 * 24));
$days_since_first = max(1, $days_since_first);
$avg_daily_total = round($total_visits / $days_since_first);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Visit Analytics - PST DEV Portfolio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-black: #000000;
            --accent-red: #ff0000;
            --neon-red: #ff0033;
            --dark-red: #ae0000;
            --cyber-blue: #00f3ff;
            --matrix-green: #00ff41;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0a;
            color: #fff;
            line-height: 1.6;
            padding: 20px;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 0, 51, 0.03) 0%, transparent 25%),
                radial-gradient(circle at 90% 80%, rgba(174, 0, 0, 0.03) 0%, transparent 25%),
                linear-gradient(to bottom, #000, #0a0a0a);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            border: 1px solid rgba(255, 0, 51, 0.1);
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--neon-red), transparent);
        }
        
        h1 {
            color: var(--neon-red);
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 0 15px rgba(255, 0, 51, 0.5);
            font-family: 'Orbitron', monospace;
            letter-spacing: 2px;
        }
        
        .subtitle {
            color: #aaa;
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 0, 51, 0.3);
            box-shadow: 0 15px 40px rgba(255, 0, 51, 0.2);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--neon-red), var(--accent-red));
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: var(--neon-red);
            margin-bottom: 15px;
            text-shadow: 0 0 10px rgba(255, 0, 51, 0.5);
        }
        
        .stat-value {
            font-size: 3.5rem;
            font-weight: bold;
            color: white;
            margin-bottom: 10px;
            font-family: 'Orbitron', monospace;
            text-shadow: 0 0 10px rgba(255, 0, 51, 0.3);
        }
        
        .stat-label {
            color: #aaa;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }
        
        .stat-subtext {
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        
        /* Sections */
        .section {
            margin-bottom: 50px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .section-title {
            color: var(--neon-red);
            font-size: 1.8rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(255, 0, 51, 0.2);
            font-family: 'Orbitron', monospace;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section-title i {
            font-size: 1.5rem;
        }
        
        /* Tables */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.3);
            min-width: 800px;
        }
        
        th {
            background: rgba(255, 0, 51, 0.15);
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid rgba(255, 0, 51, 0.3);
            font-family: 'Orbitron', monospace;
            font-size: 0.9rem;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #ddd;
        }
        
        tr:hover {
            background: rgba(255, 0, 51, 0.05);
        }
        
        .ip-address {
            font-family: 'Courier New', monospace;
            color: var(--matrix-green);
            font-weight: bold;
        }
        
        .time-ago {
            color: #aaa;
            font-size: 0.9rem;
            font-style: italic;
        }
        
        .user-agent {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #bbb;
        }
        
        .page-url {
            color: var(--cyber-blue);
            text-decoration: none;
            font-family: 'Courier New', monospace;
        }
        
        .page-url:hover {
            text-decoration: underline;
            color: #66ccff;
        }
        
        .referrer {
            color: #ffcc66;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Charts */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .chart-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .chart-title {
            color: #ddd;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-family: 'Orbitron', monospace;
        }
        
        .bar-chart {
            display: flex;
            align-items: flex-end;
            height: 200px;
            gap: 10px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        
        .bar {
            flex: 1;
            background: linear-gradient(to top, var(--neon-red), var(--accent-red));
            border-radius: 4px 4px 0 0;
            position: relative;
            transition: height 0.3s ease;
        }
        
        .bar:hover {
            opacity: 0.8;
        }
        
        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            color: #888;
            font-size: 0.8rem;
        }
        
        .bar-value {
            position: absolute;
            top: -25px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            color: #666;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 30px;
            background: linear-gradient(90deg, var(--accent-red), var(--dark-red));
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Orbitron', monospace;
            letter-spacing: 1px;
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 0, 51, 0.4);
        }
        
        .action-btn.secondary {
            background: transparent;
            border: 2px solid var(--neon-red);
            color: var(--neon-red);
        }
        
        .action-btn.secondary:hover {
            background: rgba(255, 0, 51, 0.1);
        }
        
        /* Empty State */
        .empty-message {
            text-align: center;
            color: #888;
            padding: 60px;
            font-style: italic;
            font-size: 1.1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                padding: 25px 15px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .section {
                padding: 20px;
            }
            
            .footer-links {
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--dark-red), var(--neon-red));
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--neon-red), var(--accent-red));
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--neon-red);
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header fade-in">
            <h1><i class="fas fa-chart-line"></i> ANALYTICS DASHBOARD</h1>
            <p class="subtitle">PST DEV Portfolio • Real-time Visitor Statistics</p>
            
            <div class="footer-links">
                <a href="/" class="action-btn">
                    <i class="fas fa-home"></i> Back to Portfolio
                </a>
                <a href="javascript:location.reload()" class="action-btn secondary">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </a>
                <a href="stats.php" class="action-btn secondary">
                    <i class="fas fa-download"></i> Export Data
                </a>
            </div>
        </div>
        
        <!-- Main Stats -->
        <div class="stats-grid fade-in">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_visits); ?></div>
                <div class="stat-label">Total Visits</div>
                <div class="stat-subtext">Since <?php echo date('M j, Y', strtotime($first_visit_date)); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($unique_visitors); ?></div>
                <div class="stat-label">Unique Visitors</div>
                <div class="stat-subtext">Based on IP addresses</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-value"><?php echo number_format($today_visits); ?></div>
                <div class="stat-label">Today's Visits</div>
                <div class="stat-subtext">As of <?php echo date('g:i A'); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-value"><?php echo number_format($avg_daily_total); ?></div>
                <div class="stat-label">Avg. Daily</div>
                <div class="stat-subtext">Over <?php echo $days_since_first; ?> days</div>
            </div>
        </div>
        
        <!-- Daily Statistics Chart -->
        <div class="section fade-in">
            <h2 class="section-title"><i class="fas fa-calendar-alt"></i> LAST 30 DAYS TREND</h2>
            <?php if (!empty($daily_stats)): ?>
                <div class="bar-chart">
                    <?php 
                    $max_visits = max(array_column($daily_stats, 'visits'));
                    $recent_days = array_slice($daily_stats, 0, 14);
                    
                    foreach (array_reverse($recent_days) as $day): 
                        $height = $max_visits > 0 ? ($day['visits'] / $max_visits * 100) : 0;
                        $date_label = date('M j', strtotime($day['visit_date']));
                    ?>
                    <div class="bar" style="height: <?php echo $height; ?>%" title="<?php echo $date_label; ?>: <?php echo $day['visits']; ?> visits">
                        <div class="bar-value"><?php echo $day['visits']; ?></div>
                        <div class="bar-label"><?php echo $date_label; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="text-align: center; margin-top: 20px; color: #888;">
                    <i class="fas fa-info-circle"></i> Showing last 14 days of 30-day period
                </div>
            <?php else: ?>
                <div class="empty-message">No daily statistics available yet.</div>
            <?php endif; ?>
        </div>
        
        <!-- Top Pages -->
        <div class="section fade-in">
            <h2 class="section-title"><i class="fas fa-star"></i> MOST VISITED PAGES</h2>
            <?php if (!empty($top_pages)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Page URL</th>
                                <th>Visits</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_pages as $page): 
                                $percentage = $total_visits > 0 ? ($page['visits'] / $total_visits * 100) : 0;
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo htmlspecialchars($page['page_url']); ?>" class="page-url" target="_blank">
                                        <?php echo htmlspecialchars($page['page_url']); ?>
                                    </a>
                                </td>
                                <td style="font-weight: bold;"><?php echo number_format($page['visits']); ?></td>
                                <td>
                                    <div style="background: rgba(255, 0, 51, 0.1); border-radius: 4px; padding: 5px;">
                                        <div style="background: linear-gradient(90deg, var(--neon-red), var(--accent-red)); 
                                                    width: <?php echo min($percentage, 100); ?>%; 
                                                    height: 8px; border-radius: 4px;"></div>
                                        <span style="margin-left: 10px;"><?php echo number_format($percentage, 1); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-message">No page visit data available yet.</div>
            <?php endif; ?>
        </div>
        
        <!-- Recent Visits -->
        <div class="section fade-in">
            <h2 class="section-title"><i class="fas fa-history"></i> RECENT VISITS (LAST 50)</h2>
            <?php if (!empty($recent_visits)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>IP Address</th>
                                <th>Page</th>
                                <th>Browser</th>
                                <th>Referrer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_visits as $visit): 
                                $time_ago = timeAgo($visit['visit_time']);
                                $browser = 'Unknown';
                                if (strpos($visit['user_agent'], 'Chrome') !== false) $browser = 'Chrome';
                                elseif (strpos($visit['user_agent'], 'Firefox') !== false) $browser = 'Firefox';
                                elseif (strpos($visit['user_agent'], 'Safari') !== false && strpos($visit['user_agent'], 'Chrome') === false) $browser = 'Safari';
                                elseif (strpos($visit['user_agent'], 'Edge') !== false) $browser = 'Edge';
                                elseif (strpos($visit['user_agent'], 'Opera') !== false) $browser = 'Opera';
                            ?>
                            <tr>
                                <td>
                                    <div><?php echo date('M j, g:i A', strtotime($visit['visit_time'])); ?></div>
                                    <div class="time-ago"><?php echo $time_ago; ?></div>
                                </td>
                                <td class="ip-address"><?php echo htmlspecialchars($visit['ip_address']); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($visit['page_url']); ?>" class="page-url" target="_blank">
                                        <?php 
                                        $page_name = parse_url($visit['page_url'], PHP_URL_PATH);
                                        echo $page_name ? htmlspecialchars(basename($page_name)) : 'Home';
                                        ?>
                                    </a>
                                </td>
                                <td>
                                    <span style="color: #4dff88;"><?php echo $browser; ?></span>
                                </td>
                                <td class="referrer" title="<?php echo htmlspecialchars($visit['referrer']); ?>">
                                    <?php if (!empty($visit['referrer'])): ?>
                                        <?php 
                                        $ref_domain = parse_url($visit['referrer'], PHP_URL_HOST);
                                        echo $ref_domain ? htmlspecialchars($ref_domain) : 'Direct';
                                        ?>
                                    <?php else: ?>
                                        <span style="color: #888;">Direct</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-message">No recent visits recorded.</div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="footer fade-in">
            <p><i class="fas fa-server"></i> System Status: <span style="color: #4dff88;">ACTIVE</span> • Last updated: <?php echo date('F j, Y \\a\\t g:i A'); ?></p>
            <p><i class="fas fa-shield-alt"></i> Privacy: IP addresses are logged for analytics only and are not shared</p>
            <p><i class="fas fa-code"></i> PST DEV Portfolio Analytics v1.0 • © <?php echo date('Y'); ?> All rights reserved</p>
            
            <div class="footer-links">
                <a href="javascript:window.print()" class="action-btn secondary">
                    <i class="fas fa-print"></i> Print Report
                </a>
                <a href="clear_cache.php" class="action-btn secondary" onclick="return confirm('Clear cache? This will not delete visit data.')">
                    <i class="fas fa-trash-alt"></i> Clear Cache
                </a>
            </div>
        </div>
    </div>
    
    <script>
    // Auto-refresh data every 60 seconds
    setTimeout(() => {
        location.reload();
    }, 60000);
    
    // Add click handlers for better UX
    document.querySelectorAll('.page-url').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    });
    
    // Add animation to stats cards on hover
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Show loading state when refreshing
    document.querySelector('a[href="javascript:location.reload()"]').addEventListener('click', function(e) {
        this.innerHTML = '<span class="loading"></span> Refreshing...';
    });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>