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
  
    #s_YYYY,s_MM,s_DD,s_hh,s_mm,s_ss = input("input start time (YYYY/MM/DD/hh/mm/ss): ").split() 
    #e_YYYY,e_MM,e_DD,e_hh,e_mm,e_ss = input("input end time (YYYY/MM/DD/hh/mm/ss): ").split()
    
    #s_timeStr = "{}-{}-{} {}:{}:{}".format(s_YYYY, s_MM, s_DD, s_hh, s_mm, s_ss)
    #e_timeStr = "{}-{}-{} {}:{}:{}".format(e_YYYY, e_MM, e_DD, e_hh, e_mm, e_ss)
    
    #s_timeAry = time.strptime(s_timeStr, "%Y-%m-%d %H:%M:%S")
    s_timeStamp = int(argv_list[2])
    #e_timeAry = time.strptime(e_timeStr, "%Y-%m-%d %H:%M:%S")
    e_timeStamp = int(argv_list[3])
    
    
    #print("[{}] to [{}]".format(s_timeStamp, e_timeStamp))
    print("data range: [{}] to [{}]".format(s_timeStamp, e_timeStamp))
    
  else:
    print("select all data...")
  
  
  
  #check_ip_range = input("filter IP? (y/n): ")
  check_ip_range = argv_list[4]
  
  srcIp_specify = "n.n.n.n"
  desIp_specify = "n.n.n.n"
  if check_ip_range == "y":
  
    srcIp_specify = argv_list[5]  #ip_specify
    desIp_specify = argv_list[6]
    srcIp_specify = srcIp_specify.split(".")
    desIp_specify = desIp_specify.split(".")
    
  else:
    srcIp_specify = srcIp_specify.split(".")
    desIp_specify = desIp_specify.split(".")
    print("select all ip...")
  
  
  
  #----data processing
  print("data processing...")
  s = time.time()
  
  record_date_list = list()
  record_date_list_hr = list()
  record_conBytes_list = list()
  
  record_ip_list = list()
  record_conFK_list = list()
  
  
  #db.inventory.find( { qty: { $in: [ 5, 15 ] } } )
  
  cursor_con = mycol_connection.find()
  for single_con in cursor_con:
  
    #if check_data_range == "y":        #filter data time
      #if not (single_con["Start_Time"]>=s_timeStamp and single_con["Start_Time"]<=e_timeStamp) :
        #continue
    
    if single_con["Connection_Type"] == 2054:  #filter ARP
      continue
  
    ipA = single_con["Source_IP"]
    ipB = single_con["Destination_IP"]
    conFK = single_con["Foreign_Key"]
    
    
    if not (isinstance(ipA,int)):  #check ipv6 and ipv4
      ipA = "{}:{}:{}:{}:{}:{}:{}:{}".format(ipA[0:4],ipA[4:8],ipA[8:12],ipA[12:16],ipA[16:20],ipA[20:24],ipA[24:28],ipA[28:32])
      if (ipA == "0000:0000:0000:0000:0000:0000:0000:0000"):
        continue
      continue
    else:
      ipA = long2ip(ipA)
      A_filter_ck = ipA.split(".")
      if (A_filter_ck[3] == "255" or ipA == "0.0.0.0"):          #remove broadcast
        continue
      elif (int(A_filter_ck[0]) >= 224 and int(A_filter_ck[0]) <= 239):  #remove Multicast
        continue

    if not (isinstance(ipB,int)):  #check ipv6 and ipv4
      ipB = "{}:{}:{}:{}:{}:{}:{}:{}".format(ipB[0:4],ipB[4:8],ipB[8:12],ipB[12:16],ipB[16:20],ipB[20:24],ipB[24:28],ipB[28:32])
      continue
    else:
      ipB = long2ip(ipB)
      B_filter_ck = ipB.split(".")
      if (B_filter_ck[3] == "255" or ipB == "0.0.0.0"):          #remove broadcast & Multicast
        continue
      elif (int(B_filter_ck[0]) >= 224 and int(B_filter_ck[0]) <= 239):  #remove Multicast
        continue


    ip_ary_a = ipA.split(".")
    ip_ary_b = ipB.split(".")
    
    
    if (srcIp_specify[0] != "n" and ip_ary_a[0] != srcIp_specify[0]):          #filter ip
      continue
    elif (srcIp_specify[1] != "n" and ip_ary_a[1] != srcIp_specify[1]):
      continue
    elif (srcIp_specify[2] != "n" and ip_ary_a[2] != srcIp_specify[2]):
      continue
    elif (srcIp_specify[3] != "n" and ip_ary_a[3] != srcIp_specify[3]):
      continue


    if (desIp_specify[0] != "n" and ip_ary_b[0] != desIp_specify[0]):          #filter ip
      continue
    elif (desIp_specify[1] != "n" and ip_ary_b[1] != desIp_specify[1]):
      continue
    elif (desIp_specify[2] != "n" and ip_ary_b[2] != desIp_specify[2]):
      continue
    elif (desIp_specify[3] != "n" and ip_ary_b[3] != desIp_specify[3]):
      continue



    dataCon_day = dt.datetime.utcfromtimestamp(single_con["Start_Time"]).strftime("%Y-%m-%d")
    try:
        date_index = record_date_list.index(dataCon_day)
        record_conBytes_list[date_index] += (single_con["A2Bbytes"]+single_con["B2Abytes"])
    except ValueError:
        record_date_list.append(dataCon_day)
        record_conBytes_list.append(single_con["A2Bbytes"]+single_con["B2Abytes"])


    dataCon_hr = dt.datetime.utcfromtimestamp(single_con["Start_Time"]).strftime("%Y-%m-%dT%H")
    try:
        date_index = record_date_list_hr.index(dataCon_hr)
        record_conBytes_list[date_index] += (single_con["A2Bbytes"]+single_con["B2Abytes"])
    except ValueError:
        record_date_list_hr.append(dataCon_hr)
        record_conBytes_list.append(single_con["A2Bbytes"]+single_con["B2Abytes"])
    
    #print(packet) #cursor_packet end
    
    
    
  for i in range(len(record_date_list)):
    print(record_date_list[i])
    print(record_conBytes_list[i])


  #write data
  fdata_d3js = open("./data/d3js_calendar_conNum.json", "w")

  week_str = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]  #week string

  
  fdata_d3js.write("[\n")

  check_loop_first = 1
  for i in range(len(record_date_list)):      #node
  
    if(check_loop_first == 1):
      fdata_d3js.write("\t{\n\t\t\"date\": \"")
      fdata_d3js.write(record_date_list[i])
      fdata_d3js.write("\",")
      check_loop_first = 0
    else:
      fdata_d3js.write(",\n\t{\n\t\t\"date\": \"")
      fdata_d3js.write(record_date_list[i])
      fdata_d3js.write("\",")


    fdata_d3js.write("\n\t\t\"details\": [")
    for j in range(7):    #week details
      fdata_d3js.write("\n\t\t\t{")
      fdata_d3js.write("\n\t\t\t\t\"name\": \"")
      fdata_d3js.write(week_str[j])
      fdata_d3js.write("\",")
      fdata_d3js.write("\n\t\t\t\t\"date\": \"")
      fdata_d3js.write("null")
      fdata_d3js.write("\",")
      fdata_d3js.write("\n\t\t\t\t\"value\": ")
      fdata_d3js.write("0")
      fdata_d3js.write("\n\t\t\t}")
      if(j != 6):
        fdata_d3js.write(",")
    fdata_d3js.write("\n\t\t],")

    
    fdata_d3js.write("\n\t\t\"conBytes\": ")
    fdata_d3js.write(str(record_conBytes_list[i]))
    fdata_d3js.write(",")


    fdata_d3js.write("\n\t\t\"summary\": [")
    for j in range(7):    #week details
      fdata_d3js.write("\n\t\t\t{")
      fdata_d3js.write("\n\t\t\t\t\"name\": \"")
      fdata_d3js.write(week_str[j])
      fdata_d3js.write("\",")
      fdata_d3js.write("\n\t\t\t\t\"value\": ")
      fdata_d3js.write("0")
      fdata_d3js.write("\n\t\t\t}")
      if(j != 6):
        fdata_d3js.write(",")
    fdata_d3js.write("\n\t\t]")


    fdata_d3js.write("\n\t}")

  fdata_d3js.write("\n]")


  
  e = time.time()

  print("con_Foreign_Key: {}".format(len(record_conFK_list)))
  
  
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

