from mancala_service_tests_common import *
from uuid import uuid4

@pytest.fixture()
def test_data():
    url = 'http://localhost:8000/api/mancala_service/Refund'

    return {
        'url': url,
        'json': {
            'SessionId': '',
            'TransactionGuid': '',
            'RefundTransactionGuid': '',
            'RoundGuid': '',
            'Amount': '123.1232',
        },
        'extra': {
            'path': '/Refund',
            'key': key,
        }
    }

def test_Refund_invalid_hash(test_data):
    test_data['json']['SessionId'] = 'some id'
    test_data['json']['Hash'] = ''

    resp = requests.post(
        test_data['url'],
        json=test_data['json']
    )

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Error'] == CODES['HashMismatch']

def test_Refund_invalid_sessionId(test_data):
    test_data['json']['SessionId'] = 'some id'
    test_data['json']['TransactionGuid'] = str(uuid4())
    test_data['json']['RefundTransactionGuid'] = str(uuid4())
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
    
    assert resp['Msg'] == "No user with such session_id"


def test_Refund_invalid_transaction(test_data):
    test_data['json']['SessionId'] = 'c0a27c6f-b4b4-4887-8a62-6d925eba49f2'
    test_data['json']['TransactionGuid'] = str(uuid4())
    test_data['json']['RefundTransactionGuid'] = str(uuid4())
    test_data['json']['RoundGuid'] = str(uuid4())
    test_data['json']['Amount'] = '12341241241'
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
    
    assert resp['Msg'] == 'No such transaction'

def test_Refund_normal_transaction(test_data):
    test_data['json']['SessionId'] = 'c0a27c6f-b4b4-4887-8a62-6d925eba49f2'
    test_data['json']['TransactionGuid'] = str(uuid4())
    test_data['json']['RefundTransactionGuid'] = '382e30c1-bf04-4dc3-9642-0699829fcadd'
    test_data['json']['RoundGuid'] = str(uuid4())
    test_data['json']['Amount'] = '12341241241'
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
    
    assert resp['Error'] == CODES['NoErrors']


def test_Refund_already_refund(test_data):
    test_data['json']['SessionId'] = 'c0a27c6f-b4b4-4887-8a62-6d925eba49f2'
    test_data['json']['TransactionGuid'] = str(uuid4())
    test_data['json']['RefundTransactionGuid'] = 'c3498dff-9d90-44ed-9e75-5e807df92f74'
    test_data['json']['RoundGuid'] = str(uuid4())
    test_data['json']['Amount'] = '12341241241'
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
    
    assert resp['Msg'] == 'Transaction already rollbacked'
