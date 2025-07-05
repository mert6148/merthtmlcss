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

if __name__ == '__main__':
    app.run(debug=True, port=5001)
