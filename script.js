// script.js — ES6+ interactivity

/* ── Hamburger Menu ────────────────────────────────────────────── */
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');

if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    const open = navLinks.classList.toggle('open');
    hamburger.setAttribute('aria-expanded', open);
  });
}

/* ── Show/Hide Password ────────────────────────────────────────── */
document.querySelectorAll('.toggle-pw').forEach(btn => {
  btn.addEventListener('click', () => {
    const targetId = btn.dataset.target;
    const input    = document.getElementById(targetId);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.textContent   = isHidden ? '🙈' : '👁';
    btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
  });
});

/* ── Password Strength Meter ───────────────────────────────────── */
const pwInput      = document.getElementById('password');
const strengthFill = document.getElementById('strengthFill');
const strengthLabel = document.getElementById('strengthLabel');

if (pwInput && strengthFill && strengthLabel) {
  pwInput.addEventListener('input', () => {
    const val = pwInput.value;
    let score = 0;

    if (val.length >= 8)              score++;
    if (val.length >= 12)             score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const levels = [
      { pct: '0%',   color: 'transparent', label: '' },
      { pct: '25%',  color: '#f87171',     label: 'Weak' },
      { pct: '50%',  color: '#fb923c',     label: 'Fair' },
      { pct: '75%',  color: '#facc15',     label: 'Good' },
      { pct: '100%', color: '#34d399',     label: 'Strong' },
    ];

    const level = levels[Math.min(score, 4)];
    strengthFill.style.width      = val.length ? level.pct   : '0%';
    strengthFill.style.background = level.color;
    strengthLabel.textContent     = val.length ? level.label : '';
  });
}

/* ── Scroll-reveal Feature Cards ──────────────────────────────── */
const revealCards = document.querySelectorAll('[data-reveal]');

if ('IntersectionObserver' in window && revealCards.length) {
  const observer = new IntersectionObserver(entries => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => entry.target.classList.add('revealed'), i * 120);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  revealCards.forEach(card => observer.observe(card));
} else {
  // Fallback: show all immediately
  revealCards.forEach(card => card.classList.add('revealed'));
}

/* ── Live Clock on Dashboard ───────────────────────────────────── */
const clockEl = document.getElementById('serverTime');
if (clockEl) {
  setInterval(() => {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    clockEl.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
  }, 1000);
}
