<?php
/**
 * 簡單的 Email 收集表單
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
} catch(PDOException $e) {
    die("資料庫連接失敗：" . $e->getMessage());
}

// 資料表建立由 init.sql 處理，此處保留此註釋作為參考
// $create_table_sql = "CREATE TABLE IF NOT EXISTS email_collector ...";

// 處理表單提交
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_email'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        $message = '請輸入 email 地址';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '請輸入有效的 email 地址';
        $message_type = 'error';
    } else {
        try {
            // 檢查 email 是否已存在
            $check_stmt = $pdo->prepare("SELECT id FROM email_collector WHERE email = ?");
            $check_stmt->execute([$email]);
            
            if ($check_stmt->fetch()) {
                $message = '此 email 已經註冊過了';
                $message_type = 'error';
            } else {
                // 插入資料庫
                $insert_stmt = $pdo->prepare("INSERT INTO email_collector (email) VALUES (?)");
                $insert_stmt->execute([$email]);
                
                $message = 'Email 已成功儲存！';
                $message_type = 'success';
                $_POST['email'] = ''; // 清空表單
            }
        } catch(PDOException $e) {
            $message = '儲存失敗：' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// 獲取已收集的 email 數量
$count_stmt = $pdo->query("SELECT COUNT(*) FROM email_collector");
$count = $count_stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email 收集表單</title>
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
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .stats {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
            color: #495057;
        }
        
        .stats strong {
            color: #667eea;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        input[type="email"] {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
            font-weight: 500;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email 收集表單</h1>
        <p class="subtitle">輸入您的 email 地址，我們會定期發送最新資訊給您</p>
        
        <div class="stats">
            目前已收集 <strong><?php echo $count; ?></strong> 個 email
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email 地址：</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="example@email.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                >
            </div>
            
            <button type="submit" name="submit_email">提交 Email</button>
            
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
        </form>
        
        <div class="links">
            <a href="email-list-simple.php">管理員：查看 Email 列表</a>
        </div>
    </div>
</body>
</html>
