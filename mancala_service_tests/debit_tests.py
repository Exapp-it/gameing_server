from mancala_service_tests_common import *
from uuid import uuid4

@pytest.fixture()
def test_data():
    url = 'http://localhost:8000/api/mancala_service/Debit'

    return {
        'url': url,
        'json': {
            'SessionId': '',
            'TransactionGuid': '',
            'RoundGuid': '',
            'Amount': '123.1232',
        },
        'extra': {
            'path': '/Debit',
            'key': key,
        }
    }

def test_Debit_invalid_hash(test_data):
    test_data['json']['SessionId'] = 'some id'
    test_data['json']['Hash'] = ''

    resp = requests.post(
        test_data['url'],
        json=test_data['json']
    )

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Error'] == CODES['HashMismatch']

def test_Debit_invalid_sessionId(test_data):
    test_data['json']['SessionId'] = 'some id'
    test_data['json']['TransactionGuid'] = str(uuid4())
    test_data['json']['RoundGuid'] = str(uuid4())
    test_data['json']['Hash'] = gen_hash(
        [test_data['extra']['path']] +
        list(test_data['json'].values()) +
        [test_data['extra']['key']]
    )

    resp = requests.post(
        test_data['url'],
        json=test_data['json']
    )

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Error'] == CODES['InternalServiceError']

def test_Debit_normal(test_data):
    test_data['json']['SessionId'] = 'c0a27c6f-b4b4-4887-8a62-6d925eba49f2'
    test_data['json']['TransactionGuid'] = str(uuid4())
    test_data['json']['RoundGuid'] = str(uuid4())
    test_data['json']['Amount'] = '12'
    test_data['json']['Hash'] = gen_hash(
        [test_data['extra']['path']] +
        list(test_data['json'].values()) +
        [test_data['extra']['key']]
    )

    resp = requests.post(
        test_data['url'],
        json=test_data['json']
    )

    print(resp.text)

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Error'] == CODES['NoErrors']
