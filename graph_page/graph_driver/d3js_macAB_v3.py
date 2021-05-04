#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 17 sec
#v3 update: node src&des bigger

from py2neo import *
import pymongo
import json
from bson.json_util import loads, dumps
import time
import numpy as np

import os
import uuid



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
  
  record_mac_list = list()
  record_conFK_list = list()
  
  record_relation_list = list()
  relation_field_conNum = list()
  
  node_field_desLinkNum = list()
  node_field_srcLinkNum = list()
  
  
  cursor_packet = mycol_packet.find({})
  for single_pkt in cursor_packet:
  
  
    if check_data_range == "y":        #filter data time
      if not (single_pkt["Arrival_Time"]>=s_timeStamp and single_pkt["Arrival_Time"]<=e_timeStamp) :
        continue
  
    macA = single_pkt["Second_Layer"]["Source_MAC"]
    macB = single_pkt["Second_Layer"]["Destination_MAC"]
    conFK = single_pkt["Foreign_Key"]
    
    if macA=="ffffffffffff" or macB=="ffffffffffff" or macA=="000000000000" or macB=="000000000000":      #broadcast
      continue
    
    
    try:
      record_conFK_list.index(conFK)
      continue
    except ValueError:
      record_conFK_list.append(conFK)
      
      macA_node = Node("device_mac", mac_addr=macA)
      macB_node = Node("device_mac", mac_addr=macB)
      #cursor_rel = "{}-[:Connection_mac]->{}".format(macA,macB)
      #cursor_rel = "source: \'{}\', target: \'{}\', type:'Connection_mac'".format(macA,macB)
      cursor_rel = json.dumps({"source": macA, "target": macB, "type": "Connection_mac"})
    
      #print(cursor_rel)
      
      try:
        rel_index = record_relation_list.index(cursor_rel)
        relation_field_conNum[rel_index] += 1
      except ValueError:
        record_relation_list.append(cursor_rel)
        relation_field_conNum.append(1)
    
    
    try:
      record_mac_list.index(macA)
    except ValueError:
      record_mac_list.append(macA)
 
    try:
      record_mac_list.index(macB)
    except ValueError:
      record_mac_list.append(macB)
      
        
    #print(packet) #cursor_packet end




  for i in range(len(record_mac_list)):        #calculate node link number (src&des bigger)
    node_field_desLinkNum.append(0)
    node_field_srcLinkNum.append(0)
    for j in range(len(record_relation_list)):
      cursor_rel = json.loads(record_relation_list[j])
      if(cursor_rel["target"] == record_mac_list[i]):
        node_field_desLinkNum[i] += relation_field_conNum[j]
      if(cursor_rel["source"] == record_mac_list[i]):
        node_field_srcLinkNum[i] += relation_field_conNum[j]



  #write data
  fdata_d3js = open("./data/d3js_macAB.json", "w")
  
  fdata_d3js.write("{\n\t\"nodes\": [\n\t\t")

  check_loop_first = 1
  for i in range(len(record_mac_list)):      #node
    node_json = json.dumps({"mac_addr": record_mac_list[i], "src_link_num": node_field_srcLinkNum[i], "des_link_num": node_field_desLinkNum[i]})
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
    rel_json = json.dumps({"source": rel_json["source"], "target": rel_json["target"], "type": "Connection_mac", "con_num": relation_field_conNum[i]})
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
  
  print("node: {}".format(len(record_mac_list)))
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


