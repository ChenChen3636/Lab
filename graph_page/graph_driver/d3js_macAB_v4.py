#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 17 sec
#v3 update: node src&des bigger
#v4 for web input

from py2neo import *
import pymongo
import json
from bson.json_util import loads, dumps
import time
import numpy as np

import os
import uuid
import sys



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
  
  argv_list = sys.argv  #main input    format: check_data_range(y/n), rangeA, rangeB, ip_filter(y/n)
  
  
  #check_data_range = input("To select a range? (y/n): ")
  check_data_range = argv_list[1]
  
  timeRange_str = "null"

  if check_data_range == "y":
  

    s_timeStamp = int(argv_list[2])
    e_timeStamp = int(argv_list[3])
    
    timeRange_str = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(s_timeStamp))
    timeRange_str += " ~ "
    timeRange_str += time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(e_timeStamp))
    
    #print("[{}] to [{}]".format(s_timeStamp, e_timeStamp))
    print("data range: [{}] to [{}]".format(s_timeStamp, e_timeStamp))
    
  else:
    print("select all data...")



  for i in range(len(argv_list)):     #get highlight mark value
    if(argv_list[i] == "-HL"):
      hl_value = int(argv_list[i+1])
      per_lab = int(argv_list[i+2])
  
  
  
  #----data processing
  s = time.time()
  print("data processing...")
  
  record_mac_list = list()
  record_conFK_list = list()
  
  record_relation_list = list()
  relation_field_conNum = list()
  
  node_field_desLinkNum = list()
  node_field_srcLinkNum = list()
  
  
  cursor_packet = mycol_packet.find({"$and": [ { "Arrival_Time": {"$gt": s_timeStamp} }, { "Arrival_Time": {"$lt": e_timeStamp} }]})
  for single_pkt in cursor_packet:
  
  
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



  #highlight_mark data


  highlight_mark = list()
  
  for i in range(len(record_mac_list)):
  
    curr_value = node_field_srcLinkNum[i]+node_field_desLinkNum[i]
  
    if(len(highlight_mark) <= hl_value):
      highlight_mark.append(curr_value)
      highlight_mark.sort()
    else:
      if(curr_value > highlight_mark[0]):
        highlight_mark[0] = curr_value
        highlight_mark.sort()
      else:
        continue


  text_show_index = int(len(highlight_mark) - (len(highlight_mark) * (per_lab/100)))


  #write data

  deleNode_list = list()       #save to bigger than the limit number of node

  fdata_d3js = open("./data/d3js_macAB.json", "w")
  
  fdata_d3js.write("{\n")

  fdata_d3js.write("\t\"dataTime\": \"")
  fdata_d3js.write(str(timeRange_str))
  fdata_d3js.write("\",\n")

  fdata_d3js.write("\t\"nodes\": [\n\t\t")



  check_loop_first = 1
  for i in range(len(record_mac_list)):      #node

    total_link = node_field_srcLinkNum[i]+node_field_desLinkNum[i]

    if(total_link >= highlight_mark[0]):

      if(total_link >= highlight_mark[text_show_index]):
        node_json = json.dumps({"mac_addr": record_mac_list[i], "src_link_num": node_field_srcLinkNum[i], "des_link_num": node_field_desLinkNum[i], "highlight": 1})
      else:
        node_json = json.dumps({"mac_addr": record_mac_list[i], "src_link_num": node_field_srcLinkNum[i], "des_link_num": node_field_desLinkNum[i], "highlight": 0})

    else:
      deleNode_list.append(record_mac_list[i])
      continue



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

    try:                                        #delete no node rel
      deleNode_list.index(rel_json["source"])
      continue
    except ValueError:
      process = "pass"

    try:
      deleNode_list.index(rel_json["target"])
      continue
    except ValueError:
      process = "pass"

    rel_json = json.dumps({"source": rel_json["source"], "target": rel_json["target"], "type": "Connection_mac", "con_num": relation_field_conNum[i]})

    
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


