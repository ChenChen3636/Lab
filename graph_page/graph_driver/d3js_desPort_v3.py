#!/usr/bin/python3

#add time range ex:2020 12 21 12 49 52  ~ 2020 12 21 12 49 57
#processing about 17 sec
#filter ip
# v3: web input

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



  for i in range(len(argv_list)):     #get highlight mark value
    if(argv_list[i] == "-HL"):
      hl_value_des = int(argv_list[i+1])
      per_lab_des = int(argv_list[i+2])

      hl_value_srcip = int(argv_list[i+3])
      per_lab_srcip = int(argv_list[i+4])
  
  
  #check_ip_range = input("filter IP? (y/n): ")
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
  
  record_srcIP_list = list()
  record_desPort_list = list()
  record_conFK_list = list()
  
  record_relation_list = list()
  relation_field_conNum = list()
  
  nodePort_field_linkNum = list()
  nodeSrcIP_field_linkNum = list()
  
  cursor_con = mycol_connection.find({"Destination_Port": {"$exists": "true"}, "$and": [ { "Start_Time": {"$gt": s_timeStamp} }, { "Start_Time": {"$lt": e_timeStamp} }]})    #check Fourth_Layer exist
  for single_con in cursor_con:
  

    conFK = single_con["Foreign_Key"]
    srcIP = single_con["Source_IP"]
    desPort = single_con["Destination_Port"]
    
    
    if not (isinstance(srcIP,int)):  #check ipv6 and ipv4
      srcIP = "{}:{}:{}:{}:{}:{}:{}:{}".format(srcIP[0:4],srcIP[4:8],srcIP[8:12],srcIP[12:16],srcIP[16:20],srcIP[20:24],srcIP[24:28],srcIP[28:32])
      if (srcIP == "0000:0000:0000:0000:0000:0000:0000:0000"):
        continue
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
    
    
    #print(packet) 
  #cursor_packet end



  for i in range(len(record_desPort_list)):        #calculate node port link number
    nodePort_field_linkNum.append(0)
    for j in range(len(record_relation_list)):
      cursor_rel = json.loads(record_relation_list[j])
      if(cursor_rel["target"] == record_desPort_list[i]):
        nodePort_field_linkNum[i] += relation_field_conNum[j]




  
  for i in range(len(record_srcIP_list)):      #calculate node src ip link number
    nodeSrcIP_field_linkNum.append(0)
    for j in range(len(record_relation_list)):
      cursor_rel = json.loads(record_relation_list[j])
      if(cursor_rel["source"] == record_srcIP_list[i]):
        nodeSrcIP_field_linkNum[i] += relation_field_conNum[j]
        
        
        
  #write highlight mark
  
  highlight_mark_desPort = list()
  
  for i in range(len(record_desPort_list)):        #desPort mark 
    curr_value = nodePort_field_linkNum[i]
    if(len(highlight_mark_desPort) <= hl_value_des):
      highlight_mark_desPort.append(curr_value)
      highlight_mark_desPort.sort()
    else:
      if(curr_value > highlight_mark_desPort[0]):
        highlight_mark_desPort[0] = curr_value
        highlight_mark_desPort.sort()
      else:
        continue
        

  highlight_mark_srcipOfdesPort = list()  #every desPort node have all scrip  rank
  
  for i in range(len(record_desPort_list)):       #src ip mark
    highlight_mark_srcipOfdesPort.append(list())
    curr_desPort = record_desPort_list[i] 
    for j in range(len(record_relation_list)):
      rel_json = json.loads(record_relation_list[j])
      if(curr_desPort == rel_json["target"]):
        curr_srcip_link = relation_field_conNum[j]
        if(len(highlight_mark_srcipOfdesPort[i]) <= hl_value_srcip):                       #limit number of node
          highlight_mark_srcipOfdesPort[i].append(curr_srcip_link)
          highlight_mark_srcipOfdesPort[i].sort()
        else:
          if(curr_srcip_link > highlight_mark_srcipOfdesPort[i][0]):
            highlight_mark_srcipOfdesPort[i][0] = curr_srcip_link
            highlight_mark_srcipOfdesPort[i].sort()
          else:
            continue
        
    highlight_mark_srcipOfdesPort[i].sort()
  
  

  text_show_index_des = int(len(highlight_mark_desPort) - (len(highlight_mark_desPort) * (per_lab_des/100)))

  text_show_index_srcip = list()
  for i in range(len(highlight_mark_srcipOfdesPort)):
    text_show_index_srcip.append(int(len(highlight_mark_srcipOfdesPort[i]) - (len(highlight_mark_srcipOfdesPort[i]) * (per_lab_srcip/100))))


  
  #write data

  deleNode_list_des = list()       #save to bigger than the limit number of node
  deleNode_list_srcip = list()

  fdata_d3js = open("./data/d3js_desPort.json", "w")
  
  fdata_d3js.write("{\n\t\"nodes\": [\n\t\t")

  check_loop_first = 1
  
  currip_link_port = list()
  for i in range(len(record_srcIP_list)):          #node  srcIP
   

    currip_link_port.clear()
    curr_srcip = record_srcIP_list[i]
  
    for j in range(len(record_relation_list)):
      rel_json = json.loads(record_relation_list[j])
      if(curr_srcip == rel_json["source"]):
        currip_link_port.append(rel_json["target"])
    

    for j in range(len(currip_link_port)):
      currIndex_port = record_desPort_list.index(currip_link_port[j])
      if (nodeSrcIP_field_linkNum[i] >= highlight_mark_srcipOfdesPort[currIndex_port][0]):

        if(nodeSrcIP_field_linkNum[i] >= highlight_mark_srcipOfdesPort[currIndex_port][text_show_index_srcip[currIndex_port]]):
          node_json = json.dumps({"node_attr": record_srcIP_list[i], "linkNum": nodeSrcIP_field_linkNum[i], "group": 1, "highlight": 1})
        else:
          node_json = json.dumps({"node_attr": record_srcIP_list[i], "linkNum": nodeSrcIP_field_linkNum[i], "group": 1, "highlight": 0})

      else:
        deleNode_list_srcip.append(curr_srcip)
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

  fdata_d3js.write(",")
  fdata_d3js.write("\n\t\t")

  check_loop_first = 1
  for i in range(len(record_desPort_list)):          #node  desPort

    
    if(nodePort_field_linkNum[i] >= highlight_mark_desPort[0]):

      if(nodePort_field_linkNum[i] >= highlight_mark_desPort[text_show_index_des]):
        node_json = json.dumps({"node_attr": record_desPort_list[i], "link_num": nodePort_field_linkNum[i], "group": 2, "highlight": 1})
      else:
        node_json = json.dumps({"node_attr": record_desPort_list[i], "link_num": nodePort_field_linkNum[i], "group": 2, "highlight": 0})

    else:
      deleNode_list_des.append(record_desPort_list[i])
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
      deleNode_list_des.index(rel_json["target"])
      continue
    except ValueError:
      process = "pass"

    try:
      deleNode_list_srcip.index(rel_json["source"])
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

  
  #fdata_d3js.close()



  #---remove single node
  if(os.path.isfile("./data/d3js_desPort_updata.json")):
    os.remove("./data/d3js_desPort_updata.json")

  fdata_d3js = open("./data/d3js_desPort.json", "r")
  json_data = fdata_d3js.read()
  json_data = json.loads(json_data)

  new_json = open("./data/d3js_desPort_updata.json", "w")   #updata json
  new_json.write("{\n\t\"nodes\": [\n\t\t")

  check_loop_first = 1


  for i in range(len(json_data["nodes"])):      #node copy

    single_node = json_data["nodes"][i]

    remove_mark = 1

    if(single_node["group"] == 1):              #src ip
      
      for j in range(len(json_data["links"])):

        single_link = json_data["links"][j]

        if(single_node["node_attr"] == single_link["source"]):
          remove_mark = 0
          continue
    else:
      remove_mark = 0

    if(remove_mark == 1):
      continue


    single_node = json.dumps(single_node)
    if check_loop_first == 1:
      new_json.write(str(single_node))
      check_loop_first = 0
      continue
      
    new_json.write(",")
    new_json.write("\n\t\t")
    new_json.write(str(single_node))


  new_json.write("\n\t],\n\t\"links\": [\n\t\t")

  check_loop_first = 1
  for i in range(len(json_data["links"])):      #link copy

    single_link = json_data["links"][i]

    single_link = json.dumps(single_link)
    if check_loop_first == 1:
      new_json.write(str(single_link))
      check_loop_first = 0
      continue
      
    new_json.write(",")
    new_json.write("\n\t\t")
    new_json.write(str(single_link))

  new_json.write("\n\t]\n}")

  fdata_d3js.close()
  new_json.close()


  


  e = time.time()

  print("node: {}".format(len(record_srcIP_list)+len(record_desPort_list)))
  print("relationship: {}".format(len(record_relation_list)))
  print("con_Foreign_Key: {}".format(len(record_conFK_list)))

  
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

