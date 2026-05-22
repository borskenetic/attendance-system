import serial
import time
import sys
import json

PORT = "COM1"
BAUD = 9600

def send_command(ser, command, delay=1):
    ser.write((command + "\r").encode())
    time.sleep(delay)

def send_sms(ser, number, text):

    send_command(ser, "AT")
    send_command(ser, "AT+CMGF=1")

    ser.write(f'AT+CMGS="{number}"\r'.encode())
    time.sleep(1)

    ser.write(text.encode())
    ser.write(b"\x1A")

    time.sleep(5)

data = json.loads(sys.argv[1])

ser = serial.Serial(PORT, BAUD, timeout=1)
time.sleep(2)

for item in data:

    number = item["number"]
    message = item["message"]

    print("Sending to", number)

    send_sms(ser, number, message)

    time.sleep(3)

ser.close()

print("DONE")