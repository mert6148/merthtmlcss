<script>
function renderAside(userName = 'Mert', profileDesc = 'Modern web geliştirme, açık kaynak ve eğitim içerikleri.') {
    const links = [
        { url: '/', label: 'Anasayfa' },
        { url: '/hakkinda', label: 'Hakkında' },
        { url: '/bilgi', label: 'Bilgi' },
        { url: 'mailto:info@merthtmlcss.com', label: 'İletişim' }
    ];
    let navHtml = links.map(l => `<a href="${l.url}" class="nav-link">${l.label}</a>`).join('');
    document.getElementById('aside').innerHTML = `
    <aside class="aside">
        <div style="text-align:center;">
            <img src="/images/profile.png" alt="Profil" style="width:80px;height:80px;border-radius:50%;box-shadow:0 2px 8px rgba(102,126,234,0.10);margin-bottom:10px;">
            <h3 style="color:#667eea;margin-bottom:6px;">${userName}</h3>
            <p style="font-size:0.98em;color:#555;">${profileDesc}</p>
        </div>
        <hr style="margin:16px 0;">
        <nav style="display:flex;flex-direction:column;gap:8px;">
            ${navHtml}
        </nav>
    </aside>
    `;
}
</script>
