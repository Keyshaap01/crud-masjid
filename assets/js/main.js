// ===== Menu mobile =====
const toggle = document.getElementById('navToggle');
const menu = document.getElementById('navMenu');
if (toggle) {
    toggle.addEventListener('click', () => {
        toggle.classList.toggle('open');
        menu.classList.toggle('open');
    });
}

// ===== Bayangan navbar saat scroll =====
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 10);
});

// ===== Jam & hitung mundur waktu sholat =====
const pad = (n) => String(n).padStart(2, '0');
const clock = document.querySelector('[data-clock]');
const cd = document.querySelector('[data-countdown]');
const cdDisplay = document.querySelector('[data-countdown-display]');

function tick() {
    const now = new Date();
    if (clock) clock.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

    if (cd && cdDisplay) {
        const [h, m] = cd.dataset.next.split(':').map(Number);
        const target = new Date(now);
        target.setHours(h, m, 0, 0);
        if (cd.dataset.besok === '1') target.setDate(target.getDate() + 1);

        let diff = Math.floor((target - now) / 1000);
        if (diff <= 0) {
            cdDisplay.textContent = 'Telah masuk waktu';
            // Muat ulang agar jadwal berikutnya ditampilkan
            if (diff < -60) location.reload();
            return;
        }
        const jam = Math.floor(diff / 3600);
        const menit = Math.floor((diff % 3600) / 60);
        const detik = diff % 60;
        cdDisplay.textContent = `${pad(jam)}:${pad(menit)}:${pad(detik)}`;
    }
}
tick();
setInterval(tick, 1000);

// ===== Salin nomor rekening =====
document.querySelectorAll('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(btn.dataset.copy);
            btn.textContent = 'Tersalin ✓';
            btn.classList.add('copied');
            setTimeout(() => { btn.textContent = 'Salin'; btn.classList.remove('copied'); }, 2000);
        } catch {
            btn.textContent = 'Gagal';
        }
    });
});

// ===== Format input rupiah (1.500.000) =====
document.querySelectorAll('[data-rupiah]').forEach((input) => {
    input.addEventListener('input', () => {
        const angka = input.value.replace(/\D/g, '');
        input.value = angka ? Number(angka).toLocaleString('id-ID') : '';
    });
});
