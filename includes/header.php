<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? e($pageTitle) . ' — ' . APP_NAME : APP_NAME; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
<?php if (isLoggedIn()): 
    $user = currentUser();
    $flash = getFlash();
?>
<div class="app-wrapper">
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="brand">
        <span class="brand-icon">◼</span>
        <div>
          <div class="brand-name"><?php echo APP_NAME; ?></div>
          <div class="brand-loc"><?php echo APP_LOCATION; ?></div>
        </div>
      </div>
      <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">✕</button>
    </div>
    <nav class="sidebar-nav">
      <a href="<?php echo BASE_URL; ?>/pages/kasir.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'kasir.php' ? 'active' : ''; ?>">
        <span class="nav-icon">⊞</span><span>Kasir</span>
      </a>
      <a href="<?php echo BASE_URL; ?>/pages/produk.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : ''; ?>">
        <span class="nav-icon">⊡</span><span>Produk</span>
      </a>
      <a href="<?php echo BASE_URL; ?>/pages/kategori.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'kategori.php' ? 'active' : ''; ?>">
        <span class="nav-icon">⊟</span><span>Kategori</span>
      </a>
      <a href="<?php echo BASE_URL; ?>/pages/transaksi.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'transaksi.php' ? 'active' : ''; ?>">
        <span class="nav-icon">⊠</span><span>Riwayat Transaksi</span>
      </a>
      <a href="<?php echo BASE_URL; ?>/pages/daftar-kasir.php" class="nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['daftar-kasir.php']) ? 'active' : ''; ?>">
        <span class="nav-icon">⊕</span><span>Daftar Kasir</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <a href="<?php echo BASE_URL; ?>/pages/profile.php" class="profile-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['profile.php','edit-profile.php']) ? 'active' : ''; ?>">
        <div class="avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
        <div class="profile-info">
          <div class="profile-name"><?php echo e($user['username']); ?></div>
          <div class="profile-email"><?php echo e($user['email']); ?></div>
        </div>
      </a>
      <a href="<?php echo BASE_URL; ?>/pages/logout.php" class="logout-btn" title="Logout">⏻</a>
    </div>
  </aside>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Main Content -->
  <main class="main-content">
    <div class="topbar">
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">☰</button>
      <h1 class="page-title"><?php echo isset($pageTitle) ? e($pageTitle) : ''; ?></h1>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo e($user['username']); ?></span>
      </div>
    </div>
    <?php if ($flash): ?>
    <div class="flash flash-<?php echo e($flash['type']); ?>" id="flashMsg">
      <span><?php echo e($flash['msg']); ?></span>
      <button onclick="this.parentElement.remove()">✕</button>
    </div>
    <?php endif; ?>
    <div class="content-area">
<?php else: ?>
<div class="auth-wrapper">
<?php endif; ?>
