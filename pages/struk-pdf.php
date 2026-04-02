<?php
require_once '../config.php';
requireLogin();

$conn = db();
$id = (int)($_GET['id'] ?? 0);
$kode = sanitize($_GET['kode'] ?? '');

if ($id) {
    $trxRes = mysqli_query($conn, "SELECT t.*, k.username as kasir_name FROM transaksi t JOIN kasir k ON t.kasir_id = k.id WHERE t.id = $id LIMIT 1");
} elseif ($kode) {
    $trxRes = mysqli_query($conn, "SELECT t.*, k.username as kasir_name FROM transaksi t JOIN kasir k ON t.kasir_id = k.id WHERE t.kode = '$kode' LIMIT 1");
} else {
    header('Location: ' . BASE_URL . '/pages/transaksi.php');
    exit;
}

$trx = mysqli_fetch_assoc($trxRes);
if (!$trx) { header('Location: ' . BASE_URL . '/pages/transaksi.php'); exit; }

$detailRes = mysqli_query($conn, "SELECT * FROM transaksi_detail WHERE transaksi_id = " . (int)$trx['id'] . " ORDER BY id");
$details = [];
while ($r = mysqli_fetch_assoc($detailRes)) $details[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Struk <?php echo e($trx['kode']); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Space Mono', 'Courier New', monospace;
  font-size: 12px;
  background: #f0f0f0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
  padding: 24px;
}
.receipt {
  background: #fff;
  width: 80mm;
  min-width: 300px;
  padding: 16px 14px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.12);
  border-radius: 4px;
}
.r-center { text-align: center; }
.r-store { font-weight: 700; font-size: 14px; }
.r-sub { font-size: 10px; color: #666; }
.r-divider { border: none; border-top: 1px dashed #ccc; margin: 8px 0; }
.r-row { display: flex; justify-content: space-between; padding: 2px 0; }
.r-item-name { font-size: 11px; margin-top: 4px; }
.r-total { font-weight: 700; font-size: 13px; }
.r-footer { text-align: center; font-size: 10px; color: #888; margin-top: 12px; }
.r-kode { font-size: 11px; }

.no-print { text-align: center; margin-bottom: 16px; }
.btn-print {
  background: #0a0a0a;
  color: #fff;
  border: none;
  padding: 8px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-family: 'Space Mono', monospace;
  font-size: 12px;
  margin-right: 8px;
}
.btn-back {
  background: transparent;
  color: #0a0a0a;
  border: 1px solid #ccc;
  padding: 8px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-family: 'Space Mono', monospace;
  font-size: 12px;
  text-decoration: none;
}

@media print {
  body { background: #fff; padding: 0; }
  .receipt { box-shadow: none; border-radius: 0; padding: 4px; }
  .no-print { display: none !important; }
}
</style>
</head>
<body>
<div>
  <div class="no-print">
    <button class="btn-print" onclick="window.print()">⎙ Print</button>
    <a class="btn-back" href="javascript:history.back()">← Kembali</a>
  </div>

  <div class="receipt">
    <div class="r-center">
      <div class="r-store"><?php echo APP_NAME; ?></div>
      <div class="r-sub"><?php echo APP_LOCATION; ?></div>
      <div class="r-sub"><?php echo date('d/m/Y H:i:s', strtotime($trx['created_at'])); ?></div>
    </div>
    <hr class="r-divider">
    <div class="r-row r-kode"><span>No.</span><span><?php echo e($trx['kode']); ?></span></div>
    <div class="r-row"><span>Kasir</span><span><?php echo e($trx['kasir_name']); ?></span></div>
    <hr class="r-divider">

    <?php foreach ($details as $d): ?>
    <div class="r-item-name"><?php echo e($d['name']); ?> x<?php echo $d['qty']; ?></div>
    <div class="r-row" style="padding-left:8px;color:#444;">
      <span>
        Rp <?php echo number_format($d['price'],0,',','.'); ?>
        <?php if ($d['discount'] > 0): ?> (-<?php echo $d['discount']; ?>%)<?php endif; ?>
      </span>
      <span>Rp <?php echo number_format($d['subtotal'],0,',','.'); ?></span>
    </div>
    <?php endforeach; ?>

    <hr class="r-divider">
    <div class="r-row r-total">
      <span>TOTAL</span>
      <span>Rp <?php echo number_format($trx['grand_total'],0,',','.'); ?></span>
    </div>
    <div class="r-row">
      <span>Bayar</span>
      <span>Rp <?php echo number_format($trx['amount_paid'],0,',','.'); ?></span>
    </div>
    <div class="r-row">
      <span>Kembalian</span>
      <span>Rp <?php echo number_format($trx['change_amount'],0,',','.'); ?></span>
    </div>
    <hr class="r-divider">
    <div class="r-footer">
      <div>Terima kasih telah berbelanja!</div>
      <div><?php echo APP_NAME; ?> — <?php echo APP_LOCATION; ?></div>
    </div>
  </div>
</div>
</body>
</html>
