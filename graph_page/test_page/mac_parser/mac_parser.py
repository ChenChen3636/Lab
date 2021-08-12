#!/usr/bin/python3
# encoding: utf-8
 
import requests
import json
 
 
def mac_parser(mac_addr):

    oui_url = ("http://www.macvendorlookup.com/api/v2/" + mac_addr) 

    try:
        response_json = requests.get(oui_url).json()
        print("mac address:" + mac_addr + ", country: " + response_json[0]["country"] + ", company: " + response_json[0]["company"] + ", addressL3: " + response_json[0]["addressL3"])
    except ValueError:
        print("not found...")
        return "not found"

    return response_json[0]["company"]


#### main
if __name__ == '__main__':
    print("query...")
    mac_parser("54a05086eb92")
    mac_parser("08:00:20")
    mac_parser("fc:d7:33")
    mac_parser("da:a1:19")
