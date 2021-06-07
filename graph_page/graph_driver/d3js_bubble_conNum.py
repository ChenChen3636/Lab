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


  for i in range(len(argv_list)):     #get highlight mark value
    if(argv_list[i] == "-HL"):
      hl_value = int(argv_list[i+1])
  
  
  
  #----data processing
  print("data processing...")
  s = time.time()
  
  record_ip_list = list()
  record_conFK_list = list()
  
  record_relation_list = list()
  relation_field_conNum = list()
  
  
  node_field_desLinkNum = list()
  node_field_srcLinkNum = list()
  
  #db.inventory.find( { qty: { $in: [ 5, 15 ] } } )
  
  cursor_con = mycol_connection.find({"$and": [ { "Start_Time": {"$gt": s_timeStamp} }, { "Start_Time": {"$lt": e_timeStamp} }]})
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



    try:
      record_conFK_list.index(conFK)
      continue
    except ValueError:
      record_conFK_list.append(conFK)
      
      ipA_node = Node("network_ip", ip_addr=ipA)
      ipB_node = Node("network_ip", ip_addr=ipB)

      cursor_rel = json.dumps({"source": ipA, "target": ipB, "type": "Connection_ip"})
    
      #print(cursor_rel)
      
      try:
        rel_index = record_relation_list.index(cursor_rel)
        relation_field_conNum[rel_index] += 1
      except ValueError:
        record_relation_list.append(cursor_rel)
        relation_field_conNum.append(1)
        
   
    try:
      record_ip_list.index(ipA)
    except ValueError:
      record_ip_list.append(ipA)
 
    try:
      record_ip_list.index(ipB)
    except ValueError:
      record_ip_list.append(ipB)
    
    
    
    #print(packet) #cursor_packet end


  for i in range(len(record_ip_list)):        #calculate node link number (src&des bigger)
    node_field_desLinkNum.append(0)
    node_field_srcLinkNum.append(0)
    for j in range(len(record_relation_list)):
      cursor_rel = json.loads(record_relation_list[j])
      if(cursor_rel["target"] == record_ip_list[i]):
        node_field_desLinkNum[i] += relation_field_conNum[j]
      if(cursor_rel["source"] == record_ip_list[i]):
        node_field_srcLinkNum[i] += relation_field_conNum[j]


  #highlight mark write

  highlight_mark = list()
  
  for i in range(len(record_ip_list)):
  
    curr_value = node_field_desLinkNum[i]
  
    if(len(highlight_mark) <= hl_value):
      highlight_mark.append(curr_value)
      highlight_mark.sort()
    else:
      if(curr_value > highlight_mark[0]):
        highlight_mark[0] = curr_value
        highlight_mark.sort()
      else:
        continue


  #write data
  fdata_d3js = open("./data/d3js_bubble_ipAB.json", "w")
  
  fdata_d3js.write("{\n\t\"children\": [\n\t\t")

  check_loop_first = 1
  for i in range(len(record_ip_list)):      #node
  
    if(node_field_desLinkNum[i] >= highlight_mark[0]):
      node_json = json.dumps({"ip_addr": record_ip_list[i], "src_link_num": node_field_srcLinkNum[i], "des_link_num": node_field_desLinkNum[i]})
    else:
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
    
  
  
  fdata_d3js.write("\n\t]\n}")
  
  e = time.time()

  print("node: {}".format(len(record_ip_list)))
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

