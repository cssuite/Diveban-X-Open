#if defined _add_ban_inc
    #endinput
#endif

#define _add_ban_inc

#include <amxmodx>

stock AddPlayerBan(id, player, time, reason[]) {

	#if defined DIVEBAN_CORE_DEBUG
		PrintMessage("[AddPlayerBan] ID: %d, player %d, minutes %d, reason %s", id, player, time, reason)
	#endif

	if(get_bit(g_being_banned, player)) {
		PrintMessage("[DoubleBan] Block double ban for %d", player)
		return Print(id,"%L", id, "DB_ERROR_DOUBLEBAN",g_info_player[player][CS_PLAYER_NAME])
	}
		
	set_bit(g_being_banned, player)

	new data[BanData];
	data[BAN_ID] = random(10000);
	data[BAN_ADMIN_ID] = id;
	data[BAN_PLAYER_ID] = player;
	data[BAN_TIME] = abs(time);
	copy(data[BAN_REASON], 63, reason);

	ArrayPushArray(g_ban_data, data);

	set_task( random_float(1.5, 2.5), "TaskAddBan", data[BAN_ID] + TASK_BAN_PLAYER);
	return PLUGIN_HANDLED;
}

public TaskAddBan ( banid )
{
	if (banid > TASK_BAN_PLAYER)	banid -= TASK_BAN_PLAYER;

	new data[BanData],ban_id = get_ban_id(banid);
	ArrayGetArray(g_ban_data, ban_id, data);

	if ( (!is_user_connected(data[BAN_ADMIN_ID]) && data[BAN_ADMIN_ID > 0] ) || !is_user_connected(data[BAN_PLAYER_ID])) return;

	#if defined DIVEBAN_CORE_DEBUG
		PrintMessage("[TaskAddBan] ID: %d, player %d, minutes %d, reason %s", data[BAN_ADMIN_ID], data[BAN_PLAYER_ID], data[BAN_TIME], data[BAN_REASON])
	#endif

	Cmd_BanPlayer(data[BAN_ADMIN_ID],data[BAN_PLAYER_ID], data[BAN_TIME],data[BAN_REASON])
}


stock Cmd_BanPlayer( id,  player,  minutes, reason[]) {

	#if defined DIVEBAN_CORE_DEBUG
		PrintMessage("[Cmd_BanPlayer] ID: %d, player %d, minutes %d, reason %s", id, player, minutes, reason)
	#endif

	new bantype[36]
	
	if(g_bantype_option[id][BT_ON])		copy(bantype, 35, g_bantype_option[id][BT_TYPE])
	else								GetBanType( bantype,charsmax(bantype))

	new data[BannedData];
	API_ConvertToBannedData(data, g_info_player[player][CS_PLAYER_NAME], g_info_player[player][CS_PLAYER_ID],g_info_player[player][CS_PLAYER_IP], g_info_player[player][CS_PLAYER_IP],\
	g_info_player[player][CS_PLAYER_UID], g_DiveID[player], g_iCDKey[player], minutes, reason, bantype)

	data[BD_BAN_PLAYER_ID] = player;

	Ban(id, data, 'b')

	set_task(1.0, "TaskBannedPost", player +33313, data, sizeof(data))
}

public TaskBannedPost(data[BannedData], id) {
	if (id > 33313) {
		id -= 33313
	}

	ShowMotd(id,data[BD_BAN_REASON], data[BD_BAN_TIME],data[BD_BAN_ADMIN_NAME])
	set_task( get_pcvar_float(g_Cvars[CVAR_DELAY_TIME]), "TaskKickPlayer", id+TASK_KICK_PLAYER);
}
