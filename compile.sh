#!/bin/bash

gcc -c merge_pcap.c -I/usr/local/include/libbson-1.0 -I/usr/local/include/libmongoc-1.0 -lpcap -lmongoc-1.0 -lbson-1.0
g++ -c libpcap_tools.cpp -lpcap
g++ merge_pcap.o libpcap_tools.o -o merge -I/usr/local/include/libbson-1.0 -I/usr/local/include/libmongoc-1.0 -lpcap -lmongoc-1.0 -lbson-1.0