#if defined _cmd__request_included
    #endinput
#endif

#define _cmd__request_included

#include <amxmodx>
#include <sqlx>

stock SQL_AddBan(data[BannedData]) {
    return Ban_addToSQL(data)
}
