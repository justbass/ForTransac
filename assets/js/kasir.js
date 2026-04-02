// ForTransac POS - Kasir/POS Cart Logic
// Cart is stored in JS memory (array), updated on every change
// PHP is used for checkout submission

document.addEventListener('DOMContentLoaded', function () {

  var cart = []; // [{id, sku, name, price, discount, stock, qty}]

  var cartItemsEl = document.getElementById('cartItems');
  var cartCountEl = document.getElementById('cartCount');
  var cartEmptyEl = document.getElementById('cartEmpty');
  var subtotalEl = document.getElementById('cartSubtotal');
  var discountEl = document.getElementById('cartDiscount');
  var totalEl = document.getElementById('cartTotal');
  var checkoutBtn = document.getElementById('btnCheckout');
  var clearCartBtn = document.getElementById('btnClearCart');
  var skuInput = document.getElementById('skuInput');
  var addSkuBtn = document.getElementById('btnAddSku');

  // ---- Product search filter ----
  var prodSearchInput = document.getElementById('prodSearch');
  if (prodSearchInput) {
    prodSearchInput.addEventListener('input', function () {
      var q = this.value.toLowerCase();
      document.querySelectorAll('.product-card').forEach(function (card) {
        var name = (card.getAttribute('data-name') || '').toLowerCase();
        var sku = (card.getAttribute('data-sku') || '').toLowerCase();
        card.style.display = (name.includes(q) || sku.includes(q)) ? '' : 'none';
      });
    });
  }

  // ---- Add by SKU input ----
  function addBySku(sku) {
    sku = sku.trim().toUpperCase();
    if (!sku) return;
    var card = document.querySelector('.product-card[data-sku="' + sku + '"]');
    if (!card) {
      showSkuError('Produk dengan SKU "' + sku + '" tidak ditemukan.');
      return;
    }
    var productData = {
      id: parseInt(card.getAttribute('data-id')),
      sku: card.getAttribute('data-sku'),
      name: card.getAttribute('data-name'),
      price: parseFloat(card.getAttribute('data-price')),
      discount: parseFloat(card.getAttribute('data-discount') || 0),
      stock: parseInt(card.getAttribute('data-stock'))
    };
    addToCart(productData);
    if (skuInput) { skuInput.value = ''; skuInput.focus(); }
  }

  function showSkuError(msg) {
    var el = document.getElementById('skuError');
    if (el) {
      el.textContent = msg;
      el.style.display = '';
      setTimeout(function () { el.style.display = 'none'; }, 2500);
    }
  }

  if (skuInput) {
    skuInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') addBySku(skuInput.value);
    });
  }

  if (addSkuBtn) {
    addSkuBtn.addEventListener('click', function () {
      addBySku(skuInput ? skuInput.value : '');
    });
  }

  // ---- Click product card ----
  document.querySelectorAll('.product-card').forEach(function (card) {
    card.addEventListener('click', function () {
      var productData = {
        id: parseInt(card.getAttribute('data-id')),
        sku: card.getAttribute('data-sku'),
        name: card.getAttribute('data-name'),
        price: parseFloat(card.getAttribute('data-price')),
        discount: parseFloat(card.getAttribute('data-discount') || 0),
        stock: parseInt(card.getAttribute('data-stock'))
      };
      addToCart(productData);
    });
  });

  // ---- Cart logic ----
  function addToCart(product) {
    var existing = null;
    for (var i = 0; i < cart.length; i++) {
      if (cart[i].id === product.id) { existing = cart[i]; break; }
    }
    if (existing) {
      if (existing.qty >= existing.stock) {
        showSkuError('Stok ' + product.name + ' tidak cukup!');
        return;
      }
      existing.qty++;
    } else {
      if (product.stock < 1) {
        showSkuError('Stok ' + product.name + ' habis!');
        return;
      }
      cart.push({ id: product.id, sku: product.sku, name: product.name, price: product.price, discount: product.discount, stock: product.stock, qty: 1 });
    }
    renderCart();
  }

  function removeFromCart(idx) {
    cart.splice(idx, 1);
    renderCart();
  }

  function updateQty(idx, delta) {
    cart[idx].qty += delta;
    if (cart[idx].qty <= 0) {
      cart.splice(idx, 1);
    } else if (cart[idx].qty > cart[idx].stock) {
      cart[idx].qty = cart[idx].stock;
      showSkuError('Stok tidak cukup!');
    }
    renderCart();
  }

  function getItemSubtotal(item) {
    var discounted = item.price * (1 - item.discount / 100);
    return discounted * item.qty;
  }

  function getCartTotals() {
    var subtotal = 0, discount = 0, total = 0;
    cart.forEach(function (item) {
      var raw = item.price * item.qty;
      var sub = getItemSubtotal(item);
      subtotal += raw;
      discount += (raw - sub);
      total += sub;
    });
    return { subtotal: subtotal, discount: discount, total: total };
  }

  function renderCart() {
    if (!cartItemsEl) return;

    if (cart.length === 0) {
      if (cartEmptyEl) cartEmptyEl.style.display = '';
      cartItemsEl.innerHTML = '';
      if (cartCountEl) cartCountEl.textContent = '0';
      if (subtotalEl) subtotalEl.textContent = formatRp(0);
      if (discountEl) discountEl.textContent = '-' + formatRp(0);
      if (totalEl) totalEl.textContent = formatRp(0);
      if (checkoutBtn) checkoutBtn.disabled = true;
      if (clearCartBtn) clearCartBtn.style.display = 'none';
      return;
    }

    if (cartEmptyEl) cartEmptyEl.style.display = 'none';
    if (checkoutBtn) checkoutBtn.disabled = false;
    if (clearCartBtn) clearCartBtn.style.display = '';

    var totalQty = 0;
    cart.forEach(function (i) { totalQty += i.qty; });
    if (cartCountEl) cartCountEl.textContent = totalQty;

    var html = '';
    cart.forEach(function (item, idx) {
      var sub = getItemSubtotal(item);
      var discLine = item.discount > 0 ? '<div class="ci-disc">Diskon ' + item.discount + '%</div>' : '';
      html += '<div class="cart-item">' +
        '<div class="ci-info">' +
          '<div class="ci-name" title="' + esc(item.name) + '">' + esc(item.name) + '</div>' +
          '<div class="ci-price">' + formatRp(item.price) + '</div>' +
          discLine +
        '</div>' +
        '<div class="ci-qty">' +
          '<button type="button" onclick="cartUpdateQty(' + idx + ',-1)">−</button>' +
          '<span>' + item.qty + '</span>' +
          '<button type="button" onclick="cartUpdateQty(' + idx + ',1)">+</button>' +
        '</div>' +
        '<div class="ci-subtotal">' + formatRp(sub) + '</div>' +
        '<button class="ci-remove" type="button" onclick="cartRemove(' + idx + ')" title="Hapus">✕</button>' +
      '</div>';
    });

    cartItemsEl.innerHTML = html;

    var totals = getCartTotals();
    if (subtotalEl) subtotalEl.textContent = formatRp(totals.subtotal);
    if (discountEl) discountEl.textContent = (totals.discount > 0 ? '-' : '') + formatRp(totals.discount);
    if (totalEl) totalEl.textContent = formatRp(totals.total);
  }

  // Exposed to inline handlers
  window.cartRemove = function (idx) { removeFromCart(idx); };
  window.cartUpdateQty = function (idx, delta) { updateQty(idx, delta); };

  // ---- Clear cart ----
  if (clearCartBtn) {
    clearCartBtn.addEventListener('click', function () {
      if (confirm('Hapus semua item dari keranjang?')) {
        cart = [];
        renderCart();
      }
    });
  }

  // ---- Checkout ----
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', function () {
      if (cart.length === 0) return;
      var totals = getCartTotals();
      // Populate payment modal
      var pmSubtotal = document.getElementById('pmSubtotal');
      var pmDiscount = document.getElementById('pmDiscount');
      var pmTotal = document.getElementById('pmTotal');
      var pmPaid = document.getElementById('pmAmountPaid');
      var pmChange = document.getElementById('pmChange');
      if (pmSubtotal) pmSubtotal.textContent = formatRp(totals.subtotal);
      if (pmDiscount) pmDiscount.textContent = (totals.discount > 0 ? '-' : '') + formatRp(totals.discount);
      if (pmTotal) pmTotal.textContent = formatRp(totals.total);
      if (pmPaid) { pmPaid.value = ''; }
      if (pmChange) pmChange.textContent = formatRp(0);

      // Populate hidden cart data
      var cartInput = document.getElementById('cartDataInput');
      if (cartInput) cartInput.value = JSON.stringify(cart);

      openModal('paymentModal');
      if (pmPaid) setTimeout(function () { pmPaid.focus(); }, 100);
    });
  }

  // ---- Payment amount change ----
  var pmPaidInput = document.getElementById('pmAmountPaid');
  if (pmPaidInput) {
    pmPaidInput.addEventListener('input', function () {
      var totals = getCartTotals();
      var paid = parseFloat(this.value.replace(/[^0-9]/g, '')) || 0;
      var change = paid - totals.total;
      var pmChange = document.getElementById('pmChange');
      if (pmChange) {
        pmChange.textContent = change >= 0 ? formatRp(change) : '— Kurang ' + formatRp(-change);
        pmChange.style.color = change >= 0 ? '' : '#c0392b';
      }
      var confirmBtn = document.getElementById('btnConfirmPayment');
      if (confirmBtn) confirmBtn.disabled = (paid < totals.total);
    });
  }

  // ---- Confirm payment ----
  var confirmPayBtn = document.getElementById('btnConfirmPayment');
  if (confirmPayBtn) {
    confirmPayBtn.addEventListener('click', function () {
      var totals = getCartTotals();
      var paid = parseFloat(document.getElementById('pmAmountPaid').value.replace(/[^0-9]/g, '')) || 0;
      if (paid < totals.total) {
        alert('Nominal bayar kurang!');
        return;
      }

      // Build form and submit
      var form = document.getElementById('checkoutForm');
      if (!form) return;

      document.getElementById('hiddenCartData').value = JSON.stringify(cart);
      document.getElementById('hiddenTotal').value = totals.total;
      document.getElementById('hiddenPaid').value = paid;
      document.getElementById('hiddenChange').value = paid - totals.total;

      form.submit();
    });
  }

  // Helpers
  function formatRp(n) {
    return 'Rp ' + parseInt(n || 0).toLocaleString('id-ID');
  }

  function esc(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
  }

  // Init
  renderCart();
});
