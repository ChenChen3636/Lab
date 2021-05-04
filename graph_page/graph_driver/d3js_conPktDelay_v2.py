#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 7 sec

from py2neo import *
import pymongo
import json
from bson.json_util import loads, dumps
import time
import numpy as np
import math

import os
import uuid
import socket, struct


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
  
  check_data_range = input("To select a range? (y/n): ")
  
  if check_data_range == "y":
  
    s_YYYY,s_MM,s_DD,s_hh,s_mm,s_ss = input("input start time (YYYY/MM/DD/hh/mm/ss): ").split() 
    e_YYYY,e_MM,e_DD,e_hh,e_mm,e_ss = input("input end time (YYYY/MM/DD/hh/mm/ss): ").split()
    
    s_timeStr = "{}-{}-{} {}:{}:{}".format(s_YYYY, s_MM, s_DD, s_hh, s_mm, s_ss)
    e_timeStr = "{}-{}-{} {}:{}:{}".format(e_YYYY, e_MM, e_DD, e_hh, e_mm, e_ss)
    
    s_timeAry = time.strptime(s_timeStr, "%Y-%m-%d %H:%M:%S")
    s_timeStamp = int(time.mktime(s_timeAry))
    e_timeAry = time.strptime(e_timeStr, "%Y-%m-%d %H:%M:%S")
    e_timeStamp = int(time.mktime(e_timeAry))
    
    
    #print("[{}] to [{}]".format(s_timeStamp, e_timeStamp))
    print("data range: [{}] to [{}]".format(s_timeStr, e_timeStr))
    
  else:
    print("select all data...")
  
  
  
  #----data processing
  print("data processing...")
  s = time.time()
  
  record_srcIP_list = list()      #node
  record_avgDelay_list = list()
  record_conFK_list = list()
  record_timeRange_list = list()  #node
  
  record_relation_list = list()    #link
  relation_field_conNum = list()
  
  node_field_linkNum = list()
  
  max_delay_time = 0
  
  #db.inventory.find( { qty: { $in: [ 5, 15 ] } } )
  
  cursor_con = mycol_connection.find({"A2Bpacket":{"$exists":"true"}, "B2Apacket":{"$exists":"true"}})
  for single_con in cursor_con:
  
    if check_data_range == "y":        #filter data time
      if not (single_con["Start_Time"]>=s_timeStamp and single_con["Start_Time"]<=e_timeStamp) :
        continue
  
    conFK = single_con["Foreign_Key"]
    con_srcIP = single_con["Source_IP"]
    con_dur = single_con["Connection_Duration"]
    con_A2Bpkt = single_con["A2Bpacket"]
    con_B2Apkt = single_con["B2Apacket"]
    
    con_avgDelay = 0
    if not ((con_A2Bpkt+con_B2Apkt) == 0):
      con_avgDelay = con_dur/(con_A2Bpkt+con_B2Apkt)
    else:
      continue
    
    
    if not (isinstance(con_srcIP,int)):  #check ipv6 and ipv4 
      con_srcIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(con_srcIP[0:4],con_srcIP[4:8],con_srcIP[8:12],con_srcIP[12:16],con_srcIP[16:20],con_srcIP[20:24],con_srcIP[24:28],con_srcIP[28:32])
      if (con_srcIP == "0000:0000:0000:0000:0000:0000:0000:0000"):
        continue
      continue
    else:
      con_srcIP = long2ip(con_srcIP)
      filter_ck = con_srcIP.split(".")
      if (filter_ck[3] == "255" and filter_ck == "0.0.0.0"):          #remove broadcast
        continue
      elif (int(filter_ck[0]) >= 224 and int(filter_ck[0]) <= 239):  #remove Multicast
        continue
    
    record_conFK_list.append(conFK)
    record_avgDelay_list.append(con_avgDelay)
    
    con_timeRange = math.floor(con_avgDelay/5)
    
    if((con_timeRange+1)*5 > max_delay_time):
      max_delay_time = (con_timeRange+1)*5

    cursor_rel = json.dumps({"source": con_srcIP, "target": (con_timeRange+1)*5, "type": "Connection_FK"})
    
    #print(cursor_rel)
    
    
    try:
      record_srcIP_list.index(con_srcIP)
    except ValueError:
      record_srcIP_list.append(con_srcIP)
    
    
    try:
      rel_index = record_relation_list.index(cursor_rel)
      relation_field_conNum[rel_index] += 1
    except ValueError:
      record_relation_list.append(cursor_rel)
      relation_field_conNum.append(1)
    
    
    
    #print(packet) #cursor_packet end


  for i in range(math.floor(max_delay_time/5)):
    record_timeRange_list.append((i+1)*5)
    node_field_linkNum.append(0)


  for i in range(len(record_timeRange_list)):        #calculate node link number
    for j in range(len(record_relation_list)):
      cursor_rel = json.loads(record_relation_list[j])
      if(cursor_rel["target"] == record_timeRange_list[i]):
        node_field_linkNum[i] += relation_field_conNum[j]



  #write data
  fdata_d3js = open("./data/d3js_conPktDelay.json", "w")
  
  fdata_d3js.write("{\n\t\"nodes\": [\n\t\t")


  check_loop_first = 1
  for single_node in record_srcIP_list:          #node
    node_json = json.dumps({"node_attr": single_node, "group": 1})
    #node_json = json.loads(node_json)    #single quote
    
    if check_loop_first == 1:
      fdata_d3js.write(str(node_json))
      print(node_json)
      check_loop_first = 0
      continue
      
    fdata_d3js.write(",")
    fdata_d3js.write("\n\t\t")
    fdata_d3js.write(str(node_json))
    print(node_json)

  fdata_d3js.write(",")
  fdata_d3js.write("\n\t\t")

  check_loop_first = 1
  for i in range(len(record_timeRange_list)):          #node time range
    node_json = json.dumps({"node_attr": record_timeRange_list[i], "link_num": node_field_linkNum[i], "group": 2})
    #node_json = json.loads(node_json)    #single quote
    
    if check_loop_first == 1:
      fdata_d3js.write(str(node_json))
      print(node_json)
      check_loop_first = 0
      continue
      
    fdata_d3js.write(",")
    fdata_d3js.write("\n\t\t")
    fdata_d3js.write(str(node_json))
    print(node_json)
    
  
  fdata_d3js.write("\n\t],\n\t\"links\": [\n\t\t")
  
  check_loop_first = 1
  for i in range(len(record_relation_list)):          #rel
    rel_json = json.loads(record_relation_list[i])
    rel_json = json.dumps({"source": rel_json["source"], "target": rel_json["target"], "type": "Connection_FK", "con_num": relation_field_conNum[i]})
    #rel_json = json.loads(rel_json)    #single quote
    
    if check_loop_first == 1:
      fdata_d3js.write(str(rel_json))
      print(rel_json)
      check_loop_first = 0
      continue

    
    fdata_d3js.write(",")
    fdata_d3js.write("\n\t\t")
    fdata_d3js.write(str(rel_json))
    print(rel_json)

  fdata_d3js.write("\n\t]\n}")
  
  e = time.time()

  print("node: {}".format(len(record_timeRange_list)))
  print("relationship: {}".format(len(record_relation_list)))
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
  #total times 1400

