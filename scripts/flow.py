#!/usr/bin/python
#flowsensor.py
import RPi.GPIO as GPIO
import time, sys
import json
import datetime
import os

FLOW_SENSOR = 18
GPIO.setmode(GPIO.BCM)
GPIO.setup(FLOW_SENSOR, GPIO.IN)

global state
state = "none"

global count
count = 0

global lastCount
lastCount = 0

global flowLog
flowLog = "/var/www/fishpi/config/flow.log.json"

global daemonStatus
daemonStatus = "/var/www/fishpi/config/flow.daemon.json"

global flowStatus
flowStatus = "/var/www/fishpi/config/flow.status.json"

global flowLitresCount
flowLitresCount = "/var/www/fishpi/config/flow.litres.json"

#Counts a pulse and the epoch it was detected
def countPulse(channel):
	global state
	global count
	global lastCount

	if GPIO.input(channel) != state:
		state = GPIO.input(channel)
		count = count + 1
		if count > 1:
			setStatus("FLOW")
			lastCount = int(time.time())
			print "counting... " + str(count) + " at " + str(lastCount)
			setLitresCount(countToLitres(count))
		else:
			setLitresCount(0)
			setStatus("IDLE")

def setStatus(status):
	d = {}
        d["response"] = {}
	d["response"]["status"] = str(status)

       	with open(flowStatus, 'w') as f:
                json.dump(d,f)

def setLitresCount(litres):
        d = {}
        d["response"] = {}
        d["response"]["litres"] = str(litres)

        with open(flowLitresCount, 'w') as f:
                json.dump(d,f)


def countToLitres(count):
	return count * (0.4489164086687307)/1000

def save():

	global count
	global flowLog

	key = time.strftime("%Y%m%d")
	subKey = str(datetime.datetime.now().hour)

	if os.path.isfile(flowLog):
		f = open(flowLog, "r")
         	uCountConfig = f.read()
         	d = json.loads(uCountConfig)
      	else:
     	 	d = {}
		d["response"] = {}

	if not key in d["response"]:
		d["response"][key] = {}

	if not subKey in d["response"][key]:
		d["response"][key][subKey] = {}

	litres = countToLitres(count)

	if "count" in d["response"][key][subKey]:
		count = float(d["response"][key][subKey]["count"]) + count

        if "litres" in d["response"][key][subKey]:
		litres = float(d["response"][key][subKey]["litres"]) + litres

      	d["response"][key][subKey]["count"] = count
        d["response"][key][subKey]["litres"] = litres

	count = 0;

	print "Saved " + str(litres) + " Litres"

	with open(flowLog, 'w') as f:
		json.dump(d,f)

	print "Saved Status to IDLE"

	setStatus("IDLE")

GPIO.add_event_detect(FLOW_SENSOR, GPIO.BOTH, callback=countPulse)

while True:
    try:

	setStatus("IDLE")
        time.sleep(1)

	#Used to monitor heartbeat of daemon
	with open(daemonStatus, "w") as f:
		d = {"response":{"heartbeat": int(time.time())}}
		json.dump(d,f)

	if int(lastCount + 3) == int(time.time()):
		if(count > 3):
			save()
		else:
			count = 0

    except KeyboardInterrupt:
        print('\ncaught keyboard interrupt!')
        GPIO.cleanup()
        sys.exit()






