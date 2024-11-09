import serial
import requests
import time

create_coupon_url = 'http://127.0.0.1:8000/voucher'


def main():
    while True:
        try:
            ser = serial.Serial('/dev/ttyACM0', 9600)
            print("Serial port opened successfully.")
            break
        except:
            print("Serial port not opened. Retrying in 5 seconds...")
            time.sleep(5)

    while True:
        line = ser.readline()
        line = line.decode('utf-8').strip()

        print(f"Received line: {line}")

        garbage_type = None
        if "metal" in line:
            garbage_type = "metal"
        elif "plastic" in line:
            garbage_type = "plastic"
        elif "water" in line:
            garbage_type = "water"
        elif "other" in line:
            garbage_type = "other"

        if garbage_type:
            print(f"Garbage type: {garbage_type}")
            response = requests.post(create_coupon_url, json={
                "type": garbage_type,
            })
            response_json = response.json()
            code = response_json["code"]
            ser.write(f"{code}\n".encode('utf-8'))


if __name__ == "__main__":
    main()
