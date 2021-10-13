#if defined _checkplayer_included
    #endinput
#endif

#define _checkplayer_included
 
#include <amxmodx>
#include <sqlx>

public TaskCheckPlayer(id)
{	
	if(!license(0))	return PLUGIN_CONTINUE;

	if ( id > TASK_CHECK_PLAYER )	id -= TASK_CHECK_PLAYER;

	bCook[id] = false;
	g_player_banned[id] = false;
	
	set_user_diveid(id)
	get_user_uniqueid(id,g_info_player[id][CS_PLAYER_UID],25)
	
	if(get_pcvar_num(g_Cvars[CVAR_LOG_LEVEL]))
		PrintMessage("Connect ^"%s^" ( SteamID ^"%s^", IP ^"%s^", DivID ^"%s^")", g_info_player[id][CS_PLAYER_NAME],\
				 g_info_player[id][CS_PLAYER_ID],g_info_player[id][CS_PLAYER_IP], g_DiveID[id])
	
	SetCDKey(id); 

	if ( Cache_IsPlayerCached(id) ) {
		//TrieDeleteKey(gTrieCache, g_info_player[id][CS_PLAYER_IP])
		PrintMessage("[Cache] Skip cached player %s [%s]", g_info_player[id][CS_PLAYER_NAME], g_info_player[id][CS_PLAYER_IP])

		PlayerReady(id, false)
		return PLUGIN_CONTINUE;
	}

	new data[BannedData];
	API_ConvertToBannedData(data, 
		g_info_player[id][CS_PLAYER_NAME], 
		g_info_player[id][CS_PLAYER_ID], 
		g_info_player[id][CS_PLAYER_IP], 
		g_info_player[id][CS_PLAYER_IP],
		g_info_player[id][CS_PLAYER_UID],
		g_DiveID[id],
		g_iCDKey[id],
		-1,
		"",
		""
		)

	data[BD_BAN_ADMIN_PLAYER_ID] = id; // PlayerID

	new query[QUERY_MAX_LEN]
	Ban_MakeQuery(data, query, charsmax(query))

	SQL_ThreadQuery(g_SqlTuple,"LoadDataPlayer",query, data, sizeof data)
	
	return PLUGIN_CONTINUE;	
}

PlayerReady(id, bool:ban = false) {
	if(!is_uniqueid_exists(id))
		Create_UID(id);

	if ( !ban) {
		set_bit(g_ready, id);
		Cache_AddPlayer(id);
	}

	RefreshDisconnect(id)
	Delayed_AddPlayer(id)
	player_ban_count(id)
}

stock CheckDataPlayer(data[BannedData], Handle:Query) {
	new id = data[BD_BAN_ADMIN_PLAYER_ID]

	if(SQL_NumResults(Query) >0)
	{
		new systime= TimeGap + get_systime(0)
		new szUnbanTime[15],szBanTime[12], iBanTime, iUnbanTime

		SQL_ReadResult(Query,SQL_FieldNameToNum(Query,"unbantime"), szUnbanTime, 14);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"bantime"), szBanTime, 11);
		
		iBanTime = str_to_num(szBanTime);
		iUnbanTime = str_to_num(szUnbanTime);
		g_player_banned[id] = true;
			
		new iUid = SQL_ReadResult(Query, SQL_FieldNameToNum(Query, "banid"));
			
		new reason[64],ip[26],admin_name[32],admin_id[20],server[32],szTemp[512]
		
		new last_name[26];
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"name"), last_name,sizeof(last_name) -1);
		
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"reason"), reason, charsmax(reason));
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"admin"), admin_name, charsmax(admin_name));
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"adminip"), admin_id, charsmax(admin_id));
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"Server"), server, charsmax(server));
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"ServerIp"), ip, charsmax(ip));
		
		new label[34];
		get_player_marker(id, Query, label, charsmax(label));
		
		if(!g_Color)
		client_print(0,print_chat,"[%s] %L",PREFIX, LANG_PLAYER, "DB_IN_BANLIST_KICK",\
			data[BD_BAN_NAME],data[BD_BAN_STEAM])
				 
		PrintMessage("%s [%s] KICKED for [%s] | Marker:%s",data[BD_BAN_NAME],\
			last_name, reason, label)
		
		new ret;
		ExecuteForward(g_forward[DB_KICK_PRE],ret,id,admin_name,reason);
		
		formatex(szTemp,charsmax(szTemp),
			"SET NAMES UTF8;UPDATE `%s` SET `time` = '%d', `name` = '%s', `Bans_Kick` = `Bans_Kick` + 1 WHERE `banid`='%d'",\
			szTable,systime,g_info_player[id][CS_PLAYER_NAME], iUid)

		data[BD_BAN_LEN] = (!iBanTime || !iUnbanTime) ?  0 : abs((iBanTime - iUnbanTime) /60);
		copy(data[BD_BAN_REASON], 63, reason)
		API_FillBannedData(data, iBanTime, iUnbanTime, admin_name, admin_id, server, ip)
	
		Ban_PrintBanInfo(id, data);
		SQL_ThreadQuery(g_SqlTuple,"QuerySqlOK",szTemp)
		
		//if(get_pcvar_num(g_Cvars[CVAR_CSBANS_SUPPORT]))	      	csbans_check_player(id, Query)
		//else							set_task(get_pcvar_float(g_Cvars[CVAR_KICK_TIME]),"TaskKickPlayer", id+TASK_KICK_PLAYER)

		set_task(get_player_kick_time(),"TaskKickPlayer", id+TASK_KICK_PLAYER)

		DeleteDiscBan(id);
		Cache_RemovePlayer(id);
	}
	
	PlayerReady(id, g_player_banned[id])
}

public TaskKickPlayer(id)
{
	if(id > TASK_KICK_PLAYER) 
		id -=TASK_KICK_PLAYER;
	
	if(id > 200)
		id -=200;
	
	if(is_user_connected(id))
	{	
		Cache_RemovePlayer(id);
		
		if(get_pcvar_num(g_Cvars[CVAR_LOG_LEVEL]))
			PrintMessage("[ACTION] Kick Player %s <%d>",g_info_player[id][CS_PLAYER_NAME],get_user_userid(id))
			
		server_cmd("kick #%d ^"You have been banned, check console^"",get_user_userid(id))
	} else {
		PrintMessage("[ACTION] Kick <%d> Failed...", id)
	}

	
}
