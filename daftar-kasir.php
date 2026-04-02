/* ForTransac POS — Black & White Modern Minimal */
:root {
  --black: #0a0a0a;
  --white: #fafafa;
  --gray-50: #f5f5f5;
  --gray-100: #e8e8e8;
  --gray-200: #d0d0d0;
  --gray-300: #b0b0b0;
  --gray-400: #888888;
  --gray-500: #666666;
  --gray-600: #444444;
  --gray-700: #2a2a2a;
  --gray-800: #1a1a1a;
  --accent: #0a0a0a;
  --danger: #c0392b;
  --success: #1a7a3a;
  --warning: #c47a00;
  --info: #1a4a7a;
  --sidebar-w: 240px;
  --topbar-h: 60px;
  --radius: 6px;
  --radius-lg: 10px;
  --shadow: 0 1px 4px rgba(0,0,0,0.08);
  --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
  --font-mono: 'Space Mono', monospace;
  --font-sans: 'DM Sans', sans-serif;
  --transition: 0.18s ease;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { font-size: 15px; }

body {
  font-family: var(--font-sans);
  background: var(--gray-50);
  color: var(--black);
  line-height: 1.6;
  overflow-x: hidden;
}

a { text-decoration: none; color: inherit; }
img { max-width: 100%; }

/* ---- SCROLLBAR ---- */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 10px; }

/* ==============================
   APP LAYOUT
============================== */
.app-wrapper {
  display: flex;
  min-height: 100vh;
}

/* ---- SIDEBAR ---- */
.sidebar {
  width: var(--sidebar-w);
  background: var(--black);
  color: var(--white);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0; left: 0;
  height: 100vh;
  z-index: 100;
  transition: transform var(--transition);
  overflow-y: auto;
}

