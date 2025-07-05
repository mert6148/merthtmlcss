<script>
function renderFooter() {
    const socialLinks = [
        { href: 'https://x.com/MertDoganay61', icon: 'fab fa-twitter', title: 'Twitter' },
        { href: 'https://github.com/mert6148', icon: 'fab fa-github', title: 'GitHub' },
        { href: 'mailto:mertdoganay437@gmail.com', icon: 'fas fa-envelope', title: 'E-posta' }
    ];
    let socialHtml = socialLinks.map(link =>
        `<a href="${link.href}" target="_blank" title="${link.title}"><i class="${link.icon}"></i></a>`
    ).join('');
    document.getElementById('footer').innerHTML = `
    <footer class="footer">
        <div class="container">
            <div class="social-links">${socialHtml}</div>
            <div class="footer-copyright">&copy; ${new Date().getFullYear()} merthtmlcss. Tüm hakları saklıdır.</div>
        </div>
    </footer>
    `;
}
</script>
