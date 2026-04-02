// ForTransac POS - Main JS

document.addEventListener('DOMContentLoaded', function () {

  // ---- Sidebar toggle ----
  var menuToggle = document.getElementById('menuToggle');
  var sidebar = document.getElementById('sidebar');
  var sidebarClose = document.getElementById('sidebarClose');
  var overlay = document.getElementById('sidebarOverlay');

  if (menuToggle) {
    menuToggle.addEventListener('click', function () {
      sidebar.classList.add('open');
      overlay.classList.add('open');
    });
  }

  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
  }

  if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
  if (overlay) overlay.addEventListener('click', closeSidebar);

  // ---- Flash auto-dismiss ----
  var flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(function () {
      flash.style.opacity = '0';
      flash.style.transition = 'opacity 0.4s';
      setTimeout(function () { if (flash) flash.remove(); }, 400);
    }, 4000);
  }

  // ---- Modal helpers ----
  function openModal(id) {
    var el = document.getElementById(id);
    if (el) el.classList.add('open');
  }

  function closeModal(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('open');
  }

  // Close modal on backdrop click
  document.querySelectorAll('.modal-backdrop').forEach(function (bd) {
    bd.addEventListener('click', function (e) {
      if (e.target === bd) bd.classList.remove('open');
    });
  });

  // Close buttons
  document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = btn.getAttribute('data-close-modal');
      closeModal(target);
    });
  });

  document.querySelectorAll('[data-open-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = btn.getAttribute('data-open-modal');
      openModal(target);
    });
  });

  // ---- Number format helpers ----
  window.formatRupiah = function (n) {
    return 'Rp ' + parseInt(n || 0).toLocaleString('id-ID');
  };

  window.openModal = openModal;
  window.closeModal = closeModal;

  // Confirm delete
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm') || 'Yakin?')) {
        e.preventDefault();
        return false;
      }
    });
  });

});
