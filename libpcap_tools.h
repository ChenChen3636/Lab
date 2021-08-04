#ifndef LIBPCAP_TOOLS_H
#define LIBPCAP_TOOLS_H
#endif

#include <stdbool.h>
#include <pcap.h>


#ifdef __cplusplus
extern "C" {
#endif


typedef struct packet_struct {
	  struct pcap_pkthdr  pkt_header;
	  unsigned char       *pkt_data;
	  long double         pkt_time;
} pkt_struct ;


bool comparison(pkt_struct *a, pkt_struct *b);
int sort_pcap(pcap_t *unsort_pcap, char *fname);


#ifdef __cplusplus
}
#endif