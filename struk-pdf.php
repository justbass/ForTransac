<?php
require_once '../config.php';
requireLogin();

$conn = db();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . '/pages/transaksi.php'); exit; }

$trxRes = mysqli_query($conn, "SELECT t.*, k.username as kasir_name FROM transaksi t JOIN kasir k ON t.kasir_id = k.id WHERE t.id = $id LIMIT 1");
$trx = mysqli_fetch_assoc($trxRes);
if (!$trx) { header('Location: ' . BASE_URL . '/pages/transaksi.php'); exit; }

$detailRes = mysqli_query($conn, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id ORDER BY id");
$details = [];
while ($r = mysqli_fetch_assoc($detailRes)) $details[] = $r;

$pageTitle = 'Detail — ' . $trx['kode'];
include '../includes/header.php';
?>

<div style="margin-bottom:16px;">
  <a href="transaksi.php" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div style="display:grid;grid-template-columns:1fr auto;gap:16px;align-items:start;flex-wrap:wrap;" class="mb-16">
  <div>
    <div class="text-mono font-bold" style="font-size:1.1rem;"><?php echo e($trx['kode']); ?></div>
    <div class="text-muted text-sm"><?php echo date('d/m/Y H:i:s', strtotime($trx['created_at'])); ?></div>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="struk-pdf.php?id=<?php echo $trx['id']; ?>" class="btn btn-outline btn-sm" target="_blank">↓ PDF Struk</a>
    <button class="btn btn-primary btn-sm" onclick="printReceipt()">⎙ Print</button>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;" class="resp-grid">
  <div class="card">
    <div class="card-header"><span class="card-title">Info Transaksi</span></div>
    <div class="card-body">
      <div class="profile-detail-row"><span class="pdr-label">Kode</span><span class="text-mono"><?php echo e($trx['kode']); ?></span></div>
      <div class="profile-detail-row"><span class="pdr-label">Kasir</span><span><?php echo e($trx['kasir_name']); ?></span></div>
      <div class="profile-detail-row"><span class="pdr-label">Waktu</span><span><?php echo date('d/m/Y H:i', strtotime($trx['created_at'])); ?></span></div>
      <div class="profile-detail-row"><span class="pdr-label">Grand Total</span><span class="text-mono font-bold"><?php echo formatRupiah($trx['grand_total']); ?></span></div>
      <div class="profile-detail-row"><span class="pdr-label">Dibayar</span><span class="text-mono"><?php echo formatRupiah($trx['amount_paid']); ?></span></div>
      <div class="profile-detail-row"><span class="pdr-label">Kembalian</span><span class="text-mono"><?php echo formatRupiah($trx['change_amount']); ?></span></div>
    </div>
  </div>

  <!-- Receipt preview -->
  <div class="card">
    <div class="card-header"><span class="card-title">Preview Struk</span></div>
    <div class="card-body">
      <div class="receipt-wrap" id="receiptContent">
        <div class="receipt-header">
          <div class="receipt-store"><?php echo APP_NAME; ?></div>
          <div class="receipt-sub"><?php echo APP_LOCATION; ?></div>
          <div class="receipt-sub"><?php echo date('d/m/Y H:i', strtotime($trx['created_at'])); ?></div>
        </div>
        <hr class="receipt-divider">
        <div class="receipt-row"><span>No.</span><span><?php echo e($trx['kode']); ?></span></div>
        <div class="receipt-row"><span>Kasir</span><span><?php echo e($trx['kasir_name']); ?></span></div>
        <hr class="receipt-divider">
        <?php foreach ($details as $d): ?>
        <div class="receipt-item-name"><?php echo e($d['name']); ?> x<?php echo $d['qty']; ?></div>
        <div class="receipt-row" style="padding-left:10px;">
          <span><?php echo formatRupiah($d['price']); ?><?php if ($d['discount'] > 0): ?> (-<?php echo $d['discount']; ?>%)<?php endif; ?></span>
          <span><?php echo formatRupiah($d['subtotal']); ?></span>
        </div>
        <?php endforeach; ?>
        <hr class="receipt-divider">
        <div class="receipt-row receipt-total"><span>TOTAL</span><span><?php echo formatRupiah($trx['grand_total']); ?></span></div>
        <div class="receipt-row"><span>Bayar</span><span><?php echo formatRupiah($trx['amount_paid']); ?></span></div>
        <div class="receipt-row"><span>Kembalian</span><span><?php echo formatRupiah($trx['change_amount']); ?></span></div>
        <div class="receipt-footer">
          <div>Terima kasih telah berbelanja!</div>
          <div><?php echo APP_NAME; ?> — <?php echo APP_LOCATION; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Products detail -->
<div class="card">
  <div class="card-header"><span class="card-title">Item Transaksi</span></div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>SKU</th>
          <th>Nama Produk</th>
          <th>Harga Satuan</th>
          <th>Diskon</th>
          <th>Qty</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($details as $i => $d): ?>
        <tr>
          <td class="text-muted text-mono" style="font-size:0.75rem;"><?php echo $i + 1; ?></td>
          <td><span class="sku-tag"><?php echo e($d['sku']); ?></span></td>
          <td style="font-weight:600;"><?php echo e($d['name']); ?></td>
          <td class="text-mono"><?php echo formatRupiah($d['price']); ?></td>
          <td><?php echo $d['discount'] > 0 ? '<span class="badge badge-success">' . $d['discount'] . '%</span>' : '—'; ?></td>
          <td class="text-mono font-bold"><?php echo $d['qty']; ?></td>
          <td class="text-mono font-bold"><?php echo formatRupiah($d['subtotal']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function printReceipt() {
  var content = document.getElementById('receiptContent').innerHTML;
  var w = window.open('', '_blank', 'width=400,height=600');
  w.document.write('<html><head><title>Struk - <?php echo e($trx["kode"]); ?></title>');
  w.document.write('<style>body{font-family:"Courier New",monospace;font-size:11px;padding:10px;max-width:80mm;}');
  w.document.write('.receipt-store{font-weight:700;font-size:14px;text-align:center;}');
  w.document.write('.receipt-sub,.receipt-footer{text-align:center;color:#666;font-size:10px;}');
  w.document.write('.receipt-row{display:flex;justify-content:space-between;padding:2px 0;}');
  w.document.write('.receipt-divider{border:none;border-top:1px dashed #ccc;margin:6px 0;}');
  w.document.write('.receipt-total{font-weight:700;}');
  w.document.write('.receipt-item-name{font-size:10px;}');
  w.document.write('</style></head><body>');
  w.document.write(content);
  w.document.write('</body></html>');
  w.document.close();
  w.focus();
  setTimeout(function(){ w.print(); w.close(); }, 300);
}
</script>

<style>
@media (max-width:640px) {
  .resp-grid { grid-template-columns: 1fr !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
