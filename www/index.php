<?php
/**
 * 首頁 - Email 收集系統
 * 當訪問 http://localhost/ 時會顯示這個頁面
 */

// 獲取已收集的 email 數量（可選）
$count = 0;
try {
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_name = getenv('DB_NAME') ?: 'laratesting2';
    $db_user = getenv('DB_USER') ?: 'root';
    $db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $table_exists = $pdo->query("SHOW TABLES LIKE 'email_collector'")->rowCount() > 0;
    if ($table_exists) {
        $count = $pdo->query("SELECT COUNT(*) FROM email_collector")->fetchColumn();
    }
} catch(PDOException $e) {
    // 忽略錯誤，只顯示首頁
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email 收集系統 - 首頁</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 50px;
            max-width: 800px;
            width: 100%;
            text-align: center;
        }
        
        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 36px;
        }
        
        .subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 40px;
        }
        
        .stats-number {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stats-label {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            min-width: 200px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            color: #666;
            border: 2px solid #ddd;
        }
        
        .btn-outline:hover {
            background: #f8f9fa;
            border-color: #667eea;
            color: #667eea;
        }
        
        .features {
            margin-top: 50px;
            text-align: left;
        }
        
        .features h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .feature-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        
        .feature-item .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .feature-item h3 {
            color: #333;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .feature-item p {
            color: #666;
            font-size: 14px;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email 收集系統</h1>
        <p class="subtitle">簡單、安全、易用的 Email 收集解決方案</p>
        
        <div class="stats">
            <div class="stats-number"><?php echo $count; ?></div>
            <div class="stats-label">已收集的 Email 數量</div>
        </div>
        
        <div class="button-group">
            <a href="email-form-simple.php" class="btn btn-primary">
                📝 填寫 Email 表單
            </a>
            <a href="email-list-simple.php" class="btn btn-secondary">
                📋 查看 Email 列表
            </a>
        </div>
        
        <!-- <div class="button-group" style="margin-top: 15px;">
            <a href="查看資料庫.php" class="btn btn-outline">
                🗄️ 查看資料庫
            </a>
            <a href="測試資料庫連接.php" class="btn btn-outline">
                🔌 測試連接
            </a>
        </div> -->
        
        <div class="features">
            <h2>✨ 功能特色</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon">🔒</div>
                    <h3>安全保護</h3>
                    <p>列表頁面需要密碼才能查看</p>
                </div>
                <div class="feature-item">
                    <div class="icon">✅</div>
                    <h3>自動驗證</h3>
                    <p>自動驗證 email 格式</p>
                </div>
                <div class="feature-item">
                    <div class="icon">🚫</div>
                    <h3>防止重複</h3>
                    <p>同一個 email 只能註冊一次</p>
                </div>
                <div class="feature-item">
                    <div class="icon">💾</div>
                    <h3>資料庫儲存</h3>
                    <p>安全儲存在 MySQL 資料庫</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
