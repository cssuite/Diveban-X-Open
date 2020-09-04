#if defined _stock_included
    #endinput
#endif

#define _stock_included

#include <amxmodx>
#include <amxmisc>
#include <sqlx>

stock bool:IsStrFloat(string[])
{
	new len = strlen(string);
	for ( new i = 0; i < len; i++ )
	{
		switch ( string[i] )
		{
			case '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '-', '*':continue;
			default:	return false;
		}
	}
	
	return true;
}

stock SVC_DIRECTOR_STUFFTEXT_CMD( text[] , id = 0 ) {
	#define SVC_DIRECTOR_ID			51
	#define SVC_DIRECTOR_STUFFTEXT_ID	10
	
	message_begin( MSG_ONE, SVC_DIRECTOR_ID, _, id )
	write_byte( strlen(text) + 2 )
	write_byte( SVC_DIRECTOR_STUFFTEXT_ID )
	write_string( text )
	message_end()
}

stock PrintMessage(const szMessage[], any:...)
{
	new szMsg[1024];
	vformat(szMsg, charsmax(szMsg), szMessage, 2);
	
	new LogDat[16]; 
	get_time("%Y_%m_%d", LogDat, 15);

	new LogFile[64];
	getDirByType(DirData:DIR_LOG, LogFile, charsmax(LogFile), "Log%s.log", LogDat)
	log_to_file(LogFile,"[%s] %s",PREFIX,szMsg)
	
	return -2;
}
stock ShowMotd(player,reason[],minutes,name_adm[])
{
	new str[12], unban[12]
	num_to_str(minutes,str,11)
	get_pcvar_string(g_Cvars[CVAR_COOKIE_BANFILE],g_url,254)
	
	num_to_str(get_systime(0) + minutes*60, unban, 11)
	formatex(g_url,254,"%s?NICK=%s&REASON=%s&TIME=%s&UNBAN=%s&ADMIN=%s",g_url,g_info_player[player][CS_PLAYER_NAME],reason,str,unban,name_adm)
}
stock bool:has_player_access_flag( const id, playerID, bool: disconnect = false)
{
	if ( id == 0 )	return true;

	new bool:has_player_main = false;
	new bool:has_player_sub = false;
	new bool:has_player_immune = false;

	switch (disconnect)
	{
		case true:
		{
			new data[DiscData]
			ArrayGetArray(g_array, playerID, data);

			has_player_sub = bool:(data[dd_immune_flag] == 2);
			has_player_immune = bool:(data[dd_immune_flag] == 3);
		}
		default:
		{
			new dummy[1], UserStatus:st;
			get_user_status(playerID, st, dummy, charsmax(dummy))
			has_player_main = bool:( st == UserStatus:ST_MAIN_ADMIN);

			has_player_sub = bool:(st == UserStatus:ST_SUB_ADMIN);
			has_player_immune = bool:(st == UserStatus:ST_ADMIN);

		}
	}

	new dummy[1], UserStatus:st;
	get_user_status(id, st, dummy, charsmax(dummy))

	// Если у target есть флаг главного админа, то false
	if ( has_player_main )	return false;

	// Если у id есть флаг гл админа то всегда true
	if ( st == UserStatus:ST_MAIN_ADMIN )	return true;

	// Если у id есть флаг зам админа
	if ( st == UserStatus:ST_SUB_ADMIN )
		return !has_player_sub;

	// Если у id есть флаг зам иммунитета
	if ( st == UserStatus:ST_ADMIN) {
		if ( has_player_sub ) return false;

		return !has_player_immune
	}

	return false;
}

stock is_web_ban_exists()
{
	g_web_ban = true
	
	new file[64];
	get_pcvar_string(g_Cvars[CVAR_COOKIE_BANFILE], file, charsmax(file))
	
	if( strlen(file) <= 10 || containi(file, "http") == -1 )
		g_web_ban = false;
		
	get_pcvar_string(g_Cvars[CVAR_COOKIE_CHECKFILE], file, charsmax(file))
	
	if( strlen(file) <= 10 || containi(file, "http") == -1 )
		g_web_ban = false;
		
	if( g_web_ban )
		return;
		
	PrintMessage("CookieBan disabled, configure divebans_cookieban_* in diveban.cfg")
}

