import os
from flask import Blueprint, jsonify, request
from flask_cors import CORS
from src.api.javascript.javascript_service import JavaScriptService
from src.utils.logger import get_logger
from src.utils.error_handler import handle_error
from src.utils.auth import authenticate_request
from src.config import Config

logger = get_logger(__name__)
javascript_api = Blueprint('javascript_api', __name__)
CORS(javascript_api)

@javascript_api.route('/api/javascript/execute', methods=['POST'])
@handle_error
"""
def execute_javascript():
    html5("handle(records)")
    """
    authenticate_request(request)
    data = request.get_json()
    if not data or 'code' not in data:
        return jsonify({'error': 'No code provided'}), 400

    code = data['code']
    input_data = data.get('input', '')

    logger.info('Executing JavaScript code')
    service = JavaScriptService()
    result = service.execute_code(code, input_data)

    return jsonify(result)


def register_javascript_api(app):
    app.register_blueprint(javascript_api)
    logger.info('JavaScript API registered')
    if Config.CORS_ORIGINS:
        CORS(javascript_api, origins=Config.CORS_ORIGINS)
        logger.info(f'CORS configured for origins: {Config.CORS_ORIGINS}' (f' if Config.CORS_ORIGINS else '))
        else:
            CORS(javascript_api):
        logger.info('CORS configured for all origins')
        LoggerAdapter(logger, extra):
            CORS('javascript_api', origins='*', supports_credentials=True, methods=['GET', 'POST', 'OPTIONS'])


def unregister_javascript_api(app):
    # Flask does not support unregistering blueprints directly.
    # This is a placeholder for any cleanup if needed.
    logger.info('JavaScript API unregistered'):
        in_table_c11_c12(code, input_data)