#if defined _csbans_inc
    #endinput
#endif

#define _csbans_inc

#include <amxmodx>
#include <sqlx>

csbans_check_player(id, Handle:Query)
{
	new szQuery[256];
	
	new prefix[16];
	get_pcvar_string(g_Cvars[CVAR_CSBANS_PREFIX], prefix, charsmax(prefix))
	
	new SteamID[32];
	new Ip[26];
	
	new bid = SQL_ReadResult(Query, 0);
	SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"steam"), SteamID, charsmax(SteamID));
	SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"ip"), Ip, charsmax(Ip));
	
	formatex(szQuery, charsmax(szQuery), "SELECT * FROM `%s_bans` WHERE (`player_id` = '%s' OR `player_ip` = '%s') and `expired`=0 ORDER by `bid` DESC LIMIT 1",\
	prefix, SteamID, Ip);
	
	new data[2]; data[0] = id; data[1] = bid;
	SQL_ThreadQuery(g_SqlTuple, "SQl_CSCheckPlayer", szQuery, data, 2);
}
public SQl_CSCheckPlayer(failstate, Handle:query, const error[], errornum, const data[], size, Float:queuetime)
{
	if (failstate)
	{
		return SQL_Error(query, error, errornum, failstate);
	}

	new id = data[0];
	new bid = data[1];
	if ( !SQL_NumResults(query) )
	{
		remove_task(id+13131)
		
		new szQuery[256];
		formatex(szQuery, charsmax(szQuery), "UPDATE `%s` SET `unbantime`='-1' WHERE `banid`='%d'",\
		szTable, bid);
		SQL_ThreadQuery(g_SqlTuple, "SQl_CSAddServer", szQuery);
		return SQL_FreeHandle(query);
	}
	
	set_task(get_pcvar_float(g_Cvars[CVAR_KICK_TIME]),"TaskKickPlayer", id+TASK_KICK_PLAYER);
	
	return SQL_FreeHandle(query);
}

CheckCSBanTable() {
	new szQuery[128];
	
	new prefix[16];
	get_pcvar_string(g_Cvars[CVAR_CSBANS_PREFIX], prefix, charsmax(prefix))
	
	formatex(szQuery, charsmax(szQuery), "SHOW TABLES LIKE '%s_serverinfo'", prefix);
	
	SQL_ThreadQuery(g_SqlTuple, "SQl_CSCheckTable", szQuery);
}
public SQl_CSCheckTable(failstate, Handle:query, const error[], errornum, const data[], size, Float:queuetime)
{
	if (failstate)
	{
		return SQL_Error(query, error, errornum, failstate);
	}
	
	if (SQL_NumResults(query) )	AddCSBanstoServer()

	return SQL_FreeHandle(query);
}
AddCSBanstoServer()
{
	new szQuery[128];
	
	new prefix[16];
	get_pcvar_string(g_Cvars[CVAR_CSBANS_PREFIX], prefix, charsmax(prefix))
	
	formatex(szQuery, charsmax(szQuery), "SELECT `motd_delay` FROM `%s_serverinfo` WHERE `address`='%s'", prefix, server_ipaddr);
	
	SQL_ThreadQuery(g_SqlTuple, "SQl_CSCheckServ", szQuery);
}
public SQl_CSCheckServ(failstate, Handle:query, const error[], errornum, const data[], size, Float:queuetime)
{
	if (failstate)
	{
		return SQL_Error(query, error, errornum, failstate);
	}
	
	new prefix[16];
	get_pcvar_string(g_Cvars[CVAR_CSBANS_PREFIX], prefix, charsmax(prefix))

	new szQuery[512];
	
	new szIp[32], szPort[16];
	get_user_ip(0, szIp, charsmax(szIp))
	
	strtok(szIp, szIp, charsmax(szIp), szPort, charsmax(szPort), ':');

	new modname[32];
	get_modname(modname, charsmax(modname))
	
	new csbans_support[32];
	formatex(csbans_support, charsmax(csbans_support), "%s-%s",PLUGIN, VERSION);
	
	if ( !SQL_NumResults(query) )	formatex(szQuery, charsmax(szQuery),"INSERT INTO `%s_serverinfo` (timestamp, hostname, address, gametype, amxban_version, amxban_menu) VALUES \
			(%i, '%s', '%s:%s', '%s', '%s', 0)", prefix, get_systime(0), hostname, szIp, szPort, modname, csbans_support)
	else	formatex(szQuery, charsmax(szQuery), "UPDATE `%s_serverinfo` SET timestamp='%i',hostname='%s',gametype='%s',amxban_version='%s', amxban_menu='0' WHERE address = '%s:%s'", prefix, get_systime(0), hostname, modname, csbans_support, szIp, szPort)
	
	SQL_ThreadQuery(g_SqlTuple, "SQl_CSAddServer", szQuery);
	return SQL_FreeHandle(query);
}
public SQl_CSAddServer(failstate, Handle:query, const error[], errornum, const data[], size, Float:queuetime)
{
	if (failstate)
	{
		return SQL_Error(query, error, errornum, failstate);
	}
	
	return SQL_FreeHandle(query);
}
