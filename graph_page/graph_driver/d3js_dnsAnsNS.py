#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 17 sec

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
  
  
  
  #----data processing
  s = time.time()
  print("data processing...")
  
  record_conFK_list = list()
  record_srcIP_list = list()
  record_dnsIP_list = list()
  
  record_hostName_list = list()
  
  record_relation_list = list()
  relation_field_conNum = list()
  
  
  cursor_packet = mycol_packet.find({"Fourth_Layer.Fourth_Layer_Option.DNS_Info": {"$exists": "true"}})    #check dns query
  for single_pkt in cursor_packet:
  
    if not (single_pkt["Fourth_Layer"]["Destination_Port"] == 53):  #only go pkt
      continue
  
    if check_data_range == "y":        #filter data time
      if not (single_pkt["Arrival_Time"]>=s_timeStamp and single_pkt["Arrival_Time"]<=e_timeStamp) :
        continue
  

    conFK = single_pkt["Foreign_Key"]
    srcIP = single_pkt["Third_Layer"]["Source_IP"]
    dnsIP = single_pkt["Third_Layer"]["Destination_IP"]    #DNS server
    dns_query = single_pkt["Fourth_Layer"]["Fourth_Layer_Option"]["DNS_Info"]["Question"]
    
    
    if not (isinstance(srcIP,int)):  #check ipv6 and ipv4
      srcIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(srcIP[0:4],srcIP[4:8],srcIP[8:12],srcIP[12:16],srcIP[16:20],srcIP[20:24],srcIP[24:28],srcIP[28:32])
    else:
      srcIP = long2ip(srcIP)
      
    if not (isinstance(dnsIP,int)):  #check ipv6 and ipv4
      dnsIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(dnsIP[0:4],dnsIP[4:8],dnsIP[8:12],dnsIP[12:16],dnsIP[16:20],dnsIP[20:24],dnsIP[24:28],dnsIP[28:32])
    else:
      dnsIP = long2ip(dnsIP)
      
      
      
    #if macA=="ffffffffffff" or macB=="ffffffffffff":      #broadcast
      #continue
    
    

    hostName = "not found"
    for i in range(len(dns_query)):
    
      hostName = dns_query[i]["Name"]

      try:
        record_hostName_list.index(hostName)
      except ValueError:
        record_hostName_list.append(hostName)
    
    
    
    try:
      record_srcIP_list.index(srcIP)
    except ValueError:
      record_srcIP_list.append(srcIP)
 
    try:
      record_dnsIP_list.index(dnsIP)
    except ValueError:
      record_dnsIP_list.append(dnsIP)
      
      
    try:
      record_conFK_list.index(conFK)
    except ValueError:
      record_conFK_list.append(conFK)
      
      cursor_rel = json.dumps({"source": srcIP, "target": dnsIP, "type": "Connection_FK"})
    
      #print(cursor_rel)
      
      try:
        rel_index = record_relation_list.index(cursor_rel)
        relation_field_conNum[rel_index] += 1
      except ValueError:

        record_relation_list.append(cursor_rel)
        relation_field_conNum.append(1)
        
    #dns go end
    
#---------------------------dns back

  record_answerIP_list = list()

  cursor_packet = mycol_packet.find({"Fourth_Layer.Fourth_Layer_Option.DNS_Info": {"$exists": "true"}})    #check dns  back
  for single_pkt in cursor_packet:
    
    if not (single_pkt["Fourth_Layer"]["Source_Port"] == 53):  #filter go pkt
      continue
  
    if check_data_range == "y":        #filter data time
      if not (single_pkt["Arrival_Time"]>=s_timeStamp and single_pkt["Arrival_Time"]<=e_timeStamp) :
        continue
  
    conFK = single_pkt["Foreign_Key"]
    dnsIP = single_pkt["Third_Layer"]["Source_IP"]        #DNS server
    srcIP = single_pkt["Third_Layer"]["Destination_IP"]    
    answer_records_ary = single_pkt["Fourth_Layer"]["Fourth_Layer_Option"]["DNS_Info"]["Answer_Resource_Records"]
    
    
    if not (isinstance(srcIP,int)):  #check ipv6 and ipv4
      srcIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(srcIP[0:4],srcIP[4:8],srcIP[8:12],srcIP[12:16],srcIP[16:20],srcIP[20:24],srcIP[24:28],srcIP[28:32])
    else:
      srcIP = long2ip(srcIP)
      
    if not (isinstance(dnsIP,int)):  #check ipv6 and ipv4
      dnsIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(dnsIP[0:4],dnsIP[4:8],dnsIP[8:12],dnsIP[12:16],dnsIP[16:20],dnsIP[20:24],dnsIP[24:28],dnsIP[28:32])
    else:
      dnsIP = long2ip(dnsIP)
      
      
      
    #if macA=="ffffffffffff" or macB=="ffffffffffff":      #broadcast
      #continue
    
    
    
    try:
      record_srcIP_list.index(srcIP)
    except ValueError:
      record_srcIP_list.append(srcIP)
 
    try:
      record_dnsIP_list.index(dnsIP)
    except ValueError:
      record_dnsIP_list.append(dnsIP)
      
    
    answerIP = "not found"
    hostName = "not found"
    for i in range(len(answer_records_ary)):
    
      if(answer_records_ary[i]["Type"] == 1):
        hostName = answer_records_ary[i]["Name"]
        answerIP = long2ip(answer_records_ary[i]["Answer"])
        try:
          record_answerIP_list.index(hostName)
        except ValueError:
          record_answerIP_list.append(hostName)
          
        break
          
      elif(answer_records_ary[i]["Type"] == 28):
        
        hostName = answer_records_ary[i]["Name"]
        answerIP = answer_records_ary[i]["Answer"]
        answerIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(answerIP[0:4],answerIP[4:8],answerIP[8:12],answerIP[12:16],answerIP[16:20],answerIP[20:24],answerIP[24:28],answerIP[28:32])
        try:
          record_answerIP_list.index(hostName)
        except ValueError:
          record_answerIP_list.append(hostName)

        break
    
    
      
    try:
      record_conFK_list.index(conFK)
    except ValueError:
      record_conFK_list.append(conFK)
      
      
      cursor_rel = json.dumps({"source": srcIP, "target": hostName, "type": "Connection_FK"})
    
      #print(cursor_rel)
      
      try:
        rel_index = record_relation_list.index(cursor_rel)
        relation_field_conNum[rel_index] += 1
      except ValueError:
        record_relation_list.append(cursor_rel)
        relation_field_conNum.append(1)
        
    #dns back end


#---------------------------


  #write data
  fdata_d3js = open("./data/d3js_dnsAnsNS.json", "w")
  
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
  for single_node in record_dnsIP_list:          #node  dnsIP
    node_json = json.dumps({"node_attr": single_node, "group": 2})
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
  for single_node in record_answerIP_list:          #node  hostName
    node_json = json.dumps({"node_attr": single_node, "group": 3})
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
    
  
  
  fdata_d3js.write(",\n\t\t{\"node_attr\": \"not found\", \"group\": 4}")
  
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
  
  print("node: {}".format(len(record_srcIP_list)+len(record_dnsIP_list)+len(record_hostName_list)))
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

