<?php
require_once '../config.php';
requireLogin();

$pageTitle = 'Riwayat Transaksi';
$conn = db();

// Pagination & search
$perPage = 20;
$page = max(1, (int)($_GET['page'] ?? 1));
$search = sanitize($_GET['q'] ?? '');
$dateFrom = sanitize($_GET['from'] ?? '');
$dateTo   = sanitize($_GET['to'] ?? '');

$where = "WHERE 1=1";
if ($search) $where .= " AND (t.kode LIKE '%$search%' OR k.username LIKE '%$search%')";
if ($dateFrom) $where .= " AND DATE(t.created_at) >= '$dateFrom'";
if ($dateTo)   $where .= " AND DATE(t.created_at) <= '$dateTo'";

$totalRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM transaksi t JOIN kasir k ON t.kasir_id = k.id $where");
$total = mysqli_fetch_assoc($totalRes)['cnt'];
$totalPages = max(1, ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$trxRes = mysqli_query($conn, "SELECT t.*, k.username as kasir_name FROM transaksi t JOIN kasir k ON t.kasir_id = k.id $where ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset");
$trxs = [];
while ($r = mysqli_fetch_assoc($trxRes)) $trxs[] = $r;

// Stats for today
$todayRes = mysqli_query($conn, "SELECT COUNT(*) as cnt, COALESCE(SUM(grand_total),0) as total FROM transaksi WHERE DATE(created_at) = CURDATE()");
$todayStat = mysqli_fetch_assoc($todayRes);

include '../includes/header.php';
?>

<div class="stats-grid" style="margin-bottom:20px;">
  <div class="stat-card">
    <div class="stat-icon">⊠</div>
    <div class="stat-info">
      <div class="stat-value"><?php echo $todayStat['cnt']; ?></div>
      <div class="stat-label">Transaksi Hari Ini</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">◈</div>
    <div class="stat-info">
      <div class="stat-value" style="font-size:0.95rem;"><?php echo formatRupiah($todayStat['total']); ?></div>
      <div class="stat-label">Pendapatan Hari Ini</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">⊞</div>
    <div class="stat-info">
      <div class="stat-value"><?php echo $total; ?></div>
      <div class="stat-label">Total Ditampilkan</div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header" style="flex-wrap:wrap;gap:12px;">
    <span class="card-title">Riwayat Transaksi</span>
    <form method="get" class="search-bar" style="margin:0;flex-wrap:wrap;">
      <div class="search-input-wrap" style="min-width:180px;">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" class="form-control" placeholder="Kode / kasir..." value="<?php echo e($search); ?>">
      </div>
      <input type="date" name="from" class="form-control" style="width:auto;" value="<?php echo e($dateFrom); ?>">
      <input type="date" name="to" class="form-control" style="width:auto;" value="<?php echo e($dateTo); ?>">
      <button type="submit" class="btn btn-outline">Filter</button>
      <?php if ($search || $dateFrom || $dateTo): ?>
      <a href="transaksi.php" class="btn btn-ghost">Reset</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Kode</th>
          <th>Kasir</th>
          <th>Grand Total</th>
          <th>Bayar</th>
          <th>Kembalian</th>
          <th>Waktu</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($trxs)): ?>
        <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--gray-400);">Belum ada transaksi</td></tr>
        <?php else: ?>
        <?php foreach ($trxs as $i => $t): ?>
        <tr>
          <td class="text-muted text-mono" style="font-size:0.75rem;"><?php echo $offset + $i + 1; ?></td>
          <td><span class="sku-tag"><?php echo e($t['kode']); ?></span></td>
          <td><?php echo e($t['kasir_name']); ?></td>
          <td class="text-mono font-bold"><?php echo formatRupiah($t['grand_total']); ?></td>
          <td class="text-mono"><?php echo formatRupiah($t['amount_paid']); ?></td>
          <td class="text-mono"><?php echo formatRupiah($t['change_amount']); ?></td>
          <td class="text-muted" style="font-size:0.8rem;white-space:nowrap;"><?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="detail-transaksi.php?id=<?php echo $t['id']; ?>" class="btn btn-ghost btn-sm" title="Detail">⊞</a>
              <a href="struk-pdf.php?id=<?php echo $t['id']; ?>" class="btn btn-ghost btn-sm" title="PDF Struk" target="_blank">↓</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php
    $baseUrl = 'transaksi.php?q=' . urlencode($search) . '&from=' . urlencode($dateFrom) . '&to=' . urlencode($dateTo);
    if ($page > 1): ?>
    <a href="<?php echo $baseUrl . '&page=' . ($page - 1); ?>" class="page-link">‹</a>
    <?php endif; ?>
    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
    <a href="<?php echo $baseUrl . '&page=' . $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
    <a href="<?php echo $baseUrl . '&page=' . ($page + 1); ?>" class="page-link">›</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