.sidebar-header {
  padding: 20px 16px 16px;
  border-bottom: 1px solid var(--gray-800);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand { display: flex; align-items: center; gap: 10px; }

.brand-icon {
  font-size: 22px;
  color: var(--white);
  line-height: 1;
}

.brand-name {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: -0.02em;
  line-height: 1.1;
}

.brand-loc {
  font-size: 0.7rem;
  color: var(--gray-400);
  font-family: var(--font-mono);
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.sidebar-close {
  display: none;
  background: none;
  border: none;
  color: var(--gray-300);
  cursor: pointer;
  font-size: 1rem;
  padding: 4px;
}

.sidebar-nav {
  flex: 1;
  padding: 12px 8px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: var(--radius);
  color: var(--gray-300);
  font-size: 0.88rem;
  font-weight: 500;
  transition: background var(--transition), color var(--transition);
}

.nav-item:hover { background: var(--gray-800); color: var(--white); }
.nav-item.active { background: var(--white); color: var(--black); }

.nav-icon {
  font-size: 1rem;
  width: 20px;
  text-align: center;
  flex-shrink: 0;
}

.sidebar-footer {
  padding: 12px 8px;
  border-top: 1px solid var(--gray-800);
  display: flex;
  align-items: center;
  gap: 8px;
}

.profile-link {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
  padding: 8px;
  border-radius: var(--radius);
  transition: background var(--transition);
  min-width: 0;
}

.profile-link:hover { background: var(--gray-800); }
.profile-link.active { background: var(--gray-800); }

.avatar {
  width: 34px; height: 34px;
  background: var(--white);
  color: var(--black);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 0.85rem;
  flex-shrink: 0;
}

.profile-info { min-width: 0; }
.profile-name {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--white);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.profile-email {
  font-size: 0.7rem;
  color: var(--gray-400);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.logout-btn {
  background: none;
  border: 1px solid var(--gray-700);
  color: var(--gray-400);
  padding: 6px 8px;
  border-radius: var(--radius);
  cursor: pointer;
  font-size: 1rem;
  transition: all var(--transition);
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logout-btn:hover { border-color: var(--danger); color: var(--danger); background: rgba(192,57,43,0.08); }

.sidebar-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 99;
}

/* ---- MAIN CONTENT ---- */
.main-content {
  margin-left: var(--sidebar-w);
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  min-width: 0;
}

.topbar {
  height: var(--topbar-h);
  background: var(--white);
  border-bottom: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  padding: 0 24px;
  gap: 16px;
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: var(--shadow);
}

.menu-toggle {
  display: none;
  background: none;
  border: none;
  font-size: 1.3rem;
  cursor: pointer;
  color: var(--black);
  padding: 4px 6px;
}

.page-title {
  font-size: 1rem;
  font-weight: 700;
  font-family: var(--font-mono);
  letter-spacing: -0.02em;
  flex: 1;
}

.topbar-right {
  font-size: 0.82rem;
  color: var(--gray-500);
  font-family: var(--font-mono);
}

.content-area {
  padding: 24px;
  flex: 1;
}

/* ---- FLASH ---- */
.flash {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 20px;
  margin: 0 24px;
  border-radius: var(--radius);
  font-size: 0.88rem;
  font-weight: 500;
  border-left: 4px solid;
  gap: 12px;
}

.flash button {
  background: none; border: none; cursor: pointer;
  color: inherit; opacity: 0.7; font-size: 0.9rem;
  padding: 0 2px; flex-shrink: 0;
}

.flash-success { background: #f0faf4; color: var(--success); border-color: var(--success); }
.flash-error   { background: #fdf2f0; color: var(--danger);  border-color: var(--danger);  }
.flash-warning { background: #fffbf0; color: var(--warning); border-color: var(--warning); }
.flash-info    { background: #f0f5ff; color: var(--info);    border-color: var(--info);    }

/* ==============================
   COMPONENTS
============================== */

/* Cards */
.card {
  background: var(--white);
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-100);
  box-shadow: var(--shadow);
  overflow: hidden;
}

.card-header {
  padding: 16px 20px;
  border-bottom: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.card-title {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 0.9rem;
  letter-spacing: -0.01em;
}

.card-body { padding: 20px; }

/* Stats Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: var(--white);
  border: 1px solid var(--gray-100);
  border-radius: var(--radius-lg);
  padding: 18px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.stat-icon {
  width: 42px; height: 42px;
  background: var(--gray-50);
  border: 1px solid var(--gray-100);
  border-radius: var(--radius);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.stat-info {}
.stat-value {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 1.2rem;
  line-height: 1.2;
}
.stat-label { font-size: 0.75rem; color: var(--gray-500); margin-top: 2px; }

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: var(--radius);
  font-size: 0.85rem;
  font-weight: 600;
  font-family: var(--font-sans);
  border: 1.5px solid transparent;
  cursor: pointer;
  transition: all var(--transition);
  white-space: nowrap;
  line-height: 1.4;
}

.btn-sm { padding: 5px 10px; font-size: 0.78rem; }
.btn-lg { padding: 11px 22px; font-size: 0.95rem; }
.btn-block { width: 100%; justify-content: center; }

.btn-primary {
  background: var(--black);
  color: var(--white);
  border-color: var(--black);
}
.btn-primary:hover { background: var(--gray-700); border-color: var(--gray-700); }

.btn-outline {
  background: transparent;
  color: var(--black);
  border-color: var(--gray-200);
}
.btn-outline:hover { background: var(--gray-50); border-color: var(--gray-300); }

.btn-danger {
  background: var(--danger);
  color: var(--white);
  border-color: var(--danger);
}
.btn-danger:hover { background: #a93226; border-color: #a93226; }

.btn-success {
  background: var(--success);
  color: var(--white);
  border-color: var(--success);
}
.btn-success:hover { background: #155e2e; }

.btn-ghost {
  background: transparent;
  color: var(--gray-600);
  border-color: transparent;
  padding: 5px 8px;
}
.btn-ghost:hover { background: var(--gray-100); color: var(--black); }

.btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* Forms */
.form-group {
  margin-bottom: 16px;
}

.form-label {
  display: block;
  font-size: 0.82rem;
  font-weight: 600;
  margin-bottom: 6px;
  color: var(--gray-700);
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
}

.form-label .req { color: var(--danger); margin-left: 2px; }

.form-control {
  width: 100%;
  padding: 9px 12px;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius);
  font-size: 0.88rem;
  font-family: var(--font-sans);
  background: var(--white);
  color: var(--black);
  transition: border-color var(--transition), box-shadow var(--transition);
  outline: none;
  -webkit-appearance: none;
  appearance: none;
}

.form-control:focus {
  border-color: var(--black);
  box-shadow: 0 0 0 3px rgba(10,10,10,0.08);
}

.form-control:read-only { background: var(--gray-50); color: var(--gray-500); }

select.form-control {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23666' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  padding-right: 32px;
}

.form-hint { font-size: 0.75rem; color: var(--gray-400); margin-top: 4px; }
.form-error { font-size: 0.75rem; color: var(--danger); margin-top: 4px; }

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

/* Tables */
.table-wrap {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
}

thead th {
  background: var(--gray-50);
  border-bottom: 2px solid var(--gray-100);
  padding: 10px 14px;
  text-align: left;
  font-family: var(--font-mono);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--gray-600);
  white-space: nowrap;
}

tbody tr {
  border-bottom: 1px solid var(--gray-100);
  transition: background var(--transition);
}

tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: var(--gray-50); }

tbody td {
  padding: 11px 14px;
  vertical-align: middle;
}

/* Badges */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 0.72rem;
  font-weight: 700;
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
  white-space: nowrap;
}

.badge-dark { background: var(--black); color: var(--white); }
.badge-light { background: var(--gray-100); color: var(--gray-700); }
.badge-success { background: #e8f8ee; color: var(--success); }
.badge-danger { background: #fdf0ee; color: var(--danger); }
.badge-warning { background: #fff8e8; color: var(--warning); }

/* Pagination */
.pagination {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 16px 20px;
  border-top: 1px solid var(--gray-100);
  justify-content: flex-end;
  flex-wrap: wrap;
}

.page-link {
  padding: 5px 10px;
  border: 1.5px solid var(--gray-200);
  border-radius: var(--radius);
  font-size: 0.8rem;
  font-family: var(--font-mono);
  color: var(--gray-600);
  transition: all var(--transition);
  cursor: pointer;
}

.page-link:hover { background: var(--gray-50); border-color: var(--gray-300); }
.page-link.active { background: var(--black); color: var(--white); border-color: var(--black); }
.page-link.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

/* Modals */
.modal-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 200;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.modal-backdrop.open { display: flex; }

.modal {
  background: var(--white);
  border-radius: var(--radius-lg);
  width: 100%;
  max-width: 520px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-md);
}

.modal-header {
  padding: 18px 20px 14px;
  border-bottom: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 0.95rem;
  letter-spacing: -0.01em;
}

.modal-close {
  background: none; border: none; cursor: pointer;
  font-size: 1.1rem; color: var(--gray-400);
  padding: 2px 6px; border-radius: var(--radius);
  transition: all var(--transition);
}
.modal-close:hover { background: var(--gray-100); color: var(--black); }

.modal-body { padding: 20px; }
.modal-footer {
  padding: 14px 20px;
  border-top: 1px solid var(--gray-100);
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  flex-wrap: wrap;
}

/* Search Bar */
.search-bar {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items: center;
}

.search-input-wrap {
  position: relative;
  flex: 1;
  min-width: 200px;
}

.search-input-wrap .search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  font-size: 0.85rem;
  pointer-events: none;
}

.search-input-wrap .form-control {
  padding-left: 32px;
}

/* SKU tag */
.sku-tag {
  font-family: var(--font-mono);
  font-size: 0.75rem;
  background: var(--gray-100);
  color: var(--gray-600);
  padding: 2px 7px;
  border-radius: 4px;
  letter-spacing: 0.03em;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 48px 20px;
  color: var(--gray-400);
}

.empty-state-icon { font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4; }
.empty-state-text { font-size: 0.9rem; }

/* ==============================
   AUTH PAGES
============================== */
.auth-wrapper {
  min-height: 100vh;
  background: var(--gray-50);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}

.auth-card {
  background: var(--white);
  border: 1px solid var(--gray-100);
  border-radius: var(--radius-lg);
  width: 100%;
  max-width: 400px;
  padding: 36px 32px;
  box-shadow: var(--shadow-md);
}

.auth-brand {
  text-align: center;
  margin-bottom: 28px;
}

.auth-brand-name {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 1.6rem;
  letter-spacing: -0.04em;
  margin-bottom: 4px;
}

.auth-brand-sub {
  font-size: 0.8rem;
  color: var(--gray-400);
  font-family: var(--font-mono);
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.auth-title {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 1.05rem;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--gray-100);
}

.auth-footer {
  text-align: center;
  margin-top: 20px;
  font-size: 0.83rem;
  color: var(--gray-500);
}

.auth-footer a {
  color: var(--black);
  font-weight: 600;
  text-decoration: underline;
  text-underline-offset: 2px;
}

/* ==============================
   POS / KASIR PAGE
============================== */
.pos-layout {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 20px;
  min-height: calc(100vh - var(--topbar-h) - 48px);
}

.pos-left { display: flex; flex-direction: column; gap: 16px; }
.pos-right { display: flex; flex-direction: column; gap: 16px; }

.sku-search-bar {
  display: flex;
  gap: 10px;
  align-items: stretch;
}

.sku-input-wrap { flex: 1; position: relative; }

.sku-input-wrap input {
  font-family: var(--font-mono);
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding-right: 36px;
}

.sku-scan-btn {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: var(--gray-400);
  font-size: 1rem;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 10px;
  max-height: 380px;
  overflow-y: auto;
  padding-right: 4px;
}

.product-card {
  background: var(--white);
  border: 1.5px solid var(--gray-100);
  border-radius: var(--radius);
  padding: 12px;
  cursor: pointer;
  transition: all var(--transition);
  user-select: none;
  position: relative;
}

.product-card:hover {
  border-color: var(--black);
  transform: translateY(-1px);
  box-shadow: var(--shadow);
}

.product-card.out-of-stock {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.pc-sku { font-family: var(--font-mono); font-size: 0.65rem; color: var(--gray-400); margin-bottom: 4px; }
.pc-name { font-weight: 600; font-size: 0.83rem; line-height: 1.3; margin-bottom: 6px; }
.pc-price { font-family: var(--font-mono); font-size: 0.82rem; font-weight: 700; }
.pc-stock { font-size: 0.7rem; color: var(--gray-400); margin-top: 2px; }

.pc-discount-badge {
  position: absolute;
  top: 8px; right: 8px;
  background: var(--black);
  color: var(--white);
  font-size: 0.65rem;
  font-family: var(--font-mono);
  padding: 1px 5px;
  border-radius: 3px;
}

/* Cart */
.cart-container { display: flex; flex-direction: column; flex: 1; }

.cart-header {
  padding: 14px 16px;
  border-bottom: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cart-title {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 0.88rem;
}

.cart-count {
  background: var(--black);
  color: var(--white);
  font-family: var(--font-mono);
  font-size: 0.7rem;
  padding: 2px 7px;
  border-radius: 20px;
}

.cart-items {
  flex: 1;
  overflow-y: auto;
  max-height: 320px;
  min-height: 100px;
}

.cart-empty {
  padding: 32px 20px;
  text-align: center;
  color: var(--gray-300);
  font-size: 0.85rem;
  font-family: var(--font-mono);
}

.cart-item {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  gap: 10px;
  border-bottom: 1px solid var(--gray-100);
  transition: background var(--transition);
}

.cart-item:hover { background: var(--gray-50); }

.ci-info { flex: 1; min-width: 0; }
.ci-name { font-weight: 600; font-size: 0.83rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ci-price { font-family: var(--font-mono); font-size: 0.75rem; color: var(--gray-500); }
.ci-disc { font-size: 0.7rem; color: var(--success); }

.ci-qty {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}

.ci-qty button {
  width: 24px; height: 24px;
  border: 1.5px solid var(--gray-200);
  border-radius: 4px;
  background: var(--white);
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  transition: all var(--transition);
}
.ci-qty button:hover { background: var(--black); color: var(--white); border-color: var(--black); }

.ci-qty span {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 0.85rem;
  min-width: 24px;
  text-align: center;
}

.ci-subtotal {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 0.82rem;
  flex-shrink: 0;
  min-width: 70px;
  text-align: right;
}

.ci-remove {
  background: none; border: none; cursor: pointer;
  color: var(--gray-300); font-size: 0.9rem;
  padding: 2px 4px; border-radius: 3px;
  transition: all var(--transition);
}
.ci-remove:hover { color: var(--danger); background: rgba(192,57,43,0.08); }

.cart-summary {
  padding: 14px 16px;
  border-top: 1px solid var(--gray-100);
  background: var(--gray-50);
}

.cart-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.83rem;
  padding: 3px 0;
}

.cart-row.total {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 1rem;
  padding-top: 10px;
  margin-top: 6px;
  border-top: 2px solid var(--black);
}

.cart-actions { padding: 14px 16px; }

/* Payment modal */
.payment-info {
  background: var(--gray-50);
  border-radius: var(--radius);
  padding: 14px 16px;
  margin-bottom: 16px;
}

.pay-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.88rem;
  padding: 3px 0;
}

.pay-row.total {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 1.1rem;
  padding-top: 10px;
  margin-top: 6px;
  border-top: 2px solid var(--black);
}

.change-display {
  background: var(--black);
  color: var(--white);
  border-radius: var(--radius);
  padding: 12px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  font-family: var(--font-mono);
}

/* Profile */
.profile-card {
  max-width: 560px;
}

.profile-avatar-lg {
  width: 64px; height: 64px;
  background: var(--black);
  color: var(--white);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 1.5rem;
  margin-bottom: 12px;
}

.profile-detail-row {
  display: flex;
  gap: 8px;
  padding: 12px 0;
  border-bottom: 1px solid var(--gray-100);
  font-size: 0.88rem;
}

.profile-detail-row:last-child { border-bottom: none; }

.pdr-label {
  font-family: var(--font-mono);
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--gray-400);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  min-width: 110px;
  padding-top: 2px;
}

/* Receipt */
.receipt-wrap {
  font-family: var(--font-mono);
  font-size: 0.82rem;
  max-width: 320px;
  margin: 0 auto;
}

.receipt-header { text-align: center; margin-bottom: 14px; }
.receipt-store { font-weight: 700; font-size: 1rem; }
.receipt-sub { font-size: 0.72rem; color: var(--gray-500); }
.receipt-divider { border: none; border-top: 1px dashed var(--gray-300); margin: 10px 0; }
.receipt-row { display: flex; justify-content: space-between; padding: 2px 0; font-size: 0.8rem; }
.receipt-item-name { font-size: 0.78rem; }
.receipt-total { font-weight: 700; font-size: 0.9rem; }
.receipt-footer { text-align: center; font-size: 0.72rem; color: var(--gray-500); margin-top: 14px; }

/* Konfirmasi delete */
.confirm-text {
  font-size: 0.88rem;
  color: var(--gray-600);
  line-height: 1.6;
}

.confirm-text strong { color: var(--black); }

/* ==============================
   RESPONSIVE
============================== */
@media (max-width: 900px) {
  .pos-layout {
    grid-template-columns: 1fr;
  }

  .pos-right {
    order: -1;
  }
}

@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .sidebar-close { display: flex; }

  .sidebar-overlay.open { display: block; }

  .main-content { margin-left: 0; }

  .menu-toggle { display: flex; }

  .content-area { padding: 16px; }

  .form-row { grid-template-columns: 1fr; }

  .stats-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 480px) {
  .auth-card { padding: 24px 18px; }
  .stats-grid { grid-template-columns: 1fr; }
  .card-header { flex-direction: column; align-items: flex-start; }
}

/* Print styles */
@media print {
  body * { visibility: hidden; }
  .print-area, .print-area * { visibility: visible; }
  .print-area {
    position: fixed;
    left: 0; top: 0;
    width: 80mm;
    font-family: 'Courier New', monospace;
    font-size: 11px;
  }
}

/* Utility */
.flex { display: flex; }
.flex-center { display: flex; align-items: center; }
.gap-8 { gap: 8px; }
.gap-12 { gap: 12px; }
.mt-8 { margin-top: 8px; }
.mt-16 { margin-top: 16px; }
.mb-16 { margin-bottom: 16px; }
.text-mono { font-family: var(--font-mono); }
.text-sm { font-size: 0.8rem; }
.text-muted { color: var(--gray-400); }
.text-right { text-align: right; }
.text-center { text-align: center; }
.font-bold { font-weight: 700; }
.w-full { width: 100%; }
.hidden { display: none !important; }
