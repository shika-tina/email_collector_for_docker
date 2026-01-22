<?php
/**
 * 直接查看資料庫中的 Email 資料
 * 這個工具可以幫助你確認資料是否正確儲存
 */

// 資料庫設定
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'laratesting2';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

// 連接資料庫
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 檢查資料表是否存在
    $table_exists = $pdo->query("SHOW TABLES LIKE 'email_collector'")->rowCount() > 0;
    
    if ($table_exists) {
        // 獲取所有資料
        $stmt = $pdo->query("SELECT * FROM email_collector ORDER BY created_at DESC");
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($emails);
        
        // 獲取資料表結構
        $structure = $pdo->query("DESCRIBE email_collector")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $emails = [];
        $count = 0;
        $structure = [];
    }
    
} catch(PDOException $e) {
    $error = "資料庫連接失敗：" . $e->getMessage();
    $emails = [];
    $count = 0;
    $structure = [];
    $table_exists = false;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>資料庫查看工具</title>
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
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #1976D2;
            margin-bottom: 10px;
        }
        
        .info-box p {
            color: #555;
            margin: 5px 0;
        }
        
        .error-box {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #c62828;
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
            font-size: 32px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
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
        
        .structure-table {
            margin-top: 30px;
        }
        
        .links {
            margin-top: 30px;
            text-align: center;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid #667eea;
            border-radius: 5px;
            display: inline-block;
            transition: all 0.3s;
            margin: 0 5px;
        }
        
        .links a:hover {
            background: #667eea;
            color: white;
        }
        
        .code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ 資料庫查看工具</h1>
        
        <div class="info-box">
            <h3>📊 資料庫資訊</h3>
            <p><strong>資料庫名稱：</strong><span class="code">laratesting2</span></p>
            <p><strong>資料表名稱：</strong><span class="code">email_collector</span></p>
            <p><strong>資料表狀態：</strong><?php echo $table_exists ? '<span style="color: green;">✓ 已建立</span>' : '<span style="color: red;">✗ 不存在</span>'; ?></p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-box">
                <strong>錯誤：</strong><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($table_exists): ?>
            <div class="stats">
                總共收集了 <strong><?php echo $count; ?></strong> 個 email
            </div>
            
            <?php if ($count > 0): ?>
                <h2>📋 Email 資料</h2>
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
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p>資料表中還沒有任何資料</p>
                    <p style="margin-top: 10px;"><a href="email-form-simple.php">前往表單頁面提交 email</a></p>
                </div>
            <?php endif; ?>
            
            <h2 class="structure-table">🔧 資料表結構</h2>
            <table>
                <thead>
                    <tr>
                        <th>欄位名稱</th>
                        <th>類型</th>
                        <th>允許 NULL</th>
                        <th>預設值</th>
                        <th>額外資訊</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($structure as $field): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($field['Field']); ?></strong></td>
                            <td><?php echo htmlspecialchars($field['Type']); ?></td>
                            <td><?php echo htmlspecialchars($field['Null']); ?></td>
                            <td><?php echo htmlspecialchars($field['Default'] ?? 'NULL'); ?></td>
                            <td><?php echo htmlspecialchars($field['Extra']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <p>資料表 <span class="code">email_collector</span> 尚未建立</p>
                <p style="margin-top: 10px;">請先訪問表單頁面提交一個 email，系統會自動建立資料表</p>
                <p style="margin-top: 10px;"><a href="email-form-simple.php">前往表單頁面</a></p>
            </div>
        <?php endif; ?>
        
        <div class="links">
            <a href="dashboard.php">返回管理後台</a>
            <a href="測試資料庫連接.php">測試資料庫密碼</a>
        </div>
    </div>
</body>
</html>
