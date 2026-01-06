<?php
// code69visits/visits/visits.php
if (file_exists('../../config.php')) {
    require_once '../../config.php';
}

// Get statistics
if (isset($conn) && $conn) {
    $stats = getVisitStats($conn);
} else {
    $stats = getFallbackStats();
    // Update fallback stats for this visit
    updateFallbackStats();
    $stats = getFallbackStats();
}

$total_visits = $stats['total_visits'] ?? 0;
$today_visits = $stats['today_visits'] ?? 0;
$unique_visitors = $stats['unique_visitors'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Statistics - PST DEV</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Inter', sans-serif;
            background: #0a0a0a;
            color: #fff;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 0, 51, 0.05) 0%, transparent 25%),
                radial-gradient(circle at 90% 80%, rgba(174, 0, 0, 0.05) 0%, transparent 25%);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            border: 1px solid rgba(255, 0, 51, 0.1);
            backdrop-filter: blur(10px);
        }
        
        h1 {
            color: var(--neon-red);
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 0 10px rgba(255, 0, 51, 0.3);
        }
        
        .subtitle {
            color: #aaa;
            margin-bottom: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--neon-red);
            box-shadow: 0 10px 30px rgba(255, 0, 51, 0.1);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--neon-red);
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
        }
        
        .stat-label {
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
        }
        
        .btn {
            padding: 12px 25px;
            background: linear-gradient(90deg, var(--accent-red), var(--dark-red));
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 0, 51, 0.3);
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid var(--neon-red);
            color: var(--neon-red);
        }
        
        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            color: #666;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .warning {
            background: rgba(255, 153, 0, 0.1);
            border: 1px solid rgba(255, 153, 0, 0.3);
            color: #ff9900;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> VISIT ANALYTICS</h1>
            <p class="subtitle">PST DEV Portfolio Visitor Statistics</p>
            
            <?php if (isset($stats['using_fallback']) && $stats['using_fallback']): ?>
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i>
                Using fallback data storage. Database connection not available.
            </div>
            <?php endif; ?>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($total_visits); ?></div>
                <div class="stat-label">Total Visits</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($today_visits); ?></div>
                <div class="stat-label">Today's Visits</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($unique_visitors); ?></div>
                <div class="stat-label">Unique Visitors</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="/" class="btn">
                <i class="fas fa-arrow-left"></i> Back to Portfolio
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </a>
        </div>
        
        <div class="footer">
            <p><i class="fas fa-code"></i> PST DEV Analytics System</p>
            <p>Last updated: <?php echo date('F j, Y \\a\\t g:i A'); ?></p>
            <p style="margin-top: 10px; font-size: 0.9rem; color: #888;">
                <i class="fas fa-info-circle"></i> 
                Statistics update every 30 minutes. Refresh for latest data.
            </p>
        </div>
    </div>
    
    <script>
    // Simple page interactions
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Auto-refresh every 5 minutes
    setTimeout(() => {
        window.location.reload();
    }, 300000); // 5 minutes
    </script>
</body>
</html>