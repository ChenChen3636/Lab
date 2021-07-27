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
  

  record_Type_list = list()
  record_udpPort_list = list()
  record_udpPort_num_list = list()
  

  #layer 2
  ethernet_flow = 0

  #layer 3
  ipv4_flow = 0
  ipv6_flow = 0
  arp_flow = 0
  icmp_flow = 0

  #layer 4
  tcp_flow = 0
  udp_fow = 0

  #layer 7
  http_flow = 0         #port 80
  https_flow = 0        #port 443

  tcp_smtp_flow = 0     #port 25
  tcp_smtpSSL_flow = 0  #port 465
  tcp_pop3_flow = 0          #port 110
  tcp_pop3SSL_flow = 0       #port 995

  udp_dns_flow = 0      #port 53
  udp_dhcp_flow = 0     #port 67 68
  udp_quic_flow = 0     #port 443 80
  udp_syslog_flow = 0   #port 514
  udp_snmp_flow = 0     #port 161 162
  udp_llmnr_flow = 0    #port 5355
  udp_upnp_flow = 0     #port 1900
  udp_netbios_flow = 0  #port 137
  
  #total flow
  cursor_con = mycol_connection.find({"$and": [ { "Start_Time": {"$gt": s_timeStamp} }, { "Start_Time": {"$lt": e_timeStamp} }]})
  for single_con in cursor_con:


    try:
      record_Type_list.index(single_con["Connection_Type"])
    except ValueError:
      record_Type_list.append(single_con["Connection_Type"])


    ethernet_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]  #ethernet


    if(single_con["Connection_Type"] == 1):                         #ICMP
      icmp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]
      continue

    if(single_con["Connection_Type"] == 2054):                      #ARP
      arp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]
      continue

    if(isinstance(single_con["Source_IP"],int)):                    #ipv4
      ipv4_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

      if(single_con["Connection_Type"] == 6):                       #TCP
        tcp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con["Destination_Port"] == 80):   #http
          http_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con["Destination_Port"] == 443):   #https
          https_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con["Destination_Port"] == 25):   #SMTP
          tcp_smtp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con["Destination_Port"] == 465):   #SMTP SSL
          tcp_smtpSSL_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con["Destination_Port"] == 110):   #POP3
          tcp_pop3_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]
      
        if(single_con["Destination_Port"] == 995):   #POP3 SSL
          tcp_pop3SSL_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]



      if(single_con["Connection_Type"] == 17):                       #UDP
        udp_fow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        try:
          index = record_udpPort_list.index(single_con.get("Destination_Port"))
          record_udpPort_num_list[index] += 1
        except ValueError:
          record_udpPort_list.append(single_con.get("Destination_Port"))
          record_udpPort_num_list.append(0)


        if(single_con.get("Destination_Port") == 53):  #DNS
          udp_dns_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con.get("Destination_Port") == 67):    #DHCP
          udp_dhcp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con.get("Destination_Port") == 68):    #DHCP
          udp_dhcp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con.get("Destination_Port") == 443 or single_con.get("Destination_Port") == 80):    #QUIC
          udp_quic_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

        if(single_con.get("Destination_Port") == 514):                                #syslog
          udp_syslog_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con.get("Destination_Port") == 161 or single_con.get("Destination_Port") == 162):    #SNMP
          udp_snmp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con.get("Destination_Port") == 5355):                               #LLMNR
          udp_llmnr_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con.get("Destination_Port") == 1900):                               #UPnP
          udp_upnp_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]


        if(single_con.get("Destination_Port") == 137):                               #NetBIOS
          udp_netbios_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]



    else:                                                           #ipv6
      ipv4_flow += single_con["A2Bbytes"]+single_con["B2Abytes"]

    
  print("total etherType: {}".format(record_Type_list))
  print("total udp_port: {}".format(record_udpPort_list))
  print("total udp_num_port: {}".format(record_udpPort_num_list))

    
  print("[layer 2]Ethernet: {}".format(ethernet_flow))
  print()

  print("[layer 3]Ethernet->ARP: {}".format(arp_flow))
  print("[layer 3]Ethernet->IPv4: {}".format(ipv4_flow))
  print("[layer 3]Ethernet->IPv6: {}".format(ipv6_flow))
  print("[layer 3]Ethernet->ICMP: {}".format(icmp_flow))
  print()

  print("[layer 4]Ethernet->IPv4->TCP: {}".format(tcp_flow))
  print("[layer 4]Ethernet->IPv4->UDP: {}".format(udp_fow))
  print()

  print("[layer 7]Ethernet->IPv4->TCP->http: {}".format(http_flow))
  print("[layer 7]Ethernet->IPv4->TCP->https: {}".format(https_flow))
  print("[layer 7]Ethernet->IPv4->TCP->SMTP: {}".format(tcp_smtp_flow))
  print("[layer 7]Ethernet->IPv4->TCP->SMTP SSL: {}".format(tcp_smtpSSL_flow))
  print("[layer 7]Ethernet->IPv4->TCP->POP3: {}".format(tcp_pop3_flow))
  print("[layer 7]Ethernet->IPv4->TCP->POP3 SSL: {}".format(tcp_pop3SSL_flow))


  print("[layer 7]Ethernet->IPv4->UDP->DNS: {}".format(udp_dns_flow))
  print("[layer 7]Ethernet->IPv4->UDP->DHCP: {}".format(udp_dhcp_flow)) 
  print("[layer 7]Ethernet->IPv4->UDP->QUIC: {}".format(udp_quic_flow)) 
  print("[layer 7]Ethernet->IPv4->UDP->syslog: {}".format(udp_syslog_flow))
  print("[layer 7]Ethernet->IPv4->UDP->SNMP: {}".format(udp_snmp_flow))
  print("[layer 7]Ethernet->IPv4->UDP->LLMNR: {}".format(udp_llmnr_flow))
  print("[layer 7]Ethernet->IPv4->UDP->UPnP: {}".format(udp_upnp_flow))
  print("[layer 7]Ethernet->IPv4->UDP->NetBIOS: {}".format(udp_netbios_flow))




  udp_others = udp_fow - udp_dns_flow - udp_dhcp_flow - udp_quic_flow - udp_syslog_flow - udp_snmp_flow - udp_llmnr_flow - udp_upnp_flow - udp_netbios_flow
  tcp_others = tcp_flow - https_flow - http_flow - tcp_smtp_flow - tcp_smtpSSL_flow - tcp_pop3_flow - tcp_pop3SSL_flow

  ipv4_others = ipv4_flow - udp_fow - tcp_flow

  ethernet_others = ethernet_flow - ipv4_flow - ipv6_flow - arp_flow -icmp_flow

  print("ethernet_others: {}".format(ethernet_others))
  print("ipv4_others: {}".format(ipv4_others))
  print("udp_others: {}".format(udp_others))
  print("tcp_others: {}".format(tcp_others))


  #return


  #write data
  fdata_d3js = open("./data/d3js_protocol_hierarchy.json", "w")


  json_str = {
      "name": "Frame",
      "children": [
          {
              "name": "Ethernet",
              "children": [
                  {
                      "name": "IPv4",
                      "children": [
                          {
                              "name": "UDP",
                              "children": [
                                  {"name": "DNS", "size": udp_dns_flow},
                                  {"name": "DHCP", "size": udp_dhcp_flow},
                                  {"name": "QUIC", "size": udp_quic_flow},
                                  {"name": "syslog", "size": udp_syslog_flow},
                                  {"name": "SNMP", "size": udp_snmp_flow},
                                  {"name": "LLMNR", "size": udp_llmnr_flow},
                                  {"name": "UPnP", "size": udp_upnp_flow},
                                  {"name": "NetBIOS", "size": udp_netbios_flow},
                                  {"name": "others", "size": udp_others}
                              ]
                          },
                          {
                              "name": "TCP",
                              "children": [
                                  {"name": "HTTPS", "size": https_flow},
                                  {"name": "HTTP", "size": http_flow},
                                  {"name": "SMTP", "size": tcp_smtp_flow},
                                  {"name": "SMTPS", "size": tcp_smtpSSL_flow},
                                  {"name": "POP3", "size": tcp_pop3_flow},
                                  {"name": "POP3 SSL", "size": tcp_pop3SSL_flow},
                                  {"name": "others", "size": tcp_others}
                              ]
                          },
                          {"name": "others", "size": ipv4_others}
                      ]
                  },
                  {
                      "name": "IPv6", 
                      "size": ipv6_flow
                  },
                  {
                      "name": "ARP",
                      "size": arp_flow
                  },
                  {
                      "name": "ICMP",
                      "size": icmp_flow
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

