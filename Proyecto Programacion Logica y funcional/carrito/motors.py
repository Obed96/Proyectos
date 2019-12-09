from __future__ import print_function
import RPi.GPIO as GPIO
from time import sleep
import time
from pyswip import Prolog, registerForeign, Functor
 
GPIO.setmode(GPIO.BOARD)
# motor 1 ####
Motor1A = 18
Motor1B = 16
Motor1E = 22
GPIO.setup(Motor1A,GPIO.OUT)
GPIO.setup(Motor1B,GPIO.OUT)
GPIO.setup(Motor1E,GPIO.OUT)
# motor 2 ###
Motor2A = 23
Motor2B = 21
Motor2E = 19
GPIO.setup(Motor2A,GPIO.OUT)
GPIO.setup(Motor2B,GPIO.OUT)
GPIO.setup(Motor2E,GPIO.OUT)
#pwmM2 = GPIO.PWM(19, 1000)
#pwmM2.start(100)
#pwmM2.ChangeDutyCycle(100)
#pwm.stop()
##########################################
#funciones para el control de los motores#
##########################################
def up(MotorA,MotorB, MotorE):
    GPIO.output(MotorA,GPIO.HIGH)
    GPIO.output(MotorB,GPIO.LOW)
    GPIO.output(MotorE,GPIO.HIGH)   
def stop(MotorE):
    GPIO.output(MotorE,GPIO.LOW)
def right():
    stop(Motor2E)
    up(Motor1A,Motor1B, Motor1E)
    up(Motor2B,Motor2A, Motor2E)
def left():
    stop(Motor1E)
    up(Motor2A,Motor2B, Motor2E)
    up(Motor1B,Motor1A, Motor1E)
print ("Turning motor on")


print ("Turning motor on")
"""GPIO.output(Motor2A,GPIO.HIGH)
GPIO.output(Motor2B,GPIO.LOW)
GPIO.output(Motor2E,GPIO.HIGH)
print ("Stopping motor")
GPIO.output(Motor1E,GPIO.LOW)
GPIO.output(Motor2E,GPIO.LOW)
GPIO.cleanup()
"""
#######ultrasonic sensor####
###########################

#GPIO Mode (BOARD / BCM)
GPIO.setmode(GPIO.BOARD)
 
#set GPIO Pins for sensors
GPIO_TRIGGER = 7
GPIO_ECHO = 11
GPIO_TRIGGER1 = 8
GPIO_ECHO1 = 10
 
#set GPIO direction (IN / OUT)
GPIO.setup(GPIO_TRIGGER, GPIO.OUT)
GPIO.setup(GPIO_ECHO, GPIO.IN)
GPIO.setup(GPIO_TRIGGER1, GPIO.OUT)
GPIO.setup(GPIO_ECHO1, GPIO.IN)
 
def distance(GPIO_TRIGGER, GPIO_ECHO):
    # set Trigger to HIGH
    GPIO.output(GPIO_TRIGGER, GPIO.LOW)
    # set Trigger after 0.01ms to LOW
    sleep(0.00001)
    GPIO.output(GPIO_TRIGGER, GPIO.HIGH)
    time.sleep(0.00001)
    GPIO.output(GPIO_TRIGGER, GPIO.LOW)
    StartTime = time.time()
    StopTime = time.time()
    # save StartTime
    # print(GPIO.input(GPIO_ECHO))
    while GPIO.input(GPIO_ECHO) == 0:
        StartTime = time.time()
 
    # save time of arrival
    print(GPIO.input(GPIO_ECHO))
    while GPIO.input(GPIO_ECHO) == 1:
        StopTime = time.time()
    # time difference between start and arrival
    TimeElapsed = StopTime - StartTime
    # multiply with the sonic speed (34300 cm/s)
    # and divide by 2, because there and back
    distance = (TimeElapsed * 34300) / 2
 
    return distance
 
if __name__ == '__main__':
    try:
        while True:
            up(Motor1A,Motor1B, Motor1E)
            up(Motor2A,Motor2B, Motor2E)
            dist = distance(GPIO_TRIGGER, GPIO_ECHO)
            #GPIO.cleanup()
            dist1 = distance(GPIO_TRIGGER1, GPIO_ECHO1)
            
            #print(dist1)
            print ("Measured Distance0 = %.1f cm" % dist)
            print ("Measured Distance1 = %.1f cm" % dist1)
            
            #sleep(1)
            
            prolog = Prolog()
            prolog.consult("base.pl")
            if dist <= 10:
                s1 = 0
            else:
                s1 = 1
                
            if dist1 <= 10:
                s2 = 0
            else:
                s2 = 1
                #up(Motor1A,Motor1B, Motor1E)
                #up(Motor2A,Motor2B, Motor2E)
            move = ""
            for i in prolog.query("posicion("+str(s1)+","+str(s2)+",X)"):
                move = i["X"]
            print(move)
            if(move == "izquierda"):
                left()
            elif(move == "derecha"):
                #stop(Motor2E)
                right()
            elif(move == "frente"):
                up(Motor1A,Motor1B, Motor1E)
                up(Motor2A,Motor2B, Motor2E)

            sleep(1)
            
 
        # Reset by pressing CTRL + C
    except KeyboardInterrupt:
        print("Measurement stopped by User")
        GPIO.cleanup()
    GPIO.cleanup()
