from wallet_service_tests_common import *

@pytest.fixture()
def test_data():
    url = 'http://localhost:8000/api/wallet_service/RollbackTransaction'

    name = 'f5a858f352755a07e492381ab698b31b'
    reference = 82979
    sessionID = 222372

    return {
        'url': url,
        'json': {
            'partnerID': partner_id,
            'name': name,
            'reference': reference,
            'sessionID': sessionID
        }
    }

def test_Rollback_invalid_partnerID(test_data):
    test_data['json']['partnerID'] = '32423423'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['partner_err']

def test_Rollback_sign_error(test_data):
    test_data['json']['sign'] = ''
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['sign_err']

def test_Rollback_identity_error(test_data):
    test_data['json']['name'] = '32423423'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['identity_err']

def test_Rollback_invalid_reference(test_data):
    test_data['json']['reference'] = 'odfo'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['transaction_err']

def test_Rollback_normal(test_data):
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    print(resp.text)
    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['ok']
