<?php
require_once '../config.php';
requireLogin();

$pageTitle = 'Kasir POS';
$extraJs = ['kasir.js'];

$user = currentUser();
$conn = db();

// Handle checkout POST
$receiptData = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $cartJson  = $_POST['cart_data'] ?? '[]';
    $paid      = floatval(preg_replace('/[^0-9.]/', '', $_POST['amount_paid'] ?? '0'));
    $total     = floatval(preg_replace('/[^0-9.]/', '', $_POST['grand_total'] ?? '0'));
    $change    = $paid - $total;
    $cartItems = json_decode($cartJson, true);

    if ($cartItems && count($cartItems) > 0 && $paid >= $total && $total > 0) {
        $kode = generateTrxCode();
        $kasirId = (int)$_SESSION['kasir_id'];
        $kodeS = sanitize($kode);

        mysqli_query($conn, "INSERT INTO transaksi (kode, kasir_id, grand_total, amount_paid, change_amount)
            VALUES ('$kodeS', $kasirId, $total, $paid, $change)");
        $trxId = mysqli_insert_id($conn);

        $receiptItems = [];
        foreach ($cartItems as $item) {
            $prodId    = (int)$item['id'];
            $qty       = (int)$item['qty'];
            $price     = floatval($item['price']);
            $discount  = floatval($item['discount'] ?? 0);
            $subtotal  = $price * (1 - $discount / 100) * $qty;
            $nameS     = sanitize($item['name']);
            $skuS      = sanitize($item['sku']);

            mysqli_query($conn, "INSERT INTO transaksi_detail (transaksi_id, produk_id, name, sku, price, discount, qty, subtotal)
                VALUES ($trxId, $prodId, '$nameS', '$skuS', $price, $discount, $qty, $subtotal)");

            mysqli_query($conn, "UPDATE produk SET stock = stock - $qty, sold = sold + $qty WHERE id = $prodId AND stock >= $qty");

            $receiptItems[] = ['name' => $item['name'], 'qty' => $qty, 'price' => $price, 'discount' => $discount, 'subtotal' => $subtotal];
        }

        $receiptData = [
            'kode'    => $kode,
            'items'   => $receiptItems,
            'total'   => $total,
            'paid'    => $paid,
            'change'  => $change,
            'kasir'   => $user['username'],
            'date'    => date('d/m/Y H:i'),
        ];
    }
}

// Load products
$produksRes = mysqli_query($conn, "SELECT p.*, k.nama as kategori_nama FROM produk p JOIN kategori k ON p.kategori_id = k.id ORDER BY k.nama, p.name");
$produks = [];
while ($r = mysqli_fetch_assoc($produksRes)) $produks[] = $r;

// Load categories for filter
$katsRes = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama");
$kats = [];
while ($r = mysqli_fetch_assoc($katsRes)) $kats[] = $r;

include '../includes/header.php';
?>

<?php if ($receiptData): ?>
<!-- Receipt modal auto-open after transaction -->
<div class="modal-backdrop open" id="receiptModal">
  <div class="modal" style="max-width:380px;">
    <div class="modal-header">
      <span class="modal-title">✓ Transaksi Berhasil</span>
      <button class="modal-close" data-close-modal="receiptModal">✕</button>
    </div>
    <div class="modal-body">
      <div class="receipt-wrap print-area" id="receiptContent">
        <div class="receipt-header">
          <div class="receipt-store"><?php echo APP_NAME; ?></div>
          <div class="receipt-sub"><?php echo APP_LOCATION; ?></div>
          <div class="receipt-sub"><?php echo e($receiptData['date']); ?></div>
        </div>
        <hr class="receipt-divider">
        <div class="receipt-row"><span>No. Transaksi</span><span><?php echo e($receiptData['kode']); ?></span></div>
        <div class="receipt-row"><span>Kasir</span><span><?php echo e($receiptData['kasir']); ?></span></div>
        <hr class="receipt-divider">
        <?php foreach ($receiptData['items'] as $ri): ?>
        <div class="receipt-item-name"><?php echo e($ri['name']); ?> x<?php echo $ri['qty']; ?></div>
        <div class="receipt-row" style="padding-left:10px;">
          <span>
            <?php echo formatRupiah($ri['price']); ?>
            <?php if ($ri['discount'] > 0): ?> (-<?php echo $ri['discount']; ?>%)<?php endif; ?>
          </span>
          <span><?php echo formatRupiah($ri['subtotal']); ?></span>
        </div>
        <?php endforeach; ?>
        <hr class="receipt-divider">
        <div class="receipt-row receipt-total"><span>TOTAL</span><span><?php echo formatRupiah($receiptData['total']); ?></span></div>
        <div class="receipt-row"><span>Bayar</span><span><?php echo formatRupiah($receiptData['paid']); ?></span></div>
        <div class="receipt-row"><span>Kembalian</span><span><?php echo formatRupiah($receiptData['change']); ?></span></div>
        <div class="receipt-footer">
          <div>Terima kasih telah berbelanja!</div>
          <div><?php echo APP_NAME; ?> — <?php echo APP_LOCATION; ?></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="printReceipt()">⎙ Print</button>
      <button class="btn btn-primary btn-sm" onclick="downloadReceiptPdf()">↓ PDF</button>
      <button class="btn btn-ghost btn-sm" data-close-modal="receiptModal">Tutup</button>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- POS Layout -->
<div class="pos-layout">
  <!-- LEFT: Products -->
  <div class="pos-left">
    <!-- SKU Input -->
    <div class="card">
      <div class="card-body" style="padding:14px 16px;">
        <div class="sku-search-bar">
          <div class="sku-input-wrap">
            <input type="text" id="skuInput" class="form-control" placeholder="Masukkan SKU produk..." style="font-family:var(--font-mono);letter-spacing:0.04em;text-transform:uppercase;">
          </div>
          <button class="btn btn-primary" id="btnAddSku">+ Tambah</button>
        </div>
        <div id="skuError" class="form-error" style="display:none;"></div>
      </div>
    </div>

    <!-- Product Grid -->
    <div class="card" style="flex:1;">
      <div class="card-header">
        <span class="card-title">Produk</span>
        <div class="search-bar">
          <div class="search-input-wrap" style="min-width:180px;">
            <span class="search-icon">🔍</span>
            <input type="text" id="prodSearch" class="form-control" placeholder="Cari nama / SKU...">
          </div>
        </div>
      </div>
      <div class="card-body">
        <?php if (empty($produks)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">⊡</div>
          <div class="empty-state-text">Belum ada produk</div>
        </div>
        <?php else: ?>
        <div class="product-grid">
          <?php foreach ($produks as $p): 
            $outOfStock = $p['stock'] <= 0;
            $finalPrice = $p['price'] * (1 - $p['discount'] / 100);
          ?>
          <div class="product-card <?php echo $outOfStock ? 'out-of-stock' : ''; ?>"
            data-id="<?php echo $p['id']; ?>"
            data-sku="<?php echo e($p['sku']); ?>"
            data-name="<?php echo e($p['name']); ?>"
            data-price="<?php echo $p['price']; ?>"
            data-discount="<?php echo $p['discount']; ?>"
            data-stock="<?php echo $p['stock']; ?>">
            <?php if ($p['discount'] > 0): ?>
            <div class="pc-discount-badge"><?php echo $p['discount']; ?>%</div>
            <?php endif; ?>
            <div class="pc-sku"><?php echo e($p['sku']); ?></div>
            <div class="pc-name"><?php echo e($p['name']); ?></div>
            <div class="pc-price"><?php echo formatRupiah($finalPrice); ?></div>
            <div class="pc-stock">Stok: <?php echo $p['stock']; ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- RIGHT: Cart -->
  <div class="pos-right">
    <div class="card cart-container">
      <div class="cart-header">
        <span class="cart-title">Keranjang</span>
        <span class="cart-count" id="cartCount">0</span>
      </div>
      <div class="cart-items" id="cartItems">
        <div class="cart-empty" id="cartEmpty">Keranjang kosong</div>
      </div>
      <div class="cart-summary">
        <div class="cart-row"><span>Subtotal</span><span id="cartSubtotal">Rp 0</span></div>
        <div class="cart-row"><span>Diskon</span><span id="cartDiscount">-Rp 0</span></div>
        <div class="cart-row total"><span>TOTAL</span><span id="cartTotal">Rp 0</span></div>
      </div>
      <div class="cart-actions" style="display:flex;flex-direction:column;gap:8px;">
        <button class="btn btn-primary btn-block btn-lg" id="btnCheckout" disabled>Bayar</button>
        <button class="btn btn-outline btn-block btn-sm" id="btnClearCart" style="display:none;">Hapus Semua</button>
      </div>
    </div>
  </div>
</div>

<!-- Payment Modal -->
<div class="modal-backdrop" id="paymentModal">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <span class="modal-title">Pembayaran</span>
      <button class="modal-close" data-close-modal="paymentModal">✕</button>
    </div>
    <div class="modal-body">
      <div class="payment-info">
        <div class="pay-row"><span>Subtotal</span><span id="pmSubtotal">Rp 0</span></div>
        <div class="pay-row"><span>Diskon</span><span id="pmDiscount">Rp 0</span></div>
        <div class="pay-row total"><span>TOTAL</span><span id="pmTotal">Rp 0</span></div>
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah Bayar <span class="req">*</span></label>
        <input type="number" id="pmAmountPaid" class="form-control" placeholder="0" min="0" style="font-family:var(--font-mono);font-size:1.1rem;font-weight:700;">
      </div>
      <div class="change-display">
        <span>Kembalian</span>
        <span id="pmChange">Rp 0</span>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" data-close-modal="paymentModal">Batal</button>
      <button class="btn btn-success" id="btnConfirmPayment" disabled>✓ Konfirmasi</button>
    </div>
  </div>
</div>

<!-- Hidden form for checkout submission -->
<form method="post" id="checkoutForm" style="display:none;">
  <input type="hidden" name="action" value="checkout">
  <input type="hidden" name="cart_data" id="hiddenCartData">
  <input type="hidden" name="grand_total" id="hiddenTotal">
  <input type="hidden" name="amount_paid" id="hiddenPaid">
  <input type="hidden" name="change_amount" id="hiddenChange">
</form>

<!-- Hidden cart data for kasir.js -->
<input type="hidden" id="cartDataInput">

<script>
function printReceipt() {
  var content = document.getElementById('receiptContent').innerHTML;
  var w = window.open('', '_blank', 'width=400,height=600');
  w.document.write('<html><head><title>Struk - <?php echo APP_NAME; ?></title>');
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

function downloadReceiptPdf() {
  var trxId = <?php echo $receiptData ? "'" . e($receiptData['kode']) . "'" : "null"; ?>;
  if (!trxId) return;
  // Find the transaction by code and redirect to PDF
  window.location.href = '<?php echo BASE_URL; ?>/pages/struk-pdf.php?kode=' + encodeURIComponent(trxId);
}
</script>

<?php include '../includes/footer.php'; ?>