stock SetCvars()
{
	g_Cvars[CVAR_HOST] =				register_cvar("divebans_host", "")
	g_Cvars[CVAR_USER] = 				register_cvar("divebans_user", "")
	g_Cvars[CVAR_PASS] = 				register_cvar("divebans_pass", "")
	g_Cvars[CVAR_DB] = 					register_cvar("divebans_db", "")
	g_Cvars[CVAR_TABLE] = 				register_cvar("divebans_table", "")

	g_Cvars[CVAR_SERVER_NAME] =			register_cvar("divebans_server_name", "")
	g_Cvars[CVAR_DELAY_TIME] =			register_cvar("divebans_delay_time", "5.0")
	g_Cvars[CVAR_PREVENT_IP] =			register_cvar("divebans_prevent_ip", "0")
	g_Cvars[CVAR_SQLTIME] =				register_cvar("divebans_sqltime", "1")
	
	g_Cvars[CVAR_MAX_EFFECT_BANTIME] =	register_cvar("divebans_maxeffect_time", "1440")
	
	g_Cvars[CVAR_COOKIE_BANFILE] =		register_cvar("divebans_cookieban_banfile", "")
	g_Cvars[CVAR_COOKIE_CHECKFILE] =	register_cvar("divebans_cookieban_checkfile", "")
	
	g_Cvars[CVAR_MAIN_FLAG] =			register_cvar("divebans_flag_main", "s")
	g_Cvars[CVAR_SUB_FLAG] =			register_cvar("divebans_flag_sub", "h")
	
	g_Cvars[CVAR_CMD_TYPE] =			register_cvar("divebans_cmd_type", "1")
	
	g_Cvars[CVAR_LOG_LEVEL] =			register_cvar("divebans_log_level", "1")
	g_Cvars[CVAR_CLEAR_LOGS] =			register_cvar("divebans_clear_logs", "7");
	
	g_Cvars[CVAR_KICK_TIME] =			register_cvar("divebans_kick_time", "1.5");
	g_Cvars[CVAR_CHECK_TIME] =			register_cvar("divebans_check_time", "3.0");
	
	g_Cvars[CVAR_MARKER_LOG] =			register_cvar("divebans_marker_log", "1");
	g_Cvars[CVAR_BANCOUNT_LOG] =		register_cvar("divebans_bancount_log", "3");

	new execPath[64]
	getDirByType(DirData:DIR_CONFIG, execPath, charsmax(execPath), CONFIG)

	server_cmd("exec %s",execPath)
	server_exec()
}

stock get_player_fastcmd( arg[], arglen )
{
	new is_ip = contain(arg, ".");
	new is_uid = contain(arg, "#");
	
	new player = -1
	
	if(is_ip >= 0)
		player = find_player("d", arg)
	else if (is_uid >= 0)
	{
		replace(arg, arglen,"#","")
		player = find_player("k", str_to_num(arg))
	}
		
	return player;
}

stock get_time_fastcmd ( const arg[] )
{
	new is_ip = contain(arg, ".");
	new is_uid = contain(arg, "#");
	
	if(is_ip == -1 && is_uid == -1)
		return str_to_num(arg);
	
	return -1;
}

stock ClearLogs()
{
	new Array: Logs;
	Logs = ArrayCreate(64);
	
	new szFile[64];
	new LogDir[64];
	getDirByType(DirData:DIR_LOG, LogDir, charsmax(LogDir), "")
	
	new dir = open_dir(LogDir, szFile, charsmax(szFile))
	new temp[64], log_time; new del_time = get_systime(0) - 60*60*24*get_pcvar_num(g_Cvars[CVAR_CLEAR_LOGS])
	if(dir)
	{
		do
		{
			if(!is_log_file(szFile, strlen(szFile)))
				continue;
			


			copy(temp, charsmax(temp), szFile)
			replace(temp, charsmax(temp), "Log", "")
		
			log_time = parse_time(temp, "%Y_%m_%d")
			
			if(log_time - del_time <= 0)
				ArrayPushString(Logs,szFile)
		}
		while(next_file(dir, szFile, charsmax(szFile)))
		
		close_dir(dir)
	}
	
	new size = ArraySize(Logs)
	for(new i; i<size; i++)
	{
		ArrayGetString(Logs, i, temp, charsmax(temp))
		
		formatex(szFile, charsmax(szFile), "%s%s",LogDir,temp)
		delete_file(szFile)
	}
	
	ArrayDestroy(Logs)
}

