<?php
require_once '../config.php';
requireLogin();

$pageTitle = 'Daftar Kasir';
$conn = db();

$kasirRes = mysqli_query($conn, "SELECT k.*, COUNT(t.id) as total_trx, COALESCE(SUM(t.grand_total),0) as total_omzet FROM kasir k LEFT JOIN transaksi t ON t.kasir_id = k.id GROUP BY k.id ORDER BY k.username");
$kasirs = [];
while ($r = mysqli_fetch_assoc($kasirRes)) $kasirs[] = $r;

include '../includes/header.php';
?>

<div class="card">
  <div class="card-header">
    <span class="card-title">Daftar Kasir</span>
    <span class="text-muted text-sm"><?php echo count($kasirs); ?> akun terdaftar</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Username</th>
          <th>Email</th>
          <th>Total Transaksi</th>
          <th>Total Omzet</th>
          <th>Bergabung</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($kasirs)): ?>
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--gray-400);">Belum ada kasir</td></tr>
        <?php else: ?>
        <?php foreach ($kasirs as $i => $k): ?>
        <tr>
          <td>
            <div class="avatar" style="width:30px;height:30px;font-size:0.75rem;display:inline-flex;"><?php echo strtoupper(substr($k['username'],0,1)); ?></div>
          </td>
          <td style="font-weight:600;">
            <?php echo e($k['username']); ?>
            <?php if ($k['id'] == $_SESSION['kasir_id']): ?>
            <span class="badge badge-dark" style="margin-left:6px;">Anda</span>
            <?php endif; ?>
          </td>
          <td class="text-muted"><?php echo e($k['email']); ?></td>
          <td class="text-mono"><?php echo $k['total_trx']; ?> transaksi</td>
          <td class="text-mono font-bold"><?php echo formatRupiah($k['total_omzet']); ?></td>
          <td class="text-muted text-sm"><?php echo date('d/m/Y', strtotime($k['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
