from flask import Flask, request

app = Flask(__name__)

@app.route('/setup', methods=['GET', 'POST'])
def setup_page():
    message = ''
    if request.method == 'POST':
        site_name = request.form.get('site_name', '').strip()
        admin_email = request.form.get('admin_email', '').strip()
        if not site_name or not admin_email:
            message = '<div class="info" style="color:#c00;">Tüm alanları doldurmanız gerekmektedir.</div>'
        else:
            try:
                with open('kurulum_bilgileri.txt', 'w', encoding='utf-8') as f:
                    f.write(f"Site Adı: {site_name}\nYönetici E-posta: {admin_email}\n")
                message = '<div class="info" style="color:green;">Kurulum başarıyla tamamlandı! Bilgiler kaydedildi.</div>'
            except Exception as e:
                message = f'<div class="info" style="color:#c00;">Hata: {str(e)}</div>'
    return f"""
    <html lang=\"tr\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>Merthtmlcss Kurulum</title>
        <style>
            body {{ font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #222; margin: 0; padding: 0; }}
            .container {{ max-width: 480px; margin: 60px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(14,160,160,0.10); padding: 36px; }}
            h1 {{ color: #0ea0a0; margin-bottom: 18px; }}
            label {{ display: block; margin-top: 16px; font-weight: 600; }}
            input {{ width: 100%; padding: 10px; margin-top: 6px; border-radius: 7px; border: 1px solid #b2dfdb; }}
            button {{ margin-top: 24px; background: #0ea0a0; color: #fff; border: none; padding: 12px 32px; border-radius: 8px; cursor: pointer; font-size: 1.1em; }}
            button:hover {{ background: #097c7c; }}
            .info {{ margin-top: 18px; color: #555; font-size: 0.98em; }}
        </style>
    </head>
    <body>
        <div class=\"container\">
            <h1>Merthtmlcss Kurulum Sihirbazı</h1>
            <form method=\"post\" action=\"/setup\">
                <label for=\"site_name\">Site Adı:</label>
                <input type=\"text\" id=\"site_name\" name=\"site_name\" required>
                <label for=\"admin_email\">Yönetici E-posta:</label>
                <input type=\"email\" id=\"admin_email\" name=\"admin_email\" required>
                <button type=\"submit\">Kurulumu Başlat</button>
            </form>
            {message}
            <div class=\"info\">Kuruluma başlamak için bilgileri doldurun ve butona tıklayın.</div>
        </div>
    </body>
    </html>
    """

if __name__ == '__main__':
    app.run(debug=True, port=5002)
