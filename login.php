<?php
require_once __DIR__ . '/includes/init.php';
if (currentUser()) redirect(currentUser()['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf(); $username = trim((string)($_POST['username'] ?? '')); $password = (string)($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT id,full_name,username,password_hash,role FROM users WHERE username=:username AND is_active=1 LIMIT 1');
    $stmt->execute(['username'=>$username]); $record = $stmt->fetch();
    if ($record && password_verify($password, $record['password_hash'])) {
        session_regenerate_id(true); unset($record['password_hash']); $_SESSION['user']=$record;
        redirect($record['role']==='admin'?'admin/dashboard.php':'user/dashboard.php');
    }
    $error = 'Incorrect username or password.';
}
$title='Sign in'; require __DIR__ . '/includes/header.php';
?><section class="login-card"><div class="login-brand"><span class="brand-mark">W</span><span class="eyebrow">WAREHOUSE OPERATIONS</span></div><h1>Welcome back</h1><p class="muted">Sign in to manage your inventory workspace.</p><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post" class="form-stack"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><label>Username<input name="username" autocomplete="username" required autofocus></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button class="button primary full" type="submit">Sign in <span>→</span></button></form><div class="login-hint"><b>Demo access</b><span>Admin: admin / admin123</span><span>User: demo / user123</span></div></section><?php require __DIR__ . '/includes/footer.php'; ?>
