// Veritabanı yönetim sistemi - Modern C, struct tabanlı kayıt yönetimi
#include "database.h"

#define MAX_RECORDS 100

struct Database
{
    Record records[MAX_RECORDS];
    int record_count;
    bool initialized;
};

Database *db_init()
{
    Database *db = (Database *)malloc(sizeof(Database));
    if (db)
    {
        db->record_count = 0;
        db->initialized = true;
    }
    return db;
}

int db_add_record(Database *db, const char *data)
{
    if (!db || !db->initialized)
        return DB_ERROR_NOT_INITIALIZED;
    if (db->record_count >= MAX_RECORDS)
        return DB_ERROR_FULL;
    db->records[db->record_count].id = db->record_count + 1;
    strncpy(db->records[db->record_count].data, data, MAX_RECORD_DATA);
    db->record_count++;
    return DB_SUCCESS;
}

const char *db_get_record(Database *db, int id)
{
    if (!db || !db->initialized)
        return NULL;
    for (int i = 0; i < db->record_count; ++i)
    {
        if (db->records[i].id == id)
        {
            return db->records[i].data;
        }
    }
    return NULL;
}

int db_delete_record(Database *db, int id)
{
    if (!db || !db->initialized)
        return DB_ERROR_NOT_INITIALIZED;
    int found = -1;
    for (int i = 0; i < db->record_count; ++i)
    {
        if (db->records[i].id == id)
        {
            found = i;
            break;
        }
    }
    if (found == -1)
        return DB_ERROR_NOT_FOUND;
    for (int i = found; i < db->record_count - 1; ++i)
    {
        db->records[i] = db->records[i + 1];
    }
    db->record_count--;
    return DB_SUCCESS;
}

void db_close(Database *db)
{
    if (db)
    {
        db->initialized = false;
        free(db);
    }
}

bool db_is_initialized(Database *db)
{
    return db && db->initialized;
}

int db_get_record_count(Database *db)
{
    if (!db || !db->initialized)
        return DB_ERROR_NOT_INITIALIZED;
    return db->record_count;
}

int db_clear_records(Database *db)
{
    if (!db || !db->initialized)
        return DB_ERROR_NOT_INITIALIZED;
    db->record_count = 0;
    return DB_SUCCESS;
}