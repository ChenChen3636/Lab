#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 7 sec
# v4: ip filter
# v6: web input

from py2neo import *
import pymongo
import json
from bson.json_util import loads, dumps
import time
import numpy as np

import os
import uuid
import socket, struct
import sys
import datetime as dt

def long2ip(longip):
  return socket.inet_ntoa(struct.pack('!L', longip))

def main():

  #mongodb address
  myclient = pymongo.MongoClient("mongodb://localhost:27017/")
  #database name
  mydb = "cgudb"
  
  #neo4j address
  #sys_graph = Graph("http://120.126.16.21:7474/db/data/", user="neo4j", password="123456")
  #authenticate("http://120.126.16.21:7474", "neo4j", "123456")
  #sys_graph = Graph("http://120.126.16.21:7474/db/data/")
  

  #---

  dblist = myclient.list_database_names()
  if mydb in dblist:
    print("connecting with \"{}\"".format(mydb))
    mydb = myclient[mydb]
    mycol_packet = mydb["packet_ary_collection"]
    mycol_connection = mydb["connection_collection"]
  else:
    print("database dose'n exist.")
    return 0
  
  
  #---- data time range  input processing data range
  
  
  
  argv_list = sys.argv  #main input    format(check_data_range(y/n), rangeA, rangeB, ip_filter(y/n))
  

  #check_data_range = input("To select a range? (y/n): ")
  check_data_range = argv_list[1]
  
  if check_data_range == "y":  

    s_timeStamp = int(argv_list[2])
    e_timeStamp = int(argv_list[3])
    
    
    print("data range: [{}] to [{}]".format(s_timeStamp, e_timeStamp))
    
  else:
    print("select all data...")
  
  
  
  #----data processing
  print("data processing...")
  s = time.time()
  
  record_date_list = list()
  record_date_list_hr = list()
  record_conBytes_list = list()
  
  record_ip_list = list()

  #layer 2
  ethernet_flow = 0

  #layer 3
  ipv4_flow = 0
  ipv6_flow = 0
  arp_flow = 0

  #layer 4
  tcp_flow = 0
  udp_fow = 0

  #layer 7
  http_flow = 0
  https_flow = 0

  dns_flow = 0
  
  #total flow
  cursor_con = mycol_connection.find({"$and": [ { "Start_Time": {"$gt": s_timeStamp} }, { "Start_Time": {"$lt": e_timeStamp} }]})
  for single_con in cursor_con:

    ethernet_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]  #ethernet

    if(single_con["Connection_Type"] == 2054):                      #ARP
      arp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]
      continue

    if(isinstance(single_con["Source_IP"],int)):                    #ipv4
      ipv4_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

      if(single_con["Connection_Type"] == 6):                       #TCP
        tcp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con["Source_Port"] == 80 or single_con["Destination_Port"] == 80):   #http
          http_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con["Source_Port"] == 443 or single_con["Destination_Port"] == 443):   #https
          https_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


      if(single_con["Connection_Type"] == 17):                       #UDP
        udp_fow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con["Source_Port"] == 53 or single_con["Destination_Port"] == 53):  #DNS
          dns_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]
      
    else:                                                           #ipv6
      ipv4_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

    
    
    
  print("[layer 2]Ethernet: {}".format(ethernet_flow))
  print()

  print("[layer 3]Ethernet->ARP: {}".format(arp_flow))
  print("[layer 3]Ethernet->IPv4: {}".format(ipv4_flow))
  print("[layer 3]Ethernet->IPv6: {}".format(ipv6_flow))
  print()

  print("[layer 4]Ethernet->IPv4->TCP: {}".format(tcp_flow))
  print("[layer 4]Ethernet->IPv4->UDP: {}".format(udp_fow))
  print()

  print("[layer 7]Ethernet->IPv4->TCP->http: {}".format(http_flow))
  print("[layer 7]Ethernet->IPv4->TCP->https: {}".format(https_flow))
  print("[layer 7]Ethernet->IPv4->UDP->DNS: {}".format(dns_flow))



  udp_others = udp_fow - dns_flow
  tcp_others = tcp_flow - https_flow - http_flow

  ipv4_others = ipv4_flow - udp_fow - tcp_flow

  ethernet_others = ethernet_flow - ipv4_flow - ipv6_flow - arp_flow

  print("ethernet_others: {}".format(ethernet_others))
  print("ipv4_others: {}".format(ipv4_others))
  print("udp_others: {}".format(udp_others))
  print("tcp_others: {}".format(tcp_others))


  #return


  #write data
  fdata_d3js = open("./d3js_protocol_hierarchy.json", "w")


  json_str = {
      "name": "Frame",
      "children": [
          {
              "name": "Ethernet",
              "children": [
                  {
                      "name": "Internet Protocol Version 4",
                      "children": [
                          {
                              "name": "User Datagram Protocol",
                              "children": [
                                  {"name": "Domain Name System", "size": dns_flow},
                                  {"name": "others", "size": udp_others}
                              ]
                          },
                          {
                              "name": "Transmission Control Protocol",
                              "children": [
                                  {"name": "Secure Sockets Layer", "size": https_flow},
                                  {"name": "Hypertext Transfer Protocol", "size": http_flow},
                                  {"name": "others", "size": tcp_others}
                              ]
                          },
                          {"name": "others", "size": ipv4_others}
                      ]
                  },
                  {
                      "name": "Internet Protocol Version 6", 
                      "size": ipv6_flow
                  },
                  {
                      "name": "Address Resolution Protocol",
                      "size": arp_flow
                  }
              ]
          }
      ]
  }

  json_str = json.dumps(json_str)
  #json_str = json.loads(json_str)

  fdata_d3js.write(str(json_str))



  
  e = time.time()


  fdata_d3js.close()
  
  
  
  print("during times: {:.4f}(s)".format(e-s))
  print("complete!")
  
  return
  #----test end



if __name__ == '__main__':

  #s = time.time()
  main()
  #e = time.time()
  
  #print("during times: {:.4f}(s)".format(e-s))
  #print("complete!")

