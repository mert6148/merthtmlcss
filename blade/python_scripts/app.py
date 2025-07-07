# Modern ve Kapsamlı CSS & JS Framework Python Scripti
# Geliştirici: Mert Doğanay
# Versiyon: 2.0

import datetime
import sys
import os

# Çoklu dil desteği için örnek sözlük
LANG = {
    'tr': {
        'welcome': 'Hoş Geldiniz!',
        'desc': 'Bu modern CSS ve JavaScript framework ile güzel arayüzler oluşturabilirsiniz.',
        'theme': 'Tema Değiştir',
        'alt_theme': 'Alternatif Tema',
        'feature1': 'Modern CSS değişkenleri ve animasyonlar',
        'feature2': 'Responsive tasarım ve hover efektleri',
        'feature3': 'JavaScript ile dinamik etkileşimler',
        'feature_title1': '✨ Özellik 1',
        'feature_title2': '🚀 Özellik 2',
        'feature_title3': '⚡ Özellik 3',
        'ai_suggestion': 'AI destekli kod tamamlama aktif!'
    },
    'en': {
        'welcome': 'Welcome!',
        'desc': 'With this modern CSS and JavaScript framework, you can create beautiful interfaces.',
        'theme': 'Switch Theme',
        'alt_theme': 'Alternative Theme',
        'feature1': 'Modern CSS variables and animations',
        'feature2': 'Responsive design and hover effects',
        'feature3': 'Dynamic interactions with JavaScript',
        'feature_title1': '✨ Feature 1',
        'feature_title2': '🚀 Feature 2',
        'feature_title3': '⚡ Feature 3',
        'ai_suggestion': 'AI-powered code completion is active!'
    }
}

def print_css_js(lang='tr'):
    css_link = '<link rel="stylesheet" href="merthtmlcss-styles.css">'
    js_link = '<script src="script.js"></script>'
    now = datetime.datetime.now().strftime('%Y-%m-%d %H:%M')
    t = LANG.get(lang, LANG['tr'])
    html = f'''
<!DOCTYPE html>
<html lang="{lang}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern CSS & JS Framework</title>
    {css_link}
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; margin-bottom: 30px; color: var(--primary);">
            🎨 Modern CSS & JS Framework
        </h1>
        <div class="card fade-in">
            <h3>{t['welcome']}</h3>
            <p>{t['desc']}</p>
            <div class="notification" id="ai-suggestion">{t['ai_suggestion']}</div>
        </div>
        <div style="text-align: center; margin: 20px 0;">
            <button class="button theme-toggle">🌙 {t['theme']}</button>
            <button class="button" onclick="temaDegistir()">🎨 {t['alt_theme']}</button>
        </div>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
            <div class="card">
                <h4>{t['feature_title1']}</h4>
                <p>{t['feature1']}</p>
            </div>
            <div class="card">
                <h4>{t['feature_title2']}</h4>
                <p>{t['feature2']}</p>
            </div>
            <div class="card">
                <h4>{t['feature_title3']}</h4>
                <p>{t['feature3']}</p>
            </div>
        </div>
        <footer style="margin-top:40px; text-align:center; color:var(--gray); font-size:0.95em;">
            <span>Geliştirici: Mert Doğanay | Sürüm: 2.0 | {now}</span>
        </footer>
    </div>
    {js_link}
</body>
</html>
'''
    with open('modern-app.html', 'w', encoding='utf-8') as f:
        f.write(html)
    print("✅ Modern CSS & JS Framework oluşturuldu: modern-app.html")
    print(html)

if __name__ == "__main__":
    print(f"Python sürümü: {sys.version}")
    print(f"Çalışma dizini: {os.getcwd()}")
    print_css_js('tr')
