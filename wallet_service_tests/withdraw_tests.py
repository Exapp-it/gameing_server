from wallet_service_tests_common import *

@pytest.fixture()
def test_data():
    url = 'http://localhost:8000/api/wallet_service/Withdraw'

    name = 'f5a858f352755a07e492381ab698b31b'
    amount = '20.00'
    currency = 'RUB'
    reference = 353534534
    sessionID = 222372
    gameRoundId = 5645654645
    gameModule = 'VS243Crystal_TNP'
    fgbCampaignCode = ""

    return {
        'url': url,
        'json': {
            'partnerID': partner_id,
            'name': name,
            'amount': amount,
            'currency': currency,
            'reference': reference,
            'sessionID': sessionID,
            'gameRoundID': gameRoundId,
            'gameModule': gameModule,
            'fgbCampaignCode': fgbCampaignCode,
        }
    }

def test_Withdraw_invalid_partnerID(test_data):
    test_data['json']['partnerID'] = '32423423'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['partner_err']

def test_Withdraw_sign_error(test_data):
    test_data['json']['sign'] = ''
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['sign_err']

def test_Withdraw_identity_error(test_data):
    test_data['json']['name'] = '32423423'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['identity_err']

def test_Withdraw_invalid_amount(test_data):
    test_data['json']['amount'] = 'sdfdsf'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['params_err']

def test_Withdraw_invalid_sessionID(test_data):
    test_data['json']['sessionID'] = 0
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['params_err']

def test_Withdraw_funds_error(test_data):
    test_data['json']['amount'] = 12212312312
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['funds_err']

def test_Withdraw_invalid_gameModule(test_data):
    test_data['json']['gameModule'] = 'dsf'
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['params_err']

def test_Withdraw_normal(test_data):
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])
    print(resp.text)
    print(test_data['json']['sign'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['ok']
    assert resp['Transaction']
