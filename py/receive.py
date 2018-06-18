#!/usr/bin/env python
import pika

connection = pika.BlockingConnection(pika.ConnectionParameters(
    host='localhost')) #1.build tcp
channel = connection.channel() #2.buildchannel

channel.queue_declare(queue='hello') #3.build queue

#4.build message subscribe
print '[*]Waiting for message...'

def callback(ch, method, properties, body):
    print "[x]Received %r" % (body,)

channel.basic_consume(callback, queue='hello', no_ack=True) #iteration

channel.start_consuming()