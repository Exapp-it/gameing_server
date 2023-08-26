from wallet_service_tests_common import *
from random import randint

@pytest.fixture()
def test_data():
    url = 'http://localhost:8000/api/wallet_service/Deposit'
    #url = 'https://vm4131020.62ssd.had.wf/api/wallet_service/Deposit'

    name = 'f5a858f352755a07e492381ab698b31b'
    amount = '50.30'
    currency = 'RUB'
    reference = '234324'
    sessionID = 222372
    gameRoundId = 45645456
    gameModule = 'VS243Crystal_TNP'
    type = 0
    fgbCampaignCode = ""
    isRoundEnd = "False"

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
            'type': type,
            'fgbCampaignCode': fgbCampaignCode,
            'isRoundEnd': isRoundEnd
        }
    }

def test_Deposit_invalid_partnerID(test_data):
    test_data['json']['partnerID'] = '32423423'
    test_data['json']['reference'] = str(randint(10000, 90000))
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['partner_err']

def test_Deposit_sign_error(test_data):
    test_data['json']['sign'] = ''
    test_data['json']['reference'] = str(randint(10000, 90000))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['sign_err']

def test_Deposit_identity_error(test_data):
    test_data['json']['name'] = '32423423'
    test_data['json']['reference'] = str(randint(10000, 90000))
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['identity_err']

def test_Deposit_invalid_amount(test_data):
    test_data['json']['amount'] = 'sdfdsf'
    test_data['json']['reference'] = str(randint(10000, 90000))
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['params_err']

def test_Deposit_invalid_gameModule(test_data):
    test_data['json']['gameModule'] = 'dsf'
    test_data['json']['reference'] = str(randint(10000, 90000))
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['params_err']

def test_Deposit_normal(test_data):
    #test_data['json']['isRoundEnd'] = 'True'
    test_data['json']['reference'] = str(randint(10000, 90000))
    test_data['json']['sign'] = gen_sign(list(test_data['json'].values()))
    resp = requests.post(test_data['url'], json=test_data['json'])

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Code'] == CODES['ok']
    assert resp['Transaction']
