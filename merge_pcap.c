#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <malloc.h>
#include <errno.h>
#include <sys/stat.h>
#include <pcap.h>
#include <mongoc.h>
#include <bson.h>
#include "libpcap_tools.h"


extern int errno;


typedef struct Node{
    char *file_path;
    struct Node *next;
} Node;




void merge_handler(unsigned char *dumpfile, const struct pcap_pkthdr *pkthdr, const unsigned char *content);

Node *list_create(Node *first);
Node *list_add(Node* first, char *value);
Node *get_file_list(Node *first, int argc, char *argv[]);
void list_print(Node *first);
void list_free(Node *first);

int protocol_int(char *prot_str);
bson_t *query_command(int ts, int te, int prot,
                      long long srcip, long long dstip,
                      int srcport, int dstport, int all);



int main(int argc, char *argv[])
{
    int i, ret, errnum;
    char errbuf[PCAP_ERRBUF_SIZE], fname[128], ordered_fname[128];
    
    /* packet header & body */
    struct pcap_pkthdr *pkthdr   = NULL;
    const unsigned char *content = NULL;

    for(i = 0; i < argc; i++){
        if(strcmp(argv[i], "-fn") == 0){
            strcpy(fname, argv[i + 1]);
            break;
        }
    }
    
    if(fname == NULL){
        printf("EEROR fname.\n");
        return -1;
    }
    
    memset(ordered_fname, '\0', sizeof(ordered_fname));
    strcpy(ordered_fname, "./ordered_");
    strcat(ordered_fname, fname);
    
    printf("fname: %s\n", fname);
    /* merged .pcap file */
    FILE *pFile = fopen (fname , "wb+");
    if (pFile == NULL) {
        errnum = errno;
        fprintf(stderr, "Value of errnum: %d\n", errno);
        perror("Error printed by perror");
        fprintf(stderr, "Error opening file: %s\n", strerror(errnum));
		
        return -1;
    }
    else{
        fclose (pFile);
    }
	
    pcap_t *fpcap = pcap_open_dead(DLT_EN10MB, 65535);
    pcap_dumper_t *pcap_dumper = pcap_dump_open(fpcap, fname);
	
    if(pcap_dumper == NULL){
        fprintf(stderr, "pcap_dump_open: %s\n", pcap_geterr(fpcap));
        pcap_close(fpcap);
        return -1;
    }

    
    Node *file_list = get_file_list(file_list, argc, argv);
    Node *current = NULL;

    pcap_t *tmp_fpcap = NULL;


    current = file_list;
    
    while(current != NULL){
        tmp_fpcap = pcap_open_offline(current->file_path, errbuf);
        if(tmp_fpcap == NULL){
            printf("file_handle == NULL\n");
            continue;
        }

        pcap_loop(tmp_fpcap, 0, merge_handler, (unsigned char *)pcap_dumper);
        pcap_close(tmp_fpcap);

        current = current->next;
    }
    
    printf("find all pcap\n");


    pcap_dump_flush(pcap_dumper);
    pcap_dump_close(pcap_dumper);
    pcap_close(fpcap);


    pcap_t *res_fpcap = NULL;
    res_fpcap = pcap_open_offline(fname, errbuf);
    if(res_fpcap == NULL){
        printf("res_fpcap == NULL: %s\n", errbuf);
        return -1;
    }

    if(sort_pcap(res_fpcap, ordered_fname) == -1){
        printf("ERROR sort_pcap\n");
        return -1;
    }
    

    return 0;
}

void merge_handler(unsigned char *dumpfile, const struct pcap_pkthdr *pkthdr, const unsigned char *content)
{
    pcap_dump(dumpfile, pkthdr, content);
    return;
}


Node *list_create(Node *first)
{
    printf("\n[list created]\n");

    return NULL;
}

Node* list_add(Node* first, char *value)
{
    Node *tempNode = (Node*) malloc(sizeof(Node));
    tempNode->file_path = (char*) malloc(256 * sizeof(char));

    strcpy(tempNode->file_path, "/PCAP_DB/DB-Running/");
    strcat(tempNode->file_path, value);

    tempNode->next = first;
    first = tempNode;
    

    return first;
}

void list_print(Node *first)
{
    int count = 0;

    Node *tempNode = first;
        
    printf("\n[list content]\n");
    
    while(tempNode != NULL){
        count++;
        //printf("Foreign_Key: %s\n", tempNode->file_path);
        
        tempNode = tempNode->next;
    }

    printf("Total count : %d\n", count);


    return;
}

void list_free(Node *first)
{
    Node *tmp = NULL;
    Node *tempNode = first;

    while(tempNode != NULL){
        tmp = tempNode;
        tempNode = tempNode->next;

        free(tmp->file_path);
        tmp->file_path = NULL;

        free(tmp);
        tmp = NULL;
    }

    first = NULL;


    return;
}

