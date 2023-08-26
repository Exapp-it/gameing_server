import pytest
import requests
from hashlib import md5

from dotenv import dotenv_values

config = dotenv_values("../.env")
guid = config['MANCALA_GUID']
key = config['MANCALA_KEY']

CODES = {
    'NoErrors'                      : 0,
    'HashMismatch'                  : 1,
    'PartnerDisabled'               : 2,
    'CreateTokenError'              : 3,
    'InternalServiceError'          : 4,
    'PlayerBlocked'                 : 5,
    'PlayerRegistrationFailed'      : 6,
    'CurrencyNotFound'              : 7,
    'GameNotFound'                  : 8,
    'GameNotAllowed'                : 9,
    'ExtraDataTooLong'              : 10,
    'BonusFSExternalIdAlreadyExist' : 11,
}

def gen_hash(params):
    return md5(''.join(params).encode()).hexdigest()
