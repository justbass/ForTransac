<?php
require_once '../config.php';
requireLogin();

$pageTitle = 'Produk';
$conn = db();

// Actions
$action = $_POST['action'] ?? '';
$error = '';
$success = '';

if ($action === 'add') {
    $name       = sanitize($_POST['name'] ?? '');
    $katId      = (int)($_POST['kategori_id'] ?? 0);
    $discount   = floatval($_POST['discount'] ?? 0);
    $berat      = (int)($_POST['berat'] ?? 0);
    $price      = floatval(preg_replace('/[^0-9.]/', '', $_POST['price'] ?? '0'));
    $stock      = (int)($_POST['stock'] ?? 0);

    if (!$name || !$katId || !$price) {
        $error = 'Nama, kategori, dan harga wajib diisi.';
    } else {
        // Get alias
        $katRes = mysqli_query($conn, "SELECT alias FROM kategori WHERE id = $katId LIMIT 1");
        $kat = mysqli_fetch_assoc($katRes);
        $sku = generateSKU($kat['alias'], $name, $berat);
        // Check duplicate SKU
        $chk = mysqli_query($conn, "SELECT id FROM produk WHERE sku = '" . sanitize($sku) . "' LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            $skuBase = sanitize($sku);
            $sku = $sku . '-' . rand(100, 999);
        }
        $skuS = sanitize($sku);
        mysqli_query($conn, "INSERT INTO produk (name, sku, discount, kategori_id, berat, price, stock) VALUES ('$name', '$skuS', $discount, $katId, $berat, $price, $stock)");
        setFlash('success', 'Produk berhasil ditambahkan. SKU: ' . $sku);
        header('Location: ' . BASE_URL . '/pages/produk.php');
        exit;
    }
}

if ($action === 'edit') {
    $id         = (int)($_POST['id'] ?? 0);
    $name       = sanitize($_POST['name'] ?? '');
    $katId      = (int)($_POST['kategori_id'] ?? 0);
    $discount   = floatval($_POST['discount'] ?? 0);
    $berat      = (int)($_POST['berat'] ?? 0);
    $price      = floatval(preg_replace('/[^0-9.]/', '', $_POST['price'] ?? '0'));
    $stock      = (int)($_POST['stock'] ?? 0);

    if (!$id || !$name || !$katId || !$price) {
        $error = 'Data tidak lengkap.';
    } else {
        $katRes = mysqli_query($conn, "SELECT alias FROM kategori WHERE id = $katId LIMIT 1");
        $kat = mysqli_fetch_assoc($katRes);
        $sku = generateSKU($kat['alias'], $name, $berat);
        // Check duplicate SKU (exclude self)
        $skuS = sanitize($sku);
        $chk = mysqli_query($conn, "SELECT id FROM produk WHERE sku = '$skuS' AND id != $id LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            $sku = $sku . '-' . rand(100, 999);
            $skuS = sanitize($sku);
        }
        mysqli_query($conn, "UPDATE produk SET name='$name', sku='$skuS', discount=$discount, kategori_id=$katId, berat=$berat, price=$price, stock=$stock WHERE id=$id");
        setFlash('success', 'Produk berhasil diperbarui.');
        header('Location: ' . BASE_URL . '/pages/produk.php');
        exit;
    }
}

if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $chk = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM transaksi_detail WHERE produk_id = $id");
        $r = mysqli_fetch_assoc($chk);
        if ($r['cnt'] > 0) {
            setFlash('error', 'Produk tidak dapat dihapus karena sudah ada di riwayat transaksi.');
        } else {
            mysqli_query($conn, "DELETE FROM produk WHERE id = $id");
            setFlash('success', 'Produk berhasil dihapus.');
        }
        header('Location: ' . BASE_URL . '/pages/produk.php');
        exit;
    }
}

// Pagination
$perPage = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$search = sanitize($_GET['q'] ?? '');
$katFilter = (int)($_GET['kat'] ?? 0);

$where = "WHERE 1=1";
if ($search) $where .= " AND (p.name LIKE '%$search%' OR p.sku LIKE '%$search%')";
if ($katFilter) $where .= " AND p.kategori_id = $katFilter";

$totalRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM produk p $where");
$total = mysqli_fetch_assoc($totalRes)['cnt'];
$totalPages = max(1, ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$produksRes = mysqli_query($conn, "SELECT p.*, k.nama as kategori_nama FROM produk p JOIN kategori k ON p.kategori_id = k.id $where ORDER BY p.created_at DESC LIMIT $perPage OFFSET $offset");
$produks = [];
while ($r = mysqli_fetch_assoc($produksRes)) $produks[] = $r;

$katsRes = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama");
$kats = [];
while ($r = mysqli_fetch_assoc($katsRes)) $kats[] = $r;

include '../includes/header.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <div>
    <div style="font-size:0.8rem;color:var(--gray-400);">Total: <?php echo $total; ?> produk</div>
  </div>
  <button class="btn btn-primary" data-open-modal="addProdukModal">+ Tambah Produk</button>
</div>

<?php if ($error): ?>
<div class="flash flash-error" style="margin-bottom:16px;"><span><?php echo e($error); ?></span></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title">Daftar Produk</span>
    <form method="get" class="search-bar" style="margin:0;">
      <div class="search-input-wrap">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" class="form-control" placeholder="Cari nama/SKU..." value="<?php echo e($search); ?>">
      </div>
      <select name="kat" class="form-control" style="width:auto;min-width:130px;">
        <option value="">Semua Kategori</option>
        <?php foreach ($kats as $k): ?>
        <option value="<?php echo $k['id']; ?>" <?php echo $katFilter == $k['id'] ? 'selected' : ''; ?>><?php echo e($k['nama']); ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-outline">Filter</button>
      <?php if ($search || $katFilter): ?>
      <a href="produk.php" class="btn btn-ghost">Reset</a>
      <?php endif; ?>
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>SKU</th>
          <th>Nama</th>
          <th>Kategori</th>
          <th>Berat</th>
          <th>Harga</th>
          <th>Diskon</th>
          <th>Stok</th>
          <th>Terjual</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($produks)): ?>
        <tr><td colspan="10" style="text-align:center;padding:32px;color:var(--gray-400);">Belum ada produk</td></tr>
        <?php else: ?>
        <?php foreach ($produks as $i => $p): ?>
        <tr>
          <td class="text-muted text-mono" style="font-size:0.75rem;"><?php echo $offset + $i + 1; ?></td>
          <td><span class="sku-tag"><?php echo e($p['sku']); ?></span></td>
          <td style="font-weight:600;"><?php echo e($p['name']); ?></td>
          <td><?php echo e($p['kategori_nama']); ?></td>
          <td class="text-mono"><?php echo $p['berat']; ?>g</td>
          <td class="text-mono"><?php echo formatRupiah($p['price']); ?></td>
          <td><?php echo $p['discount'] > 0 ? '<span class="badge badge-success">' . $p['discount'] . '%</span>' : '<span class="text-muted">—</span>'; ?></td>
          <td>
            <?php if ($p['stock'] <= 0): ?>
            <span class="badge badge-danger">Habis</span>
            <?php elseif ($p['stock'] <= 5): ?>
            <span class="badge badge-warning"><?php echo $p['stock']; ?></span>
            <?php else: ?>
            <span><?php echo $p['stock']; ?></span>
            <?php endif; ?>
          </td>
          <td class="text-mono"><?php echo $p['sold']; ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <button class="btn btn-ghost btn-sm" onclick='editProduk(<?php echo json_encode($p); ?>)' title="Edit">✎</button>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);" data-confirm="Hapus produk '<?php echo e($p['name']); ?>'?" title="Hapus">✕</button>
              </form>
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
    $baseUrl = 'produk.php?q=' . urlencode($search) . '&kat=' . $katFilter;
    for ($i = 1; $i <= $totalPages; $i++):
    ?>
    <a href="<?php echo $baseUrl . '&page=' . $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Modal -->
<div class="modal-backdrop" id="addProdukModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Tambah Produk</span>
      <button class="modal-close" data-close-modal="addProdukModal">✕</button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Produk <span class="req">*</span></label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kategori <span class="req">*</span></label>
            <select name="kategori_id" class="form-control" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($kats as $k): ?>
              <option value="<?php echo $k['id']; ?>"><?php echo e($k['nama']); ?> (<?php echo e($k['alias']); ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Berat (gram)</label>
            <input type="number" name="berat" class="form-control" value="0" min="0">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Harga <span class="req">*</span></label>
            <input type="number" name="price" class="form-control" min="0" step="100" required>
          </div>
          <div class="form-group">
            <label class="form-label">Diskon (%)</label>
            <input type="number" name="discount" class="form-control" value="0" min="0" max="100" step="0.5">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Stok Awal</label>
          <input type="number" name="stock" class="form-control" value="0" min="0">
        </div>
        <div class="form-hint">SKU akan dibuat otomatis: ALIAS-NAMA-BERAT</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-close-modal="addProdukModal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-backdrop" id="editProdukModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Produk</span>
      <button class="modal-close" data-close-modal="editProdukModal">✕</button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="editId">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">SKU (auto-generate)</label>
          <input type="text" id="editSku" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Produk <span class="req">*</span></label>
          <input type="text" name="name" id="editName" class="form-control" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kategori <span class="req">*</span></label>
            <select name="kategori_id" id="editKategori" class="form-control" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($kats as $k): ?>
              <option value="<?php echo $k['id']; ?>"><?php echo e($k['nama']); ?> (<?php echo e($k['alias']); ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Berat (gram)</label>
            <input type="number" name="berat" id="editBerat" class="form-control" min="0">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Harga <span class="req">*</span></label>
            <input type="number" name="price" id="editPrice" class="form-control" min="0" step="100" required>
          </div>
          <div class="form-group">
            <label class="form-label">Diskon (%)</label>
            <input type="number" name="discount" id="editDiscount" class="form-control" min="0" max="100" step="0.5">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Stok</label>
          <input type="number" name="stock" id="editStock" class="form-control" min="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-close-modal="editProdukModal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function editProduk(p) {
  document.getElementById('editId').value = p.id;
  document.getElementById('editName').value = p.name;
  document.getElementById('editKategori').value = p.kategori_id;
  document.getElementById('editBerat').value = p.berat;
  document.getElementById('editPrice').value = p.price;
  document.getElementById('editDiscount').value = p.discount;
  document.getElementById('editStock').value = p.stock;
  document.getElementById('editSku').value = p.sku;
  openModal('editProdukModal');
}
</script>

<?php include '../includes/footer.php'; ?>
