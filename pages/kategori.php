<?php
require_once '../config.php';
requireLogin();

$pageTitle = 'Kategori';
$conn = db();

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $nama  = sanitize($_POST['nama'] ?? '');
    $alias = strtoupper(sanitize($_POST['alias'] ?? ''));
    if (!$nama || strlen($alias) !== 3) {
        setFlash('error', 'Nama dan alias (3 karakter) wajib diisi.');
    } else {
        $chk = mysqli_query($conn, "SELECT id FROM kategori WHERE alias = '$alias' LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            setFlash('error', 'Alias sudah digunakan.');
        } else {
            mysqli_query($conn, "INSERT INTO kategori (nama, alias) VALUES ('$nama', '$alias')");
            setFlash('success', 'Kategori berhasil ditambahkan.');
        }
    }
    header('Location: ' . BASE_URL . '/pages/kategori.php');
    exit;
}

if ($action === 'edit') {
    $id    = (int)($_POST['id'] ?? 0);
    $nama  = sanitize($_POST['nama'] ?? '');
    $alias = strtoupper(sanitize($_POST['alias'] ?? ''));
    if (!$id || !$nama || strlen($alias) !== 3) {
        setFlash('error', 'Data tidak valid.');
    } else {
        $chk = mysqli_query($conn, "SELECT id FROM kategori WHERE alias = '$alias' AND id != $id LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            setFlash('error', 'Alias sudah digunakan oleh kategori lain.');
        } else {
            mysqli_query($conn, "UPDATE kategori SET nama='$nama', alias='$alias' WHERE id=$id");
            setFlash('success', 'Kategori berhasil diperbarui.');
        }
    }
    header('Location: ' . BASE_URL . '/pages/kategori.php');
    exit;
}

if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $chk = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM produk WHERE kategori_id = $id");
        $r = mysqli_fetch_assoc($chk);
        if ($r['cnt'] > 0) {
            setFlash('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        } else {
            mysqli_query($conn, "DELETE FROM kategori WHERE id = $id");
            setFlash('success', 'Kategori berhasil dihapus.');
        }
    }
    header('Location: ' . BASE_URL . '/pages/kategori.php');
    exit;
}

$katsRes = mysqli_query($conn, "SELECT k.*, COUNT(p.id) as produk_count FROM kategori k LEFT JOIN produk p ON p.kategori_id = k.id GROUP BY k.id ORDER BY k.nama");
$kats = [];
while ($r = mysqli_fetch_assoc($katsRes)) $kats[] = $r;

include '../includes/header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <div style="font-size:0.8rem;color:var(--gray-400);"><?php echo count($kats); ?> kategori</div>
  <button class="btn btn-primary" data-open-modal="addKatModal">+ Tambah Kategori</button>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Daftar Kategori</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Alias (SKU Prefix)</th>
          <th>Jumlah Produk</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($kats)): ?>
        <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--gray-400);">Belum ada kategori</td></tr>
        <?php else: ?>
        <?php foreach ($kats as $i => $k): ?>
        <tr>
          <td class="text-muted text-mono" style="font-size:0.75rem;"><?php echo $i + 1; ?></td>
          <td style="font-weight:600;"><?php echo e($k['nama']); ?></td>
          <td><span class="badge badge-dark"><?php echo e($k['alias']); ?></span></td>
          <td><?php echo $k['produk_count']; ?> produk</td>
          <td>
            <div style="display:flex;gap:6px;">
              <button class="btn btn-ghost btn-sm" onclick='editKat(<?php echo json_encode($k); ?>)'>✎</button>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);" data-confirm="Hapus kategori '<?php echo e($k['nama']); ?>'?">✕</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Modal -->
<div class="modal-backdrop" id="addKatModal">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <span class="modal-title">Tambah Kategori</span>
      <button class="modal-close" data-close-modal="addKatModal">✕</button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Kategori <span class="req">*</span></label>
          <input type="text" name="nama" class="form-control" required autofocus>
        </div>
        <div class="form-group">
          <label class="form-label">Alias SKU (3 karakter) <span class="req">*</span></label>
          <input type="text" name="alias" class="form-control" maxlength="3" style="text-transform:uppercase;font-family:var(--font-mono);letter-spacing:0.1em;" required placeholder="e.g. MNM">
          <div class="form-hint">Digunakan sebagai prefix SKU produk. Contoh: MNM, MKN, SNK</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-close-modal="addKatModal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-backdrop" id="editKatModal">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <span class="modal-title">Edit Kategori</span>
      <button class="modal-close" data-close-modal="editKatModal">✕</button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="editKatId">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Kategori <span class="req">*</span></label>
          <input type="text" name="nama" id="editKatNama" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Alias SKU (3 karakter) <span class="req">*</span></label>
          <input type="text" name="alias" id="editKatAlias" class="form-control" maxlength="3" style="text-transform:uppercase;font-family:var(--font-mono);letter-spacing:0.1em;" required>
          <div class="form-hint">Mengubah alias akan mempengaruhi SKU produk baru.</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-close-modal="editKatModal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function editKat(k) {
  document.getElementById('editKatId').value = k.id;
  document.getElementById('editKatNama').value = k.nama;
  document.getElementById('editKatAlias').value = k.alias;
  openModal('editKatModal');
}
</script>

<?php include '../includes/footer.php'; ?>
