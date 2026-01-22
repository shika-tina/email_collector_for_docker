<?php
/**
 * Admin 後台共用設定檔
 * 所有管理頁面都會載入這個檔案
 */

// 防止直接訪問
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not allowed');
}

// ============================================
// 🔒 安全設定
// ============================================
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'admin123');

// Session 設定
define('SESSION_TIMEOUT', 3600);  // 1 小時後自動登出（秒）

// 登入嘗試限制
define('MAX_LOGIN_ATTEMPTS', 5);  // 最多嘗試 5 次
define('LOCKOUT_TIME', 900);      // 鎖定 15 分鐘（秒）

// ============================================
// 資料庫設定
// ============================================
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'laratesting2');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

// ============================================
// 啟動 Session
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// 檢查登入狀態的函數
// ============================================
function is_admin_logged_in() {
    // 檢查 Session 是否存在
    if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        return false;
    }
    
    // 檢查 Session 是否過期
    if (isset($_SESSION['admin_login_time'])) {
        $elapsed = time() - $_SESSION['admin_login_time'];
        if ($elapsed > SESSION_TIMEOUT) {
            // Session 過期，清除並返回 false
            session_destroy();
            return false;
        }
    }
    
    return true;
}

// ============================================
// 登入驗證函數
// ============================================
function verify_admin_login($password) {
    // 檢查登入嘗試次數
    if (isset($_SESSION['login_attempts'])) {
        $attempts = $_SESSION['login_attempts'];
        $last_attempt = $_SESSION['last_attempt_time'] ?? 0;
        
        // 如果超過最大嘗試次數
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            // 檢查是否還在鎖定期間
            if (time() - $last_attempt < LOCKOUT_TIME) {
                $remaining = LOCKOUT_TIME - (time() - $last_attempt);
                return [
                    'success' => false,
                    'message' => "登入嘗試次數過多，請在 " . ceil($remaining / 60) . " 分鐘後再試"
                ];
            } else {
                // 鎖定期間已過，重置嘗試次數
                $_SESSION['login_attempts'] = 0;
            }
        }
    }
    
    // 驗證密碼
    if ($password === ADMIN_PASSWORD) {
        // 登入成功
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['login_attempts'] = 0;  // 重置嘗試次數
        
        return [
            'success' => true,
            'message' => '登入成功'
        ];
    } else {
        // 登入失敗，增加嘗試次數
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_attempt_time'] = time();
        
        $remaining = MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts'];
        
        return [
            'success' => false,
            'message' => '密碼錯誤' . ($remaining > 0 ? "，還剩 {$remaining} 次嘗試" : '，帳號已鎖定')
        ];
    }
}

// ============================================
// 登出函數
// ============================================
function admin_logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// ============================================
// 要求登入（用於保護頁面）
// ============================================
function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
