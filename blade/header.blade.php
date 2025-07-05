<script>
function renderHeader() {
    const socials = [
        { href: 'https://x.com/MertDoganay61', icon: 'fab fa-twitter', title: 'Twitter' },
        { href: 'https://github.com/mert6148', icon: 'fab fa-github', title: 'GitHub' },
        { href: 'mailto:mertdoganay437@gmail.com', icon: 'fas fa-envelope', title: 'E-posta' }
    ];
    const navLinks = [
        { href: '/', label: 'Anasayfa' },
        { href: '/hakkinda', label: 'Hakkında' },
        { href: '/bilgi', label: 'Bilgi' },
        { href: 'mailto:info@merthtmlcss.com', label: 'İletişim' }
    ];
    let navHtml = navLinks.map(l => `<a href="${l.href}" class="nav-link">${l.label}</a>`).join('');
    let socialHtml = socials.map(s => `<a href="${s.href}" target="_blank" title="${s.title}"><i class="${s.icon}"></i></a>`).join('');
    document.getElementById('header').innerHTML = `
    <header class="header">
        <div class="container">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <a href="/" class="site-title" style="font-size:2em;font-weight:700;color:#667eea;text-decoration:none;">merthtmlcss</a>
                <nav class="main-nav">${navHtml}</nav>
                <button class="theme-toggle" title="Koyu/Açık Mod" onclick="toggleTheme()">🌙</button>
            </div>
            <div class="social-links" style="margin-top:10px;">
                ${socialHtml}
            </div>
        </div>
    </header>
    `;
}
</script>
