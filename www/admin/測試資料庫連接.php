<?php
/**
 * 資料庫連接測試工具
 * 這個工具可以驗證是否真的連接到 MySQL 資料庫
 */

// 資料庫設定（與其他檔案相同）
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'laratesting2';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$connection_info = [];
$connection_success = false;

// 嘗試連接資料庫
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $connection_success = true;
    
    // 獲取資料庫資訊
    $connection_info['status'] = '成功連接！';
    $connection_info['host'] = $db_host;
    $connection_info['database'] = $db_name;
    $connection_info['user'] = $db_user;
    
    // 獲取 MySQL 版本
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    $connection_info['mysql_version'] = $version;
    
    // 獲取當前資料庫的所有資料表
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $connection_info['tables'] = $tables;
    
    // 檢查 email_collector 表是否存在
    $email_table_exists = in_array('email_collector', $tables);
    $connection_info['email_table_exists'] = $email_table_exists;
    
    if ($email_table_exists) {
        // 獲取 email_collector 表的資料數量
        $count = $pdo->query("SELECT COUNT(*) FROM email_collector")->fetchColumn();
        $connection_info['email_count'] = $count;
        
        // 獲取最新的 3 筆資料作為範例
        $sample = $pdo->query("SELECT * FROM email_collector ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
        $connection_info['sample_data'] = $sample;
    }
    
} catch(PDOException $e) {
    $connection_info['status'] = '連接失敗';
    $connection_info['error'] = $e->getMessage();
    $connection_info['error_code'] = $e->getCode();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>資料庫連接測試</title>
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
            max-width: 1000px;
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
        
        .status-box {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .status-box.success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        
        .status-box.error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .info-item {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #333;
            font-family: monospace;
        }
        
        .table-list {
            list-style: none;
            padding: 0;
        }
        
        .table-list li {
            padding: 8px;
            background: white;
            margin: 5px 0;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        
        .sample-data {
            margin-top: 15px;
        }
        
        .sample-data table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .sample-data th,
        .sample-data td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .sample-data th {
            background: #667eea;
            color: white;
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
        
        .proof {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .proof h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .proof p {
            color: #856404;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 資料庫連接測試</h1>
        
        <div class="status-box <?php echo $connection_success ? 'success' : 'error'; ?>">
            <?php if ($connection_success): ?>
                ✅ <?php echo $connection_info['status']; ?>
            <?php else: ?>
                ❌ <?php echo $connection_info['status']; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($connection_success): ?>
            <div class="info-section">
                <h2>📊 連接資訊</h2>
                <div class="info-item">
                    <span class="info-label">資料庫主機：</span>
                    <span class="info-value"><?php echo htmlspecialchars($connection_info['host']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">資料庫名稱：</span>
                    <span class="info-value"><?php echo htmlspecialchars($connection_info['database']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">使用者名稱：</span>
                    <span class="info-value"><?php echo htmlspecialchars($connection_info['user']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">MySQL 版本：</span>
                    <span class="info-value"><?php echo htmlspecialchars($connection_info['mysql_version']); ?></span>
                </div>
            </div>
            
            <div class="info-section">
                <h2>📋 資料庫中的所有資料表</h2>
                <?php if (count($connection_info['tables']) > 0): ?>
                    <ul class="table-list">
                        <?php foreach ($connection_info['tables'] as $table): ?>
                            <li>
                                <?php echo htmlspecialchars($table); ?>
                                <?php if ($table === 'email_collector'): ?>
                                    <strong style="color: green;"> ← 所有蒐集的 email 資料表</strong>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>資料庫中還沒有任何資料表</p>
                <?php endif; ?>
            </div>
            
            <?php if ($connection_info['email_table_exists']): ?>
                <div class="info-section">
                    <h2>📧 Email 資料表狀態</h2>
                    <div class="info-item">
                        <span class="info-label">資料表存在：</span>
                        <span class="info-value" style="color: green;">✓ 是</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">已收集的 Email 數量：</span>
                        <span class="info-value"><strong><?php echo $connection_info['email_count']; ?></strong> 筆</span>
                    </div>
                    
                    <?php if (count($connection_info['sample_data']) > 0): ?>
                        <div class="sample-data">
                            <h3 style="margin-top: 15px; margin-bottom: 10px;">最新的 3 筆資料（證明真的從資料庫讀取）：</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Email</th>
                                        <th>註冊時間</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($connection_info['sample_data'] as $row): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="info-section">
                    <h2>📧 Email 資料表狀態</h2>
                    <p>資料表 <code>email_collector</code> 尚未建立</p>
                    <p style="margin-top: 10px;">請先訪問表單頁面提交一個 email，系統會自動建立資料表</p>
                </div>
            <?php endif; ?>
            
            <div class="proof">
                <h3>🔍 如何證明真的連接到資料庫？</h3>
                <p>
                    <strong>1. MySQL 版本資訊：</strong> 這個版本號是從 MySQL 伺服器直接查詢的，無法偽造。<br>
                    <strong>2. 資料表列表：</strong> 顯示的是資料庫中真實存在的所有資料表。<br>
                    <strong>3. 實際資料：</strong> 上面顯示的 email 資料是從資料庫中真實讀取的。<br>
                    <strong>4. 即時更新：</strong> 如果你在表單頁面提交新的 email，重新整理這個頁面就會看到新的資料。
                </p>
            </div>
            
        <?php else: ?>
            <div class="info-section">
                <h2>❌ 連接失敗原因</h2>
                <div class="info-item">
                    <span class="info-label">錯誤訊息：</span>
                    <span class="info-value" style="color: red;"><?php echo htmlspecialchars($connection_info['error']); ?></span>
                </div>
                <?php if (isset($connection_info['error_code'])): ?>
                    <div class="info-item">
                        <span class="info-label">錯誤代碼：</span>
                        <span class="info-value"><?php echo $connection_info['error_code']; ?></span>
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px;">
                    <strong>可能的解決方法：</strong>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li>確認 Laragon 的 MySQL 服務已啟動</li>
                        <li>確認資料庫名稱 <code>laratesting2</code> 存在</li>
                        <li>檢查資料庫使用者名稱和密碼是否正確</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="links">
            <a href="dashboard.php">返回管理後台</a>
            <a href="查看資料庫.php">查看完整資料</a>
        </div>
    </div>
</body>
</html>
