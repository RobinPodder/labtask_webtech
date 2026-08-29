/* ==========================================================================
   Telemedicine++ — Shared JS
   NOTE: This is frontend-only mock behaviour. Real auth/data will be wired
   to PHP + MySQL endpoints later (see /api/*.php once backend is added).
   ========================================================================== */

// ---------- Mobile nav toggle ----------
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const links = document.querySelector('.nav-links');
  if (toggle && links) {
    toggle.addEventListener('click', () => links.classList.toggle('open'));
  }

  renderPulseDividers();
  wireFilterBars();
  wireLoginForm();
  wireSignupForm();
  wireDashboardToggles();
  wireSidebarActive();
  wireLogoutButton();
  wireCurrentUser();
});

// ---------- Signature pulse-line divider ----------
function renderPulseDividers() {
  const path = "M0,17 L60,17 L72,17 L80,4 L92,30 L104,17 L140,17 L152,17 L160,4 L172,30 L184,17 L400,17";
  const svg = `
    <svg viewBox="0 0 400 34" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="${path}"/>
      <path d="${path.replace(/^M0,17/, 'M400,17')}" transform="translate(400,0)"/>
    </svg>`;
  document.querySelectorAll('.pulse-divider').forEach(el => {
    if (!el.innerHTML.trim()) el.innerHTML = svg;
  });
}

// ---------- Filter bars (Find Doctors / Medicine / Sitters / Lab tests) ----------
function wireFilterBars() {
  document.querySelectorAll('[data-filter-list]').forEach(bar => {
    const listSelector = bar.getAttribute('data-filter-list');
    const list = document.querySelector(listSelector);
    if (!list) return;
    const search = bar.querySelector('input[type="search"], input[type="text"]');
    const select = bar.querySelector('select');

    function apply() {
      const term = (search?.value || '').toLowerCase().trim();
      const cat = select?.value || 'all';
      list.querySelectorAll('[data-name]').forEach(card => {
        const name = card.getAttribute('data-name').toLowerCase();
        const category = card.getAttribute('data-category') || 'all';
        const matchesTerm = !term || name.includes(term);
        const matchesCat = cat === 'all' || category === cat;
        card.style.display = (matchesTerm && matchesCat) ? '' : 'none';
      });
    }
    search?.addEventListener('input', apply);
    select?.addEventListener('change', apply);
  });
}

// ---------- Login (wired to api/login.php) ----------
function wireLoginForm() {
  const form = document.querySelector('#login-form');
  if (!form) return;
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorBox = form.querySelector('.error-msg');
    const submitBtn = form.querySelector('button[type="submit"]');
    const email = form.querySelector('#login-email').value.trim();
    const password = form.querySelector('#login-password').value.trim();
    const role = form.querySelector('input[name="role"]:checked')?.value || 'patient';

    const showError = (msg) => {
      if (errorBox) { errorBox.textContent = msg; errorBox.classList.add('show'); }
    };

    if (!email || !password) {
      showError('Email এবং password দুটোই দিতে হবে।');
      return;
    }

    if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'লগইন হচ্ছে...'; }
    errorBox?.classList.remove('show');

    try {
      const res = await fetch('api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password, role }),
      });
      const data = await res.json();

      if (data.success) {
        window.location.href = data.redirect;
      } else {
        showError(data.message || 'লগইন ব্যর্থ হয়েছে।');
      }
    } catch (err) {
      showError('সার্ভারের সাথে সংযোগ করা যায়নি। XAMPP-এ Apache ও MySQL চালু আছে কিনা দেখো, এবং পেজটা http://localhost/... দিয়ে খোলা হয়েছে কিনা নিশ্চিত করো।');
    } finally {
      if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Log In'; }
    }
  });
}

// ---------- Signup (wired to api/signup.php) ----------
function wireSignupForm() {
  const form = document.querySelector('#signup-form');
  if (!form) return;

  // Signup page reuses the shared .error-msg style but the markup
  // may not have the div yet — create it once if missing.
  let errorBox = form.querySelector('.error-msg');
  if (!errorBox) {
    errorBox = document.createElement('div');
    errorBox.className = 'error-msg';
    form.prepend(errorBox);
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    const name = form.querySelector('#s-name').value.trim();
    const email = form.querySelector('#s-email').value.trim();
    const phone = form.querySelector('#s-phone').value.trim();
    const password = form.querySelector('#s-password').value.trim();
    const role = form.querySelector('input[name="role"]:checked')?.value || 'patient';

    const showError = (msg) => { errorBox.textContent = msg; errorBox.classList.add('show'); };

    if (!name || !email || !phone || !password) {
      showError('সব ফিল্ড পূরণ করতে হবে।');
      return;
    }

    if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'তৈরি হচ্ছে...'; }
    errorBox.classList.remove('show');

    try {
      const res = await fetch('api/signup.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, phone, password, role }),
      });
      const data = await res.json();

      if (data.success) {
        window.location.href = data.redirect;
      } else {
        showError(data.message || 'অ্যাকাউন্ট তৈরি করা যায়নি।');
      }
    } catch (err) {
      showError('সার্ভারের সাথে সংযোগ করা যায়নি। XAMPP-এ Apache ও MySQL চালু আছে কিনা দেখো, এবং পেজটা http://localhost/... দিয়ে খোলা হয়েছে কিনা নিশ্চিত করো।');
    } finally {
      if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Create Account'; }
    }
  });
}

// ---------- Dashboard: availability toggles, status dropdowns ----------
function wireDashboardToggles() {
  document.querySelectorAll('.toggle input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => {
      const row = cb.closest('tr');
      const dot = row?.querySelector('.status-dot');
      if (dot) {
        dot.classList.toggle('online', cb.checked);
        dot.classList.toggle('offline', !cb.checked);
        dot.textContent = cb.checked ? 'Available' : 'Unavailable';
      }
    });
  });
}

// ---------- Sidebar: mark current page active ----------
function wireSidebarActive() {
  const path = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.side-links a, .nav-links a').forEach(a => {
    const href = a.getAttribute('href');
    if (href === path) a.classList.add('active');
  });
}

// ---------- Current user (wired to api/me.php) ----------
function wireCurrentUser() {
  const nameEl = document.querySelector('#user-name');
  const avatarEl = document.querySelector('#user-avatar');
  const greetingEl = document.querySelector('#user-greeting');
  if (!nameEl && !avatarEl && !greetingEl) return;

  fetch('api/me.php')
    .then(res => res.json())
    .then(data => {
      if (!data.success) return;
      if (nameEl) nameEl.textContent = data.name;
      if (avatarEl) avatarEl.textContent = data.initials;
      if (greetingEl) {
        const firstName = data.name.split(' ')[0];
        const lastName = data.name.split(' ').slice(-1)[0];
        if (data.role === 'doctor') {
          greetingEl.textContent = `Good afternoon, Dr. ${lastName}`;
        } else if (data.role === 'sitter') {
          greetingEl.textContent = `Welcome back, ${firstName}`;
        } else {
          greetingEl.textContent = `Hello, ${firstName}`;
        }
      }
    })
    .catch(() => { /* not logged in or request failed — keep placeholder text */ });
}

// ---------- Log out (wired to api/logout.php) ----------
function wireLogoutButton() {
  const btn = document.querySelector('#logout-btn');
  if (!btn) return;
  btn.addEventListener('click', async (e) => {
    e.preventDefault();
    try {
      const res = await fetch('api/logout.php');
      const data = await res.json();
      window.location.href = data.redirect || 'index.html';
    } catch (err) {
      // Even if the request fails, send the user back to the homepage.
      window.location.href = 'index.html';
    }
  });
}
