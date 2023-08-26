from mancala_service_tests_common import *

@pytest.fixture()
def test_data():
    url = 'http://localhost:8000/api/mancala_service/Balance'

    return {
        'url': url,
        'json': {
            'path': '/Balance',
            'SessionId': '',
            'key': key
        }
    }

def test_Balance_invalid_hash(test_data):
    test_data['json']['SessionId'] = 'some id'
    test_data['json']['Hash'] = ''

    resp = requests.post(
        test_data['url'],
        json={
            'SessionId': test_data['json']['SessionId'],
            'Hash': test_data['json']['Hash'],
            'ExtraData': 'some data'
        }
    )

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Error'] == CODES['HashMismatch']

def test_Balance_invalid_sessionId(test_data):
    test_data['json']['SessionId'] = 'some id'
    test_data['json']['Hash'] = gen_hash(list(test_data['json'].values()))

    resp = requests.post(
        test_data['url'],
        json={
            'SessionId': test_data['json']['SessionId'],
            'Hash': test_data['json']['Hash'],
            'ExtraData': 'some data'
        }
    )

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Error'] == CODES['InternalServiceError']

def test_Balance_normal(test_data):
    test_data['json']['SessionId'] = '976bd658-e4e3-45fa-962e-033efc3015c1'
    test_data['json']['Hash'] = gen_hash(list(test_data['json'].values()))

    resp = requests.post(
        test_data['url'],
        json={
            'SessionId': test_data['json']['SessionId'],
            'Hash': test_data['json']['Hash'],
            'ExtraData': 'some data'
        }
    )

    assert resp.status_code == 200
    resp = resp.json()
    
    assert resp['Error'] == CODES['NoErrors']
    assert resp['Balance'] == '14637.74'
