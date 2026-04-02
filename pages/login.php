<?php
require_once '../config.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/kasir.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $conn = db();
        $res = mysqli_query($conn, "SELECT id, username, password FROM kasir WHERE username = '$username' LIMIT 1");
        $user = mysqli_fetch_assoc($res);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['kasir_id'] = $user['id'];
            $_SESSION['kasir_username'] = $user['username'];
            header('Location: ' . BASE_URL . '/pages/kasir.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — <?php echo APP_NAME; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-brand">
      <div class="auth-brand-name">◼ <?php echo APP_NAME; ?></div>
      <div class="auth-brand-sub"><?php echo APP_LOCATION; ?></div>
    </div>
    <div class="auth-title">Masuk ke Akun</div>
    <?php if ($error): ?>
    <div class="flash flash-error" style="margin-bottom:16px;">
      <span><?php echo e($error); ?></span>
    </div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <div class="form-group">
        <label class="form-label">Username <span class="req">*</span></label>
        <input type="text" name="username" class="form-control" value="<?php echo e($_POST['username'] ?? ''); ?>" autofocus required>
      </div>
      <div class="form-group">
        <label class="form-label">Password <span class="req">*</span></label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">Masuk</button>
    </form>
    <div class="auth-footer">
      Belum punya akun? <a href="<?php echo BASE_URL; ?>/pages/register.php">Daftar</a>
    </div>
  </div>
</div>
<script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
</body>
</html>
