<?php
/**
 * Admin 後台主控台
 */

define('ADMIN_ACCESS', true);
require_once 'config.php';
require_admin_login();  // 要求登入

// 連接資料庫獲取統計資訊
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $table_exists = $pdo->query("SHOW TABLES LIKE 'email_collector'")->rowCount() > 0;
    if ($table_exists) {
        $email_count = $pdo->query("SELECT COUNT(*) FROM email_collector")->fetchColumn();
        $latest_emails = $pdo->query("SELECT * FROM email_collector ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $email_count = 0;
        $latest_emails = [];
    }
} catch(PDOException $e) {
    $email_count = 0;
    $latest_emails = [];
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理後台 - 主控台</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            color: #333;
        }
        
        .logout-btn {
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .menu-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .menu-card .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .menu-card h3 {
            margin-bottom: 10px;
        }
        
        .menu-card p {
            color: #666;
            font-size: 14px;
        }
        
        .latest-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .latest-section h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .empty {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 管理後台主控台</h1>
            <a href="logout.php" class="logout-btn">登出</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $email_count; ?></div>
                <div class="stat-label">已收集的 Email</div>
            </div>
        </div>
        
        <div class="menu-grid">
            <a href="emails.php" class="menu-card">
                <div class="icon">📧</div>
                <h3>Email 管理</h3>
                <p>查看和管理所有收集的 email</p>
            </a>

            <a href="查看資料庫.php" class="menu-card">
                <div class="icon">🔏</div>
                <h3>🗄️ 查看資料庫</h3>
                <p>查看資料庫結構和資料</p>
            </a>

            <!-- class="btn btn-outline" 會讓網頁顯示原文字-->
            <a href="測試資料庫連接.php" class="menu-card">
                <div class="icon">🔌</div>
                <h3>測試連接</h3>
                <p>查看資料庫連接狀態</p>
            </a>
        </div>
        
        <div class="latest-section">
            <h2>📋 最新的 5 筆 Email</h2>
            <?php if (count($latest_emails) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>註冊時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latest_emails as $email): ?>
                            <tr>
                                <td><?php echo $email['id']; ?></td>
                                <td><?php echo htmlspecialchars($email['email']); ?></td>
                                <td><?php echo htmlspecialchars($email['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">目前還沒有收集到任何 email</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
