<?php
$ADMIN_PASSWORD = getenv('ADMIN_PASSWORD') ?: 'admin123';

// 啟動 session
session_start();

// 處理登入
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $input_password = isset($_POST['password']) ? $_POST['password'] : '';
    if ($input_password === $ADMIN_PASSWORD) {
        $_SESSION['email_list_authenticated'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = '密碼錯誤，請重新輸入';
    }
}

// 處理登出
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// 檢查是否已登入
$is_authenticated = isset($_SESSION['email_list_authenticated']) && $_SESSION['email_list_authenticated'] === true;

// 如果未登入，顯示登入表單
if (!$is_authenticated) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-TW">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>登入 - Email 列表</title>
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
            
            .login-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                padding: 40px;
                max-width: 400px;
                width: 100%;
            }
            
            h1 {
                color: #333;
                margin-bottom: 10px;
                text-align: center;
                font-size: 24px;
            }
            
            .subtitle {
                color: #666;
                text-align: center;
                margin-bottom: 30px;
                font-size: 14px;
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
            
            input[type="password"] {
                width: 100%;
                padding: 14px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-size: 16px;
                transition: all 0.3s;
            }
            
            input[type="password"]:focus {
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
            
            .error {
                padding: 12px;
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
            }
            
            .back-link {
                text-align: center;
                margin-top: 20px;
            }
            
            .back-link a {
                color: #667eea;
                text-decoration: none;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>🔒 管理員登入</h1>
            <p class="subtitle">請輸入密碼以查看 Email 列表</p>
            
            <?php if ($login_error): ?>
                <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="password">密碼：</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="請輸入管理員密碼"
                        required
                        autofocus
                    >
                </div>
                
                <button type="submit" name="login">登入</button>
            </form>
            
            <div class="back-link">
                <a href="email-form-simple.php">← 返回表單頁面</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ============================================
// 以下為已登入後顯示的內容
// ============================================

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

// 獲取所有 email
try {
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
    <title>Email 列表</title>
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
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
        
        .stats {
            color: #666;
            margin-bottom: 30px;
            font-size: 18px;
        }
        
        .stats strong {
            color: #667eea;
            font-size: 24px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 收集的 Email 列表</h1>
            <a href="?logout=1" class="logout-btn">登出</a>
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
                <p style="margin-top: 10px;"><a href="email-form-simple.php">前往表單頁面</a></p>
            </div>
        <?php endif; ?>
        
        <div class="links">
            <a href="email-form-simple.php">← 返回表單頁面</a>
        </div>
    </div>
</body>
</html>
