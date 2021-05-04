#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 17 sec
#filter ip

from py2neo import *
import pymongo
import json
from bson.json_util import loads, dumps
import time
import numpy as np

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
  
  
  check_ip_range = input("filter IP? (y/n): ")
  
  ip_specify = "n.n.n.n"
  if check_ip_range == "y":
  
    ip_specify = input("xxx.xxx.xxx.xxx (0-255/n): ")
    ip_specify = ip_specify.split(".")
    
  else:
    ip_specify = ip_specify.split(".")
    print("select all ip...")
  
  
  
  #----data processing
  s = time.time()
  print("data processing...")
  
  record_srcIP_list = list()
  record_desPort_list = list()
  record_conFK_list = list()
  
  record_relation_list = list()
  relation_field_conNum = list()
  
  node_field_linkNum = list()
  
  cursor_packet = mycol_packet.find({"Fourth_Layer.Destination_Port": {"$exists": "true"}})    #check Fourth_Layer exist
  for single_pkt in cursor_packet:
  
  
    if check_data_range == "y":        #filter data time
      if not (single_pkt["Arrival_Time"]>=s_timeStamp and single_pkt["Arrival_Time"]<=e_timeStamp) :
        continue
  

    conFK = single_pkt["Foreign_Key"]
    srcIP = single_pkt["Third_Layer"]["Source_IP"]
    desPort = single_pkt["Fourth_Layer"]["Destination_Port"]
    
    
    if not (isinstance(srcIP,int)):  #check ipv6 and ipv4
      srcIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(srcIP[0:4],srcIP[4:8],srcIP[8:12],srcIP[12:16],srcIP[16:20],srcIP[20:24],srcIP[24:28],srcIP[28:32])
      if (srcIP == "0000:0000:0000:0000:0000:0000:0000:0000"):
        continue
    else:
      srcIP = long2ip(srcIP)
      filter_ck = srcIP.split(".")
      if filter_ck[3] == "255" or srcIP == "0.0.0.0":          #remove broadcast
        continue
      elif (int(filter_ck[0]) >= 224 and int(filter_ck[0]) <= 239):  #remove Multicast
        continue
    
    
    src_ip_ary = srcIP.split(".")
    if (ip_specify[0] != "n" and src_ip_ary[0] != ip_specify[0]):          #filter ip
      continue
    elif (ip_specify[1] != "n" and src_ip_ary[1] != ip_specify[1]):
      continue
    elif (ip_specify[2] != "n" and src_ip_ary[2] != ip_specify[2]):
      continue
    elif (ip_specify[3] != "n" and src_ip_ary[3] != ip_specify[3]):
      continue
    
    
    
    
      
    try:
      record_conFK_list.index(conFK)
      continue
    except ValueError:
      record_conFK_list.append(conFK)
      
      cursor_rel = json.dumps({"source": srcIP, "target": desPort, "type": "Connection_FK"})
    
      #print(cursor_rel)
      
      try:
        rel_index = record_relation_list.index(cursor_rel)
        relation_field_conNum[rel_index] += 1
      except ValueError:
        record_relation_list.append(cursor_rel)
        relation_field_conNum.append(1)
        
        
    try:
      record_srcIP_list.index(srcIP)
    except ValueError:
      record_srcIP_list.append(srcIP)
 
    try:
      record_desPort_list.index(desPort)
    except ValueError:
      record_desPort_list.append(desPort)
    
    
    #print(packet) #cursor_packet end



  for i in range(len(record_desPort_list)):        #calculate node link number
    node_field_linkNum.append(0)
    for j in range(len(record_relation_list)):
      cursor_rel = json.loads(record_relation_list[j])
      if(cursor_rel["target"] == record_desPort_list[i]):
        node_field_linkNum[i] += relation_field_conNum[j]


  
  #write data
  fdata_d3js = open("./data/d3js_desPort.json", "w")
  
  fdata_d3js.write("{\n\t\"nodes\": [\n\t\t")

  check_loop_first = 1
  for single_node in record_srcIP_list:          #node  srcIP
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
  for i in range(len(record_desPort_list)):          #node  desPort
    node_json = json.dumps({"node_attr": record_desPort_list[i], "link_num": node_field_linkNum[i], "group": 2})
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
  
  print("node: {}".format(len(record_srcIP_list)+len(record_desPort_list)))
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

