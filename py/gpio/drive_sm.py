#!/usr/bin/env python
# coding=utf-8
# Stan 2016-01-17

# Bipolar Stepper Motor 42BYGHM809
# Step Angle (degrees) : 0.9
# 2-Phase
# Rated Voltage: 3V
# Rated Current: 1.7A/Phase
# 5mm Diameter Drive Shaft
# Holding Torque: 48N.cm
# NEMA 17 form factor

# Step accuracy: ±5%
# Ambient temperature range: 20°C ~ 50°C
# Insulation resistance: 100MΩ Min.50V DC
# Dielectric strength: 500V AC for 1 minute
# Step angle: 0.9°
# 400 steps / rev
# Voltage rating: 2.7 V
# Current rating: 1.68 A
# Resistance: 1.6 Ω per coil
# Inductance: 3.5 mH per coil
# Temperature rise tolerance: 80°C
# Holding torque: 4200 g⋅cm
# Detent torque: 260 g⋅cm
# Number of leads: 4
# Motor length : 4.8 cm
# Nema 17 form factor
# Weight:  340 g

# Step timing: 1,9 + 1,9 mkSec


import sys, time
import serial


portName = "COM5"

#Open port for communication	
serPort = serial.Serial(portName, 19200, timeout=1)

#Send the command

serPort.write("gpio set 6\r")

serPort.write("gpio clear 1\r")       # Not(Enable)
serPort.write("gpio clear 2\r")       # MS1
serPort.write("gpio clear 3\r")       # MS2
serPort.write("gpio clear 4\r")       # MS3

serPort.write("gpio set 2\r")         # MS1
# serPort.write("gpio set 3\r")         # MS2
# serPort.write("gpio set 4\r")         # MS3


serPort.write("gpio set 7\r")

for i in xrange(400*2):
    t1 = time.time()
    serPort.write("gpio set 5\r")
#   time.sleep(0.0000019) 
    t2 = time.time()
    serPort.write("gpio clear 5\r")
#   time.sleep(0.0000019) 
    t3 = time.time()

#   print "t1:", t2 - t1
#   print "t2:", t3 - t2

serPort.write("gpio clear 7\r")

	
#Close the port
serPort.close()