stock get_player_marker(id, Handle:Query, marker[], len)
{
	new cvar_marker = get_pcvar_num(g_Cvars[CVAR_MARKER_LOG]);
	
	if ( !cvar_marker )
		return 0;
	
	new mark[34];
	SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"steam"), mark, charsmax(mark));
	
	if(equali(mark, g_info_player[id][CS_PLAYER_ID]))
		return formatex(marker, len, "|SteamID|")
	
	SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"ip"), mark, charsmax(mark));

	if(equali(mark, g_info_player[id][CS_PLAYER_IP]))
		return formatex(marker, len, "|IP|")

		
	SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"ipcookie"), mark, charsmax(mark));

	if(equali(mark, g_info_player[id][CS_PLAYER_IP]))
		return formatex(marker, len, "|IP Cookie|")

	SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"diveid"), mark, charsmax(mark));

	if(equali(mark, g_DiveID[id]))
		return formatex(marker, len, "|DiveID|") 

	SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"cdkey"), mark, charsmax(mark));

	if(equali(mark, g_iCDKey[id]))
		return formatex(marker, len, "|CD-Key|")
			
	return 1;
}

stock player_ban_count ( id )
{
	new cvar_ban_count = get_pcvar_num(g_Cvars[CVAR_BANCOUNT_LOG])
	
	if ( !cvar_ban_count )
		return;
	
	new query[256];
	new Data[1];Data[0] = id;
	
	formatex(query, charsmax(query), "SELECT COUNT(*) FROM `%s` WHERE (`steam`='%s' and `bantype` like '%%S%%') OR (`ip`='%s' and `bantype` like '%%I%%') ", szTable\
	, g_info_player[id][CS_PLAYER_ID], g_info_player[id][CS_PLAYER_IP]);

	
	SQL_ThreadQuery(g_SqlTuple,"BanCountData", query, Data, 1);
}

stock bool:is_log_file(CurrNAME[], len  )
{
	static S_TRY[] = ".log"
	
	if ( ( len >= 4 ) && ( CurrNAME[ len - 1 ] == S_TRY[ 3 ] ) && 
	( CurrNAME[ len - 2 ] == S_TRY[ 2 ] ) && ( CurrNAME[ len - 3 ] == S_TRY[ 1 ] ) && 
	( CurrNAME[ len - 4 ] == S_TRY[ 0 ] ) ) 
	return true;
	
	return false;
}
stock bool:is_user_steam(id)
{
// Author Sh0oter
        static dp_pointer
        if(dp_pointer || (dp_pointer = get_cvar_pointer("dp_r_id_provider")))
        {
            server_cmd("dp_clientinfo %d", id)
            server_exec()
            return (get_pcvar_num(dp_pointer) == 2) ? true : false
        }
        return false
} 
stock MakeStringSQLSafe(const input[], output[], len)
{
	copy(output, len, input);
	replace_all(output, len, "'", "*");
	replace_all(output, len, "^"", "*"); // ^"
	replace_all(output, len, "`", "*");
}
stock _parse_time( const _data[] )
{
	new data[32];
	copy(data, charsmax(data), _data)

	new count;
	new dummy[8]
	while( strlen(data) > 0)
	{
		strbreak(data, dummy, charsmax(dummy), data, charsmax(data))

		if(equal(dummy, "^"")) // ^" ^_^
			continue;

		count++;
	}
	return count
}

stock get_user_status(id, &UserStatus:st, status[], len)
{
	new uFlags = get_user_flags(id);

	new mFlag[8], sFlag[8]; // Main & sub flags
	get_pcvar_string(g_Cvars[CVAR_MAIN_FLAG], mFlag, charsmax(mFlag));
	get_pcvar_string(g_Cvars[CVAR_SUB_FLAG], sFlag, charsmax(sFlag));

	if( uFlags & read_flags(mFlag))
	{
		st = ST_MAIN_ADMIN;
		return formatex(status, len, "\y%L", id, "DB_STATUS_MAIN");
	}

	if( uFlags & read_flags(sFlag))
	{
		st = ST_SUB_ADMIN;
		return formatex(status, len, "\r%L", id, "DB_STATUS_SUB");
	}

	if( uFlags & ADMIN_BAN)
	{
		st = ST_ADMIN;
		return formatex(status, len, "\d%L", id, "DB_STATUS_ADMIN");
	}

	st = ST_UNKOWN;
	return formatex(status, len, "::Error::");
}
stock get_ban_id(const banid)
{
	new data[BanData];
	new size = ArraySize(g_ban_data);

	for(new i; i<size;i++)
	{
		ArrayGetArray(g_ban_data, i , data)

		if(data[BAN_ID] == banid)
			return i;
	}

	return -1;
}

stock Float:get_player_kick_time() {
	new Float:kickTime = get_pcvar_float(g_Cvars[CVAR_KICK_TIME]);

	if (g_web_ban) {
		kickTime = 4.0
	}

	return kickTime
}