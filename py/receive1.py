#!/usr/bin/env python
import pika
import time

connection = pika.BlockingConnection(pika.ConnectionParameters(
    host='localhost')) #1.build tcp
channel = connection.channel() #2.buildchannel

channel.queue_declare(queue='task_queue', durable=True) #3.build queue

#4.build message subscribe
print '[*]Waiting for message...'

def callback(ch, method, properties, body):
    print "[x]Received %r" % (body,)
    time.sleep(body.count('.')) #logic
    print "[x]Done"
    ch.basic_ack(delivery_tag = method.delivery_tag)

channel.basic_consume(callback, queue='task_queue', no_ack=False) #round-robin,acknowledgment default

channel.start_consuming()