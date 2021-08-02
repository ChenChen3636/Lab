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
  record_flow_list = list()
  record_pktNum_list = list()
  


  #total flow
  cursor_con = mycol_connection.find({"$and": [ { "Start_Time": {"$gt": s_timeStamp} }, { "Start_Time": {"$lt": e_timeStamp} }]})
  for single_con in cursor_con:

    if(single_con["Connection_Type"] == 6):                        #TCP
      
      data_time = dt.datetime.utcfromtimestamp(single_con["Start_Time"]).strftime("%Y-%m-%dT%H:%M")
      try:
        date_index = record_date_list.index(data_time)
        record_flow_list[date_index] += (single_con["A2Bbytes"]+single_con["B2Abytes"])
        record_pktNum_list[date_index] += (single_con["A2Bpacket"]+single_con["B2Apacket"])
      except ValueError:
        record_date_list.append(data_time)
        record_flow_list.append(single_con["A2Bbytes"]+single_con["B2Abytes"])
        record_pktNum_list.append(single_con["A2Bpacket"]+single_con["B2Apacket"])

    
    
  for i in range(len(record_date_list)):
    print("time: {}, data: {}, pkt_num: {}".format(record_date_list[i], record_flow_list[i], record_pktNum_list[i]))




  #write data
  fdata_d3js = open("./data/d3js_TCPthroughput.csv", "w")

  fdata_d3js.write("date,throughput,pkt_num\n")

  for i in range(len(record_date_list)):
    fdata_d3js.write(str(record_date_list[i]))
    fdata_d3js.write(",")
    fdata_d3js.write(str(record_flow_list[i]))
    fdata_d3js.write(",")
    fdata_d3js.write(str(record_pktNum_list[i]))
    fdata_d3js.write("\n")


  fdata_d3js.close()
  

  e = time.time()

  
  
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

