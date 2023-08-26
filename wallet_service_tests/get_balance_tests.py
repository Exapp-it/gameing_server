from wallet_service_tests_common import *

@pytest.fixture()
def test_data():
    url = 'http://localhost:8000/api/wallet_service/GetBalance'

    name = 'f5a858f352755a07e492381ab698b31b'
    currency = 'RUB'
    sessionID = 220773
    gameModule = ''
    type = ''

    return {
        'url': url,
        'json': {
            'partnerID': partner_id,
            'name': name,
            'currency': currency,
            'sessionID': sessionID,
            'gameModule': gameModule,
            'type': type
        }
    }

def test_GetBalance_invalid_partnerID(test_data):
    test_data['json']['partnerID'] = 'unknown_id'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))

    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['partner_err']

def test_GetBalance_invalid_identity(test_data):
    test_data['json']['name'] = 'unknown_identity'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))

    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['identity_err']

def test_GetBalance_normal(test_data):
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['ok']
    assert resp['Balance']
