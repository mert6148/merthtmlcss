from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import html

app = Flask(__name__)
CORS(app)

# Örnek veri
overiler = {
    "mesaj": "Merthtmlcss API'ye hoş geldiniz! Modern web geliştirme için kapsamlı bir platform.",
    "iletisim": "mertdoganay437@gmail.com"
}

# Ana sayfa GET
@app.route('/', methods=['GET'])
def home():
    return """
    <h1>Merthtmlcss Python API</h1>
    <p>Hoş geldiniz! Aşağıdaki endpointleri kullanabilirsiniz:</p>
    <ul>
      <li>/api/about - Proje hakkında bilgi</li>
      <li>/api/contact - İletişim bilgileri (GET/POST)</li>
      <li>/print - Veri gönderimi ve test (GET/POST)</li>
    </ul>
    """

# GET işlemi: /api/about
@app.route('/api/about', methods=['GET'])
def about():
    return jsonify({
        "proje": "Merthtmlcss",
        "aciklama": "Merthtmlcss, modern web geliştirme için HTML, CSS, JavaScript ve daha fazlasını bir araya getiren kapsamlı bir projedir. API üzerinden proje bilgilerine ve iletişim detaylarına ulaşabilirsiniz.",
        "ornek": {
            "kullanici": "Mert Doğanay",
            "proje": "Merthtmlcss",
            "aciklama": "Bu endpoint, API üzerinden proje hakkında bilgi verir."
        }
    })

# GET işlemi: /api/contact
@app.route('/api/contact', methods=['GET'])
def contact():
    return jsonify({
        "email": veriler["iletisim"],
        "mesaj": "Bize ulaşmak için e-posta gönderebilir veya iletişim formunu kullanabilirsiniz.",
        "ornek": {
            "isim": "Mert Doğanay",
            "mesaj": "Merhaba, projeniz hakkında bilgi almak istiyorum."
        }
    })

# POST işlemi: /api/contact (mesajı dosyaya kaydeder)
@app.route('/api/contact', methods=['POST'])
def post_contact():
    data = request.get_json(force=True, silent=True)
    if not data or not data.get("isim") or not data.get("mesaj"):
        return jsonify({"status": "error", "hata": "İsim ve mesaj alanı zorunludur."}), 400
    isim = html.escape(data.get("isim"))
    mesaj = html.escape(data.get("mesaj"))
    try:
        with open("gelen_mesajlar.txt", "a", encoding="utf-8") as f:
            f.write(f"Isim: {isim}\nMesaj: {mesaj}\n---\n")
    except Exception as e:
        return jsonify({"status": "error", "hata": str(e)}), 500
    return jsonify({
        "status": "success",
        "gelen_isim": isim,
        "gelen_mesaj": mesaj,
        "cevap": "Mesajınız başarıyla alındı ve kaydedildi. Merthtmlcss API ile iletişiminiz için teşekkürler!"
    }), 201

# Hatalı endpointler için özel hata mesajı
@app.errorhandler(404)
def not_found(e):
    return jsonify({"error": "Böyle bir endpoint yok. Lütfen dökümantasyonu kontrol edin."}), 404

if __name__ == '__main__':
    app.run(debug=True) 