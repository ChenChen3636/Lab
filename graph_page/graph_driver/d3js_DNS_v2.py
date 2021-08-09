#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 17 sec
#v2 web input

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

    
  check_ip_range = argv_list[4]
  
  ip_specify = "n.n.n.n"
  if check_ip_range == "y":
  
    ip_specify = argv_list[5]
    ip_specify = ip_specify.split(".")
    
  else:
    ip_specify = ip_specify.split(".")
    print("select all ip...")
  
  
  
  #----data processing
  s = time.time()
  print("data processing...")
  
  record_conFK_list = list()
  record_srcIP_list = list()
  record_dnsIP_list = list()
  
  record_hostName_list = list()
  
  record_relation_list = list()
  relation_field_conNum = list()
  
  node_field_linkNum = list()
  
  
  cursor_packet = mycol_packet.find({"Fourth_Layer.Fourth_Layer_Option.DNS_Info.Question": {"$exists": "true"}, "Fourth_Layer.Destination_Port": 53, "$and": [ { "Arrival_Time": {"$gt": s_timeStamp} }, { "Arrival_Time": {"$lt": e_timeStamp} }]})    #check dns query
  
  for single_pkt in cursor_packet:
  
    #if not (single_pkt["Fourth_Layer"]["Destination_Port"] == 53):  #only go pkt
      #continue
  
    #if check_data_range == "y":        #filter data time
      #if not (single_pkt["Arrival_Time"]>=s_timeStamp and single_pkt["Arrival_Time"]<=e_timeStamp) :
        #continue
  

    conFK = single_pkt["Foreign_Key"]
    srcIP = single_pkt["Third_Layer"]["Source_IP"]
    dnsIP = single_pkt["Third_Layer"]["Destination_IP"]    #DNS server
    dns_query = single_pkt["Fourth_Layer"]["Fourth_Layer_Option"]["DNS_Info"]["Question"]
    
    
    if not (isinstance(srcIP,int)):  #check ipv6 and ipv4
      srcIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(srcIP[0:4],srcIP[4:8],srcIP[8:12],srcIP[12:16],srcIP[16:20],srcIP[20:24],srcIP[24:28],srcIP[28:32])
      if (srcIP == "0000:0000:0000:0000:0000:0000:0000:0000"):
        continue
    else:
      srcIP = long2ip(srcIP)
      
    if not (isinstance(dnsIP,int)):  #check ipv6 and ipv4
      dnsIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(dnsIP[0:4],dnsIP[4:8],dnsIP[8:12],dnsIP[12:16],dnsIP[16:20],dnsIP[20:24],dnsIP[24:28],dnsIP[28:32])
      if (dnsIP == "0000:0000:0000:0000:0000:0000:0000:0000"):
        continue
    else:
      dnsIP = long2ip(dnsIP)
      

    src_ip_ary = srcIP.split(".")
    if (ip_specify[0] != "n" and src_ip_ary[0] != ip_specify[0]):          #filter ip
      continue
    elif (ip_specify[1] != "n" and src_ip_ary[1] != ip_specify[1]):
      continue
    elif (ip_specify[2] != "n" and src_ip_ary[2] != ip_specify[2]):
      continue
    elif (ip_specify[3] != "n" and src_ip_ary[3] != ip_specify[3]):
      continue
    

    hostName = "not found"
    for i in range(len(dns_query)):
    
      hostName = dns_query[i]["Name"]

      try:
        record_hostName_list.index(hostName)
      except ValueError:
        record_hostName_list.append(hostName)
    
    
    
    try:
      record_conFK_list.index(conFK)
      continue
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
        
    
    try:
      record_srcIP_list.index(srcIP)
    except ValueError:
      record_srcIP_list.append(srcIP)
 
    try:
      record_dnsIP_list.index(dnsIP)
    except ValueError:
      record_dnsIP_list.append(dnsIP)
      
      

        
    #dns go end


  for i in range(len(record_hostName_list)):        #calculate node link number
    node_field_linkNum.append(0)
    for j in range(len(record_relation_list)):
      cursor_rel = json.loads(record_relation_list[j])
      if(cursor_rel["target"] == record_hostName_list[i]):
        node_field_linkNum[i] += relation_field_conNum[j]





  #highlight mark
  
  highlight_mark_hostNam = list()
  
  for i in range(len(record_hostName_list)):
  
    curr_value = node_field_linkNum[i]
  
    if(len(highlight_mark_hostNam) <= hl_value):
      highlight_mark_hostNam.append(curr_value)
      highlight_mark_hostNam.sort()
    else:
      if(curr_value > highlight_mark_hostNam[0]):
        highlight_mark_hostNam[0] = curr_value
        highlight_mark_hostNam.sort()
      else:
        continue


  text_show_index = int(len(highlight_mark_hostNam) - (len(highlight_mark_hostNam) * (per_lab/100)))


  #write data

  deleNode_list = list()       #save to bigger than the limit number of node

  fdata_d3js = open("./data/d3js_DNS.json", "w")
  
  fdata_d3js.write("{\n")

  fdata_d3js.write("\t\"dataTime\": \"")
  fdata_d3js.write(str(timeRange_str))
  fdata_d3js.write("\",\n")

  fdata_d3js.write("\t\"nodes\": [\n\t\t")

  check_loop_first = 1
  for single_node in record_srcIP_list:          #node  srcIP
    node_json = json.dumps({"node_attr": single_node, "highlight":1, "group": 1})

    
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

  #check_loop_first = 1
  #for single_node in record_dnsIP_list:          #node  dnsIP
    #node_json = json.dumps({"node_attr": single_node, "group": 2})
    ##node_json = json.loads(node_json)    #single quote
    
    #if check_loop_first == 1:
      #fdata_d3js.write(str(node_json))
      #print(node_json)
      #check_loop_first = 0
      #continue
      
    #fdata_d3js.write(",")
    #fdata_d3js.write("\n\t\t")
    #fdata_d3js.write(str(node_json))
    #print(node_json)
    
    
  #fdata_d3js.write(",")
  #fdata_d3js.write("\n\t\t")
  
  check_loop_first = 1
  for i in range(len(record_hostName_list)):          #node DNS  hostName


    if(node_field_linkNum[i] >= highlight_mark_hostNam[0]):

      if(node_field_linkNum[i] >= highlight_mark_hostNam[text_show_index]):
        node_json = json.dumps({"node_attr": record_hostName_list[i], "link_num": node_field_linkNum[i], "highlight":1, "group": 2})
      else:
        node_json = json.dumps({"node_attr": record_hostName_list[i], "link_num": node_field_linkNum[i], "highlight":0, "group": 2})

    else:
      deleNode_list.append(record_srcIP_list[i])
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
    
  
  
  #fdata_d3js.write(",\n\t\t{\"node_attr\": \"not found\", \"group\": 4}")
  
  fdata_d3js.write("\n\t],\n\t\"links\": [\n\t\t")
  
  check_loop_first = 1
  for i in range(len(record_relation_list)):          #rel

    rel_json = json.loads(record_relation_list[i])

    try:                                        #delete no node rel
      deleNode_list.index(rel_json["source"])
      continue
    except ValueError:
      process = "pass"

    rel_json = json.dumps({"source": rel_json["source"], "target": rel_json["target"], "type": "Connection_FK", "con_num": relation_field_conNum[i]})

    
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

