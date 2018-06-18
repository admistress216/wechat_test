import requests, random

def hehe(n,p):
    header={
        'User-Agent' : 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36',
        'Referer' : 'http://www.baidu.com',
    }
    data = {
        'u' : n,
        'p' : p
    }
    r = requests.post('http://composer.com/test.php', data, headers = header)
    print(r.content)

for i in range(0, 100):
    user = str(random.randint(123456789, 999999999));
    p = "".join(random.sample('1234567890abcdefghijklmnopqrstuvwxyz@#', 10))
    hehe(user,p)