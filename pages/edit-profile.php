<?php
require_once '../config.php';
requireLogin();

$pageTitle = 'Edit Profil';
$conn = db();
$user = currentUser();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $curPass  = $_POST['current_password'] ?? '';
    $newPass  = $_POST['new_password'] ?? '';
    $confPass = $_POST['confirm_password'] ?? '';

    if (!$username || !$email) {
        $error = 'Username dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Check duplicate
        $id = (int)$user['id'];
        $chk = mysqli_query($conn, "SELECT id FROM kasir WHERE (username='$username' OR email='$email') AND id != $id LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            $error = 'Username atau email sudah digunakan akun lain.';
        } else {
            // Update basic info
            mysqli_query($conn, "UPDATE kasir SET username='$username', email='$email' WHERE id=$id");
            $_SESSION['kasir_username'] = $username;

            // Update password if provided
            if ($newPass) {
                if (!$curPass) {
                    $error = 'Masukkan password saat ini untuk mengganti password.';
                } elseif (strlen($newPass) < 6) {
                    $error = 'Password baru minimal 6 karakter.';
                } elseif ($newPass !== $confPass) {
                    $error = 'Password baru dan konfirmasi tidak cocok.';
                } else {
                    // Verify current password
                    $passRes = mysqli_query($conn, "SELECT password FROM kasir WHERE id=$id LIMIT 1");
                    $passRow = mysqli_fetch_assoc($passRes);
                    if (!password_verify($curPass, $passRow['password'])) {
                        $error = 'Password saat ini salah.';
                    } else {
                        $hash = sanitize(password_hash($newPass, PASSWORD_BCRYPT));
                        mysqli_query($conn, "UPDATE kasir SET password='$hash' WHERE id=$id");
                    }
                }
            }

            if (!$error) {
                setFlash('success', 'Profil berhasil diperbarui.');
                header('Location: ' . BASE_URL . '/pages/profile.php');
                exit;
            }
        }
    }

    // Refresh user data if there was error
    $user = currentUser();
}

include '../includes/header.php';
?>

<div style="margin-bottom:16px;">
  <a href="profile.php" class="btn btn-outline btn-sm">← Kembali ke Profil</a>
</div>

<div class="card" style="max-width:520px;">
  <div class="card-header">
    <span class="card-title">Edit Profil</span>
  </div>
  <div class="card-body">
    <?php if ($error): ?>
    <div class="flash flash-error" style="margin-bottom:16px;">
      <span><?php echo e($error); ?></span>
    </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="form-group">
        <label class="form-label">Username <span class="req">*</span></label>
        <input type="text" name="username" class="form-control" value="<?php echo e($user['username']); ?>" required autofocus>
      </div>
      <div class="form-group">
        <label class="form-label">Email <span class="req">*</span></label>
        <input type="email" name="email" class="form-control" value="<?php echo e($user['email']); ?>" required>
      </div>

      <div style="border-top:1px solid var(--gray-100);padding-top:20px;margin-top:8px;">
        <div style="font-family:var(--font-mono);font-size:0.8rem;font-weight:700;color:var(--gray-500);margin-bottom:12px;letter-spacing:0.04em;">GANTI PASSWORD (Opsional)</div>
        <div class="form-group">
          <label class="form-label">Password Saat Ini</label>
          <input type="password" name="current_password" class="form-control" autocomplete="current-password">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password Baru</label>
            <input type="password" name="new_password" class="form-control" autocomplete="new-password">
            <div class="form-hint">Min. 6 karakter</div>
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" autocomplete="new-password">
          </div>
        </div>
      </div>

      <div style="display:flex;gap:10px;margin-top:8px;">
        <a href="profile.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