int protocol_int(char *prot_str)
{
    if(prot_str == NULL){
        return -1;
    }

    if(strstr(prot_str, "TCP") != NULL){
        return 6;
    }
    else if(strstr(prot_str, "FTP") != NULL){
        return 21;
    }
    else if(strstr(prot_str, "UDP") != NULL){
        return 17;
    }
    else if(strstr(prot_str, "DNS") != NULL){
        return 53;
    }
	  else if(strstr(prot_str, "ARP") != NULL){
        return 2054;
    }
	  else if(strstr(prot_str, "ICMP") != NULL){
        return 1;
    }
    else{
        return -1;
    }
}

bson_t *query_command(int ts, int te, int prot,
                      long long srcip, long long dstip,
                      int srcport, int dstport, int all)
{
    bson_t *query;


    if(ts == -1 || te == -1){
        printf("ERROR Time Range.\n");
        exit(0);
    }
    else{
        query = BCON_NEW("Start_Time", "{", "$gt", BCON_INT32(ts), "$lt", BCON_INT32(te), "}");
    }


    if(all == 1){
        return query;
    }

    if(prot != -1){
        BCON_APPEND(query, "Connection_Type", BCON_INT32(prot));
    }
    
    if(srcip != -1){
        BCON_APPEND(query, "Source_IP", BCON_INT64(srcip));
    }

    if(dstip != -1){
        BCON_APPEND(query, "Destination_IP", BCON_INT64(dstip));
    }

    if(srcport != -1){
        BCON_APPEND(query, "Source_Port", BCON_INT32(srcport));
    }

    if(dstport != -1){
        BCON_APPEND(query, "Destination_Port", BCON_INT32(dstport));
    }


    return query;
}

Node *get_file_list(Node *first, int argc, char *argv[])
{
    int i, data_ck = 0;
    int all = 1, time_s = -1, time_e = -1, prot = -1, src_port = -1, dst_port = -1;
    long long src_ip = -1, dst_ip = -1;

    char *prot_str = NULL;
    char *singleData = "", *value_json, *con_json;

    mongoc_client_t *client;
    mongoc_database_t *database;
    mongoc_collection_t *con_collection;
    mongoc_cursor_t *cursor;
    bson_t *doc;
    bson_t *query,*insert_data;
    bson_error_t error;
    

    mongoc_init();

    client = mongoc_client_new ("mongodb://localhost:27017");
    database = mongoc_client_get_database (client, "cgudb");
    con_collection = mongoc_client_get_collection (client, "cgudb", "connection_collection");
  

    first = list_create(first);
   
    /* search condition */
    for(i = 0; i < argc; i++){
        if(strcmp(argv[i], "-tr") == 0){
            time_s = atoi(argv[i + 1]);
            time_e = atoi(argv[i + 2]);
            all--;
        }
        else if(strcmp(argv[i], "-p") == 0){
            prot_str = argv[i + 1];
            all--;
        }
        else if(strcmp(argv[i], "-srcip") == 0){
            src_ip = atoll(argv[i + 1]);
            all--;
            printf("src_ip : %lld\n", src_ip);
        }
        else if(strcmp(argv[i], "-dstip") == 0){
            dst_ip = atoll(argv[i + 1]);
            all--;
            printf("dst_ip : %lld\n", dst_ip);
        }
        else if(strcmp(argv[i], "-srcport") == 0){
            src_port = atoi(argv[i + 1]);
            all--;
            printf("src_port : %d\n", src_port);
        }
        else if(strcmp(argv[i], "-dstport") == 0){
            dst_port = atoi(argv[i + 1]);
            all--;
            printf("dst_port : %d\n", dst_port);
        }
    }

    prot = protocol_int(prot_str);
    printf("protocol : %d\n", prot);
    
    /*
    if(time_s == -1 || time_e == -1){   // query all data
        query = bson_new();
    }
    else{   // query particular range data
        query = BCON_NEW("Start_Time", "{", "$gt", BCON_INT32(time_s), "$lt", BCON_INT32(time_e), "}");
    }
    */

    query = query_command(time_s, time_e, prot, src_ip, dst_ip, src_port, dst_port, all);
    cursor = mongoc_collection_find(con_collection, MONGOC_QUERY_NONE, 0, 0, 0, query, NULL, NULL);


    while(mongoc_cursor_next(cursor, &doc)){
        value_json = bson_as_json(doc, NULL); 
        
        singleData = strtok(value_json, "\"");
        
        while(singleData != NULL){
        
            if(strcmp(singleData, "Connection_Folder_Path") == 0){
                data_ck = 2;
                singleData = strtok(NULL, "\"");

                continue;
            }
            
            if(data_ck == 2){
                data_ck--;
            }
            else if(data_ck == 1){
                first = list_add(first, singleData);	//record to linked list
                data_ck--;
            }
            
            singleData = strtok(NULL, "\"");
        }
        
        bson_free(value_json);
    }
   

    mongoc_collection_destroy(con_collection);
    mongoc_database_destroy(database);
    mongoc_client_destroy(client);
    mongoc_cleanup();
   

    list_print(first);

    printf("\nexecute end\n");


    return first;
}

