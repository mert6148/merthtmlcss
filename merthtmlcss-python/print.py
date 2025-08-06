from flask import Flask, request, jsonify
from flask_cors import CORS
import html

app = Flask(__name__)
CORS(app)

# GET işlemi: /print
@app.route('/print', methods=['GET'])
def print_get():
    return jsonify({
        "mesaj": "Merthtmlcss Print API'ye hoş geldiniz! Bu endpoint ile veri gönderebilir ve test edebilirsiniz.",
        "ornek": {
            "kullanici": "Mert Doğanay",
            "proje": "Merthtmlcss",
            "aciklama": "Bu endpoint, API üzerinden veri gönderimi ve test için kullanılır."
        }
    })

# POST işlemi: /print
@app.route('/print', methods=['POST'])
def print_post():
    data = request.get_json(force=True, silent=True)
    if not data:
        return jsonify({"status": "error", "hata": "Veri gönderilmedi."}), 400
    # Tüm değerleri sanitize et
    temiz_data = {k: html.escape(str(v)) for k, v in data.items()}
    print(f"Gelen veri: {temiz_data}")
    return jsonify({
        "status": "success",
        "gelen_veri": temiz_data,
        "cevap": "Veriniz başarıyla alındı ve işlendi. Merthtmlcss API ile test başarılı!"
    })

# HTML ve CSS ile test sayfası
@app.route('/print/html', methods=['GET'])
def print_html():
    return """
    <html lang=\"tr\">
    <head>
        <meta charset=\"UTF-8\">
        <title>Print API Test</title>
        <style>
            body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #222; }
            .container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(14,160,160,0.10); padding: 32px; }
            h1 { color: #0ea0a0; }
            label { display: block; margin-top: 12px; }
            input, textarea { width: 100%; padding: 8px; margin-top: 4px; border-radius: 6px; border: 1px solid #b2dfdb; }
            button { margin-top: 16px; background: #0ea0a0; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; }
            button:hover { background: #097c7c; }
        </style>
    </head>
    <body>
        <div class=\"container\">
            <h1>Print API Test</h1>
            <form method=\"post\" action=\"/print\">
                <label for=\"kullanici\">Kullanıcı:</label>
                <input type=\"text\" id=\"kullanici\" name=\"kullanici\" required>
                <label for=\"mesaj\">Mesaj:</label>
                <textarea id=\"mesaj\" name=\"mesaj\" required></textarea>
                <button type=\"submit\">Gönder</button>
            </form>
        </div>
    </body>
    </html>
    """

if __name__ == '__main__':
    app.run(debug=True, port=5001)
