#include <iostream>
#include <cstdlib>
#include <cstring>
#include <vector>
#include <algorithm>
#include <pcap.h>
#include "libpcap_tools.h"


using namespace std;


bool compare(pkt_struct a, pkt_struct b)
{
	return a.pkt_time < b.pkt_time;
}

int sort_pcap(pcap_t *unsort_pcap, char *fname)
{
    int ret;

    struct pcap_pkthdr *hdr   = NULL;
    const unsigned char *data = NULL;
    unsigned char *copy_data  = NULL;

    pkt_struct packet;

    vector<pkt_struct> pkt_vector;

    if(fname == NULL){
        printf("sort_pcap fname == NULL\n");
        return -1;
    }

    while(ret = pcap_next_ex(unsort_pcap, &hdr, &data)){
        if(ret == 1){
            /* copy packets to vector */       
            copy_data  = (unsigned char *) malloc(hdr->caplen * sizeof(unsigned char));
            memcpy(copy_data, data, hdr->caplen * sizeof(unsigned char));

            packet.pkt_header = *hdr;
            packet.pkt_data = copy_data;
            packet.pkt_time   = hdr->ts.tv_sec + hdr->ts.tv_usec * (1.0/1000000);

            pkt_vector.push_back(packet);
        }
        else{
            break;
        }
    }
    
    pcap_close(unsort_pcap);

    printf("packet to vector.\n");


    /* sort the packets in vector */
    sort(pkt_vector.begin(), pkt_vector.end(), compare);
    
    printf("finish vector sort.\n");
    

    /* dump the ordered packets to the new file */
    FILE *pFile = fopen (fname , "wb+");
    if(pFile == NULL){
        printf("sort_pcap pFile == NULL\n");
        return -1;
    }
    else{
        fclose(pFile);
    }

    pcap_t *final_pcap = pcap_open_dead(DLT_EN10MB, 65536);
    pcap_dumper_t * dumper = pcap_dump_open(final_pcap, fname);
    
    for(vector<pkt_struct>::size_type i = 0; i < pkt_vector.size(); i++){
        pcap_dump((unsigned char *) dumper, &pkt_vector.at(i).pkt_header, pkt_vector.at(i).pkt_data);
    }


    pcap_dump_flush(dumper);
    pcap_dump_close(dumper);
    pcap_close(final_pcap);


    for(vector<pkt_struct>::size_type i = 0; i < pkt_vector.size(); i++){
        free(pkt_vector.at(i).pkt_data);
    }

    pkt_vector.clear();
    pkt_vector.shrink_to_fit();


    return 0;
}