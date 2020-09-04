#if defined _cmd__hadler_included
    #endinput
#endif

#define _cmd__hadler_included

#include <amxmodx>
#include <sqlx>

public SQL_Error(Handle:query, const error[], errornum, failstate)
{
	new qstring[1024]
	SQL_GetQueryString(query, qstring, 1023)
	
	if(failstate == TQUERY_CONNECT_FAILED) 		PrintMessage("[SQLX] Error connected to database")
	else if (failstate == TQUERY_QUERY_FAILED) 	PrintMessage("[SQLX] Failed")

	PrintMessage("[SQLX] Error '%s' with '%d'", error, errornum)
	PrintMessage("[SQLX] %s", qstring)
	
	new LogDat[16]
	get_time("%Y_%m_%d", LogDat, 15);

	new LogFile[64];
	getDirByType(DirData:DIR_LOG, LogFile, charsmax(LogFile), "QueryError%s.log", LogDat)

	write_file(LogFile, qstring, -1);
	return SQL_FreeHandle(query)
}

public QuerySqlOK(failstate, Handle:query, error[], errcode, data[], datasize, Float:queuetime)
{
	if(failstate)
	{
		return SQL_Error(query, error, errcode, failstate);
	}

	return SQL_FreeHandle(query);
}

public UnbanSQLResult(FailState,Handle:Query,Error[],Errcode,Data[],DataSize)
{
  	if(FailState)
	{
		return SQL_Error(Query, Error, Errcode, FailState);
	}

	new id = Data[0];

	if(SQL_AffectedRows(Query) < 1) Print(id, "[BanCore] Player is not found");
	else							Print(id, "[BanCore] Player was unbanned");

	return SQL_FreeHandle(Query);
}

public BanCountData(FailState,Handle:Query,Error[],Errcode,Data[],DataSize)
{
	if(FailState)
	{
		return SQL_Error(Query, Error, Errcode, FailState);
	}
	new id = Data[0], ret
	
	if(!is_user_connected(id)) 
		return SQL_FreeHandle(Query);
	
	new bans_count = SQL_ReadResult(Query, 0);
	
	if (bans_count < get_pcvar_num(g_Cvars[CVAR_BANCOUNT_LOG]))
		return SQL_FreeHandle(Query);

	PrintMessage("Player [%s] with [%d count of bans] enter on a server", g_info_player[id][CS_PLAYER_NAME],bans_count)
	ExecuteForward(g_forward[DB_HISTORY_BAN],ret,id, bans_count);
	
	return SQL_FreeHandle(Query);
	
}

public LoadUnbanData(FailState,Handle:Query,Error[],Errcode,Data[],DataSize)
{
	if(FailState)
		return SQL_Error(Query, Error, Errcode, FailState);
	
	new id = Data[0]
	
	if(!is_user_connected(id)) 
		return SQL_FreeHandle(Query); // ????
	
	if ( !SQL_NumResults(Query) )
		return Print(id, "%L", id, "DB_MENU_UNBAN_EMPTY")

	ShowUnbanMenu(id, Query)

	return SQL_FreeHandle(Query);
}

public ClearBanlist(FailState,Handle:Query,Error[],Errcode,Data[],DataSize)
{
  	if(FailState)
	{
		return SQL_Error(Query, Error, Errcode, FailState);
	}
	
	PrintMessage("[CLEAR_BANLIST] %s clear the banlist", g_info_player[Data[0]][CS_PLAYER_NAME])
	return SQL_FreeHandle(Query);
}

public SQl_CheckTime(failstate, Handle:query, const error[], errornum, const data[], size, Float:queuetime)
{
	if (failstate)
	{
		return SQL_Error(query, error, errornum, failstate);
	}
	
	new SQLTime[16];
	new i_Col_SQLTime = SQL_FieldNameToNum(query, "UNIX_TIMESTAMP(NOW())");
	if (SQL_MoreResults(query))
	{
		SQL_ReadResult(query, i_Col_SQLTime, SQLTime, 15);
		
		TimeGap = str_to_num(SQLTime) - get_systime(0);
		PrintMessage("%L", LANG_SERVER, "DB_MESS_TIMESTAMP", TimeGap);
	}
	
	//if(get_pcvar_num(g_Cvars[CVAR_CSBANS_SUPPORT])) 	CheckCSBanTable();
	return SQL_FreeHandle(query);
}

public LoadDataPlayer(FailState,Handle:Query,Error[],Errcode,Data[BannedData],DataSize)
{
	if(FailState)
	{
		return SQL_Error(Query, Error, Errcode, FailState);
	}
	new id = Data[BD_BAN_ADMIN_PLAYER_ID] // playerID
	
	if(!is_user_connected(id)) 
		return SQL_FreeHandle(Query);
	
	
	CheckDataPlayer(Data, Query)
	return SQL_FreeHandle(Query);	
}

public LoadLastBans(FailState,Handle:Query,Error[],Errcode,Data[],DataSize)
{
	if(FailState)
	{
		return SQL_Error(Query, Error, Errcode, FailState);
	}
	new banid = Data[0];
	new data[BanData],ban_id = get_ban_id(banid);

	ArrayGetArray(g_ban_data, ban_id, data);
		
	if(SQL_NumResults(Query) >0)
	{
		new UID = SQL_FieldNameToNum(Query,"uid")
		new Uid[16]
		SQL_ReadResult(Query,UID, Uid, 15)
		
		if(!equali(Uid,g_info_player[data[BAN_PLAYER_ID]][CS_PLAYER_UID]))
		{
			copy(g_info_player[data[BAN_PLAYER_ID]][CS_PLAYER_UID], 15, Uid)
		}
	  	return !data[BAN_ADMIN_ID] ? log_amx("[%s] %L",PREFIX, LANG_SERVER, "DB_ALREADY_BAN") : Print(data[BAN_ADMIN_ID], "%L", data[BAN_ADMIN_ID], "DB_ALREADY_BAN")
	}
	
	Cmd_BanPlayer(data[BAN_ADMIN_ID],data[BAN_PLAYER_ID], data[BAN_TIME],data[BAN_REASON])
	
	return SQL_FreeHandle(Query);
}

public LoadBanHistory(FailState,Handle:Query,Error[],Errcode,Data[BannedData],DataSize)
{
	if(FailState)
	{
		return SQL_Error(Query, Error, Errcode, FailState);
	}
		
	// Already Banned
	if(SQL_NumResults(Query) > 0)
	{
	  	return SQL_FreeHandle(Query)
	}

	// new data[BannedData];

	// for(new i; i< BannedData; i++) {

	// }

	_Ban(Data)
	return SQL_FreeHandle(Query);
}