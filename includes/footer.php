</main>

<footer class="footer">
    <div class="container footer-grid">
        <div>
            <h3><?= e($site['nama']) ?></h3>
            <p><?= e($site['tagline']) ?>.</p>
            <p class="muted">📍 <?= e($site['alamat']) ?></p>
        </div>
        <div>
            <h4>Tautan</h4>
            <ul>
                <?php foreach ($menu as $file => $label): ?>
                    <li><a href="<?= $file ?>.php"><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <h4>Hubungi Kami</h4>
            <ul>
                <li>📞 <?= e($site['telepon']) ?></li>
                <li>✉️ <?= e($site['email']) ?></li>
                <li><a href="https://wa.me/<?= e($site['whatsapp']) ?>" target="_blank" rel="noopener">💬 WhatsApp DKM</a></li>
            </ul>
            <div class="sosmed">
                <?php foreach ($site['sosmed'] as $nama => $url): ?>
                    <a href="<?= e($url) ?>" target="_blank" rel="noopener"><?= e($nama) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        © <?= date('Y') ?> <?= e($site['nama']) ?>. Dikelola oleh DKM <?= e($site['nama']) ?>. &nbsp;•&nbsp; <a href="admin/">Login Admin</a>
    </div>
</footer>

<a href="https://wa.me/<?= e($site['whatsapp']) ?>" class="wa-float" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm5.3 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-3.9-4.7-4.1-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.3 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.6 2.1 1.1 1 2 1.3 2.3 1.4.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l2 1c.3.1.5.2.5.3.1.2.1.7-.1 1.2Z"/></svg>
</a>

<script src="assets/js/main.js"></script>
</body>
</html>
