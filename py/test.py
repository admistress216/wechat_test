#/usr/bin/env python

import urllib2

response = urllib2.Request.urlopen('http://www.baidu.com')
print(response.read().decode('utf-8'))