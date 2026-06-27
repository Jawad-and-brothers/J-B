/* J&B Main JavaScript */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Add to Cart (AJAX) ----
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const qty = document.getElementById('qty_' + productId)?.value || 1;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';

            fetch('php/cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&product_id=${productId}&quantity=${qty}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.innerHTML = '<i class="fas fa-check me-1"></i> Added!';
                    this.classList.remove('btn-warning');
                    this.classList.add('btn-success');
                    // Update cart badge
                    document.querySelectorAll('.cart-badge').forEach(el => {
                        el.textContent = data.cart_count;
                        el.style.display = 'flex';
                    });
                    showToast('Product added to cart!', 'success');
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-warning');
                    }, 2000);
                } else {
                    showToast(data.message || 'Error adding to cart', 'danger');
                    if (data.redirect) window.location.href = data.redirect;
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(() => {
                showToast('Network error. Please try again.', 'danger');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // ---- Cart Quantity Update ----
    document.querySelectorAll('.qty-update').forEach(input => {
        input.addEventListener('change', function () {
            updateCartItem(this.dataset.id, this.value);
        });
    });

    document.querySelectorAll('.qty-up').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            input.value = parseInt(input.value) + 1;
            updateCartItem(this.dataset.id, input.value);
        });
    });

    document.querySelectorAll('.qty-down').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateCartItem(this.dataset.id, input.value);
            }
        });
    });

    // ---- Remove from Cart ----
    document.querySelectorAll('.btn-remove-cart').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!confirm('Remove this item from cart?')) return;
            fetch('php/cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove&product_id=${this.dataset.id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
                else showToast(data.message, 'danger');
            });
        });
    });

    // ---- Password Toggle ----
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    });

    // ---- Login Form Validation ----
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            let valid = true;
            const email = document.getElementById('email');
            const pass = document.getElementById('password');
            clearErrors([email, pass]);

            if (!email.value.trim() || !isValidEmail(email.value)) {
                showError(email, 'Please enter a valid email address.');
                valid = false;
            }
            if (!pass.value.trim() || pass.value.length < 6) {
                showError(pass, 'Password must be at least 6 characters.');
                valid = false;
            }
            if (!valid) e.preventDefault();
        });
    }

    // ---- Register Form Validation ----
    const regForm = document.getElementById('registerForm');
    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            let valid = true;
            const name = document.getElementById('full_name');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const pass = document.getElementById('password');
            const cpass = document.getElementById('confirm_password');
            clearErrors([name, email, phone, pass, cpass]);

            if (!name.value.trim() || name.value.trim().length < 3) {
                showError(name, 'Full name must be at least 3 characters.'); valid = false;
            }
            if (!email.value.trim() || !isValidEmail(email.value)) {
                showError(email, 'Please enter a valid email address.'); valid = false;
            }
            if (!phone.value.trim() || phone.value.length < 10) {
                showError(phone, 'Please enter a valid phone number.'); valid = false;
            }
            if (!pass.value || pass.value.length < 8) {
                showError(pass, 'Password must be at least 8 characters.'); valid = false;
            }
            if (pass.value !== cpass.value) {
                showError(cpass, 'Passwords do not match.'); valid = false;
            }
            if (!valid) e.preventDefault();
        });
    }

    // ---- Checkout Form Validation ----
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            let valid = true;
            const fields = ['ch_name', 'ch_email', 'ch_phone', 'ch_address', 'ch_city'];
            fields.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                clearErrors([el]);
                if (!el.value.trim()) {
                    showError(el, 'This field is required.'); valid = false;
                }
            });
            const emailEl = document.getElementById('ch_email');
            if (emailEl && emailEl.value && !isValidEmail(emailEl.value)) {
                showError(emailEl, 'Please enter a valid email.'); valid = false;
            }
            if (!valid) { e.preventDefault(); showToast('Please fill all required fields.', 'danger'); }
        });
    }

    // ---- Price Filter ----
    const priceRange = document.getElementById('priceRange');
    const priceVal = document.getElementById('priceVal');
    if (priceRange && priceVal) {
        priceRange.addEventListener('input', function () {
            priceVal.textContent = 'PKR ' + parseInt(this.value).toLocaleString();
        });
    }

    // ---- Product Qty Selector ----
    const mainQtyUp = document.getElementById('mainQtyUp');
    const mainQtyDown = document.getElementById('mainQtyDown');
    const mainQtyInput = document.getElementById('mainQty');
    if (mainQtyUp && mainQtyDown && mainQtyInput) {
        mainQtyUp.addEventListener('click', () => {
            mainQtyInput.value = parseInt(mainQtyInput.value) + 1;
        });
        mainQtyDown.addEventListener('click', () => {
            if (parseInt(mainQtyInput.value) > 1)
                mainQtyInput.value = parseInt(mainQtyInput.value) - 1;
        });
    }

    // ---- Animate cards on scroll ----
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.product-card, .cat-card, .testimonial-card').forEach(el => {
            observer.observe(el);
        });
    }
});

// ---- Helper: Update Cart Item ----
function updateCartItem(productId, quantity) {
    fetch('php/cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update subtotal and total
            const subtotalEl = document.getElementById('subtotal_' + productId);
            if (subtotalEl) subtotalEl.textContent = data.item_subtotal;
            document.querySelectorAll('.cart-total-display').forEach(el => { el.textContent = data.cart_total; });
            document.querySelectorAll('.cart-badge').forEach(el => { el.textContent = data.cart_count; });
        } else {
            showToast(data.message, 'danger');
        }
    });
}

// ---- Helper: Show toast ----
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer') || createToastContainer();
    const id = 'toast_' + Date.now();
    const colors = { success: '#4a7c59', danger: '#dc3545', warning: '#856404', info: '#0d6efd' };
    const toast = document.createElement('div');
    toast.id = id;
    toast.className = 'toast align-items-center text-white border-0 show';
    toast.style.cssText = `background:${colors[type] || colors.info};min-width:240px;`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.toast').remove()"></button>
        </div>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function createToastContainer() {
    const div = document.createElement('div');
    div.id = 'toastContainer';
    div.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
    document.body.appendChild(div);
    return div;
}

// ---- Helper: Form validation ----
function showError(input, message) {
    input.classList.add('is-invalid');
    let fb = input.nextElementSibling;
    if (!fb || !fb.classList.contains('invalid-feedback')) {
        fb = document.createElement('div');
        fb.className = 'invalid-feedback';
        input.parentElement.appendChild(fb);
    }
    fb.textContent = message;
}

function clearErrors(inputs) {
    inputs.forEach(input => {
        if (!input) return;
        input.classList.remove('is-invalid');
        const fb = input.nextElementSibling;
        if (fb && fb.classList.contains('invalid-feedback')) fb.remove();
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}