// Veritabanı yönetim sistemi için fonksiyon ve veri yapısı tanımları
#ifndef DATABASE_H
#define DATABASE_H
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdbool.h>
#include <stdint.h>

// Kayıt veri modeli
#define MAX_RECORD_DATA 256

typedef struct
{
    int id;
    char data[MAX_RECORD_DATA];
} Record;

// Hata kodları
#define DB_SUCCESS 0
#define DB_ERROR_FULL -1
#define DB_ERROR_NOT_FOUND -2
#define DB_ERROR_NOT_INITIALIZED -3

// Fonksiyon prototipleri
typedef struct Database Database;
Database *db_init();
int db_add_record(Database *db, const char *record);
const char *db_get_record(Database *db, int id);
int db_delete_record(Database *db, int id);
void db_close(Database *db);
bool db_is_initialized(Database *db);
int db_get_record_count(Database *db);
int db_clear_records(Database *db);

#endif // DATABASE_H