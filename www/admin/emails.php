<?php
/**
 * Admin 後台 - Email 列表
 */

define('ADMIN_ACCESS', true);
require_once 'config.php';
require_admin_login();

// 連接資料庫
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM email_collector ORDER BY created_at DESC");
    $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = count($emails);
} catch(PDOException $e) {
    $emails = [];
    $count = 0;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email 管理 - 管理後台</title>
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
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
        }
        
        .nav-links {
            display: flex;
            gap: 10px;
        }
        
        .nav-links a {
            padding: 8px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .stats {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stats strong {
            color: #667eea;
            font-size: 24px;
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
            <h1>📧 Email 管理</h1>
            <div class="nav-links">
                <a href="dashboard.php" class="btn-primary">返回主控台</a>
                <a href="logout.php" class="btn-danger">登出</a>
            </div>
        </div>
        
        <div class="stats">
            總共收集了 <strong><?php echo $count; ?></strong> 個 email
        </div>
        
        <?php if ($count > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email 地址</th>
                        <th>註冊時間</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emails as $email): ?>
                        <tr>
                            <td><?php echo $email['id']; ?></td>
                            <td><?php echo htmlspecialchars($email['email']); ?></td>
                            <td><?php echo htmlspecialchars($email['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">
                <p>目前還沒有收集到任何 email</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
