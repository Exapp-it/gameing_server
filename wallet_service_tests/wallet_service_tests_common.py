import hmac
import hashlib
import requests
import pytest

from dotenv import dotenv_values

config = dotenv_values("../.env")
partner_id = config['TOM_HORN_API_PARTNER_ID']
secret_key = config['TOM_HORN_API_SECRET_KEY']

CODES = {
    'ok'             : 0,
    'general_err'    : 1,
    'params_err'     : 2,
    'sign_err'       : 3,
    'partner_err'    : 4,
    'identity_err'   : 5,
    'funds_err'      : 6,
    'currency_err'   : 8,
    'rollback_err'   : 9,
    'limit_err'      : 10,
    'reference_err'  : 11,
    'transaction_err': 12
}

def gen_sign(params):
    return hmac.new(
        secret_key.encode(),
        b''.join([str(param).encode() for param in params]),
        hashlib.sha256
    ).hexdigest().upper()
