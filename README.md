<?php
require_once '../config.php';
requireLogin();

$pageTitle = 'Profil Saya';
$conn = db();
$user = currentUser();

// Stats
$statsRes = mysqli_query($conn, "SELECT COUNT(*) as cnt, COALESCE(SUM(grand_total),0) as total FROM transaksi WHERE kasir_id = " . (int)$user['id']);
$stats = mysqli_fetch_assoc($statsRes);

include '../includes/header.php';
?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;" class="resp-grid">
  <!-- Profile Info -->
  <div class="card profile-card">
    <div class="card-header">
      <span class="card-title">Info Akun</span>
      <a href="edit-profile.php" class="btn btn-outline btn-sm">✎ Edit Profil</a>
    </div>
    <div class="card-body">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--gray-100);">
        <div class="profile-avatar-lg"><?php echo strtoupper(substr($user['username'],0,1)); ?></div>
        <div>
          <div style="font-weight:700;font-size:1.1rem;"><?php echo e($user['username']); ?></div>
          <div class="text-muted text-sm"><?php echo e($user['email']); ?></div>
        </div>
      </div>
      <div class="profile-detail-row">
        <span class="pdr-label">Username</span>
        <span><?php echo e($user['username']); ?></span>
      </div>
      <div class="profile-detail-row">
        <span class="pdr-label">Email</span>
        <span><?php echo e($user['email']); ?></span>
      </div>
      <div class="profile-detail-row">
        <span class="pdr-label">ID Kasir</span>
        <span class="text-mono">#<?php echo $user['id']; ?></span>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Statistik Saya</span>
    </div>
    <div class="card-body">
      <div class="stat-card" style="border:none;padding:0;margin-bottom:16px;">
        <div class="stat-icon">⊠</div>
        <div class="stat-info">
          <div class="stat-value"><?php echo $stats['cnt']; ?></div>
          <div class="stat-label">Total Transaksi</div>
        </div>
      </div>
      <div class="stat-card" style="border:none;padding:0;">
        <div class="stat-icon">◈</div>
        <div class="stat-info">
          <div class="stat-value" style="font-size:1rem;"><?php echo formatRupiah($stats['total']); ?></div>
          <div class="stat-label">Total Omzet</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent transactions by this user -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Transaksi Terbaru Saya</span>
    <a href="transaksi.php" class="btn btn-ghost btn-sm">Lihat Semua →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Grand Total</th>
          <th>Bayar</th>
          <th>Kembalian</th>
          <th>Waktu</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $myTrx = mysqli_query($conn, "SELECT * FROM transaksi WHERE kasir_id = " . (int)$user['id'] . " ORDER BY created_at DESC LIMIT 10");
        $hasTrx = false;
        while ($t = mysqli_fetch_assoc($myTrx)):
          $hasTrx = true;
        ?>
        <tr>
          <td><span class="sku-tag"><?php echo e($t['kode']); ?></span></td>
          <td class="text-mono font-bold"><?php echo formatRupiah($t['grand_total']); ?></td>
          <td class="text-mono"><?php echo formatRupiah($t['amount_paid']); ?></td>
          <td class="text-mono"><?php echo formatRupiah($t['change_amount']); ?></td>
          <td class="text-muted text-sm"><?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="detail-transaksi.php?id=<?php echo $t['id']; ?>" class="btn btn-ghost btn-sm">⊞</a>
              <a href="struk-pdf.php?id=<?php echo $t['id']; ?>" class="btn btn-ghost btn-sm" target="_blank">↓</a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (!$hasTrx): ?>
        <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--gray-400);">Belum ada transaksi</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
@media (max-width:640px) { .resp-grid { grid-template-columns: 1fr !important; } }
</style>

<?php include '../includes/footer.php'; ?>
