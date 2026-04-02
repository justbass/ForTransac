<?php
require_once '../config.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/kasir.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$username || !$email || !$password || !$confirm) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } else {
        $conn = db();
        $chk = mysqli_query($conn, "SELECT id FROM kasir WHERE username = '$username' OR email = '$email' LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            $error = 'Username atau email sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $hash = sanitize($hash);
            mysqli_query($conn, "INSERT INTO kasir (username, email, password) VALUES ('$username', '$email', '$hash')");
            $success = 'Akun berhasil dibuat! Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — <?php echo APP_NAME; ?></title>
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
    <div class="auth-title">Buat Akun Kasir</div>
    <?php if ($error): ?>
    <div class="flash flash-error" style="margin-bottom:16px;">
      <span><?php echo e($error); ?></span>
    </div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="flash flash-success" style="margin-bottom:16px;">
      <span><?php echo e($success); ?></span>
    </div>
    <?php endif; ?>
    <?php if (!$success): ?>
    <form method="post" autocomplete="off">
      <div class="form-group">
        <label class="form-label">Username <span class="req">*</span></label>
        <input type="text" name="username" class="form-control" value="<?php echo e($_POST['username'] ?? ''); ?>" autofocus required>
      </div>
      <div class="form-group">
        <label class="form-label">Email <span class="req">*</span></label>
        <input type="email" name="email" class="form-control" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password <span class="req">*</span></label>
        <input type="password" name="password" class="form-control" required>
        <div class="form-hint">Minimal 6 karakter</div>
      </div>
      <div class="form-group">
        <label class="form-label">Konfirmasi Password <span class="req">*</span></label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">Daftar</button>
    </form>
    <?php endif; ?>
    <div class="auth-footer">
      Sudah punya akun? <a href="<?php echo BASE_URL; ?>/pages/login.php">Masuk</a>
    </div>
  </div>
</div>
<script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
</body>
</html>
