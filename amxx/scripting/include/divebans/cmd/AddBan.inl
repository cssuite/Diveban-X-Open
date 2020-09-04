#if defined _cmd_ban_inc
    #endinput
#endif

#define _cmd_ban_inc

#include <amxmodx>

public CmdAddBan(id) {
	new target[MAX_IP_LENGHT], minutes[MAX_IP_LENGHT], reason[64];

	read_argv(1, minutes, sizeof(minutes)-1) 
	read_argv(2, target,  sizeof(target) -1)
	read_argv(3, reason,  sizeof(reason) -1)

	new time = Api_ArgParse_Time( API_ARG_FIRST );
	new player = Api_ArgParse_Player( API_ARG_SECOND );

	if( player) {
		return AddPlayerBan(id, player, time, reason);
	}

	new discID = find_disconnect_ban(target)

	if ( discID != -1) {
		return AddDisconnectBan(id, discID, time, reason)
	}


	new data[DelayedData];
	new res = Delayed_Find(data, target)

	if (res) {
		Print(id, "Can't find player by %s", target)
	}

	Delayed_BanAdd(id, data, time, reason)
	return 1;

}

/* db_fban */
public CmdFastBan(id, level, cid)
{
	CmdAddBan(id)
	return PLUGIN_HANDLED;
}

/** 
 * Бан из главного меню
 */
public CmdMenuBan(id, player, minutes, reason[])
{
	CmdAddBan(id)
	return ExecuteForward(g_forward[DV_BAN_PRE],ret,data[BAN_ADMIN_ID],data[BAN_PLAYER_ID],data[BAN_TIME],data[BAN_REASON]);
}

stock AddPlayerBan(id, player, time, reason[]) {
	if(get_bit(g_being_banned, player) || g_player_banned[player])
		return Print(id,"%L", id, "DB_ERROR_DOUBLEBAN",g_info_player[player][CS_PLAYER_NAME])

	set_bit(g_being_banned, player)

	new data[BanData];
	data[BAN_ID] = random(10000);
	data[BAN_ADMIN_ID] = id;
	data[BAN_PLAYER_ID] = player;
	data[BAN_TIME] = abs(time);
	copy(data[BAN_REASON], 63, reason);

	ArrayPushArray(g_ban_data, data);

	if(task_exists( player + TASK_CHECK_PLAYER) || task_exists( player + TASK_KICK_PLAYER ) && get_pcvar_num(g_Cvars[CVAR_CHECK_BAN]) == 2)
			set_task( get_pcvar_float(g_Cvars[CVAR_CHECK_TIME]) + get_pcvar_float(g_Cvars[CVAR_KICK_TIME]) + random_float(1.0,3.5), "TaskAddBan", data[BAN_ID] + TASK_BAN_PLAYER);
	else	TaskAddBan(data[BAN_ID])

	return 1;
}
stock Cmd_BanPlayer( id,  player,  minutes, reason[]) {

	new bantype[36]
	
	if(g_bantype_option[id][BT_ON])		copy(bantype, 35, g_bantype_option[id][BT_TYPE])
	else								GetBanType( bantype,charsmax(bantype))

	new data[BannedData];
	API_ConvertToBannedData(data, g_info_player[player][CS_PLAYER_NAME], g_info_player[player][CS_PLAYER_ID],g_info_player[player][CS_PLAYER_IP], g_info_player[player][CS_PLAYER_IP],\
	g_info_player[player][CS_PLAYER_UID], g_DiveID[player], g_iCDKey[player], minutes, reason, bantype)

	Ban(id, data, "ban")
	Ban_PrintBanInfo(player, data);

	set_task(1.0, "TaskBannedPost", player +33313, data, sizeof(data))

}
stock AddDelayedBan(id, data[DelayedData], minutes, reason[]) {
	new bantype[36]

	if (szBanType[0])			copy(bantype, 35, szBanType)
	else						GetBanType( bantype,charsmax(bantype))

	new _data[BannedData];
	API_ConvertToBannedData(_data, data[DD_DELAYED_NAME], data[DD_DELAYED_STEAM], data[DD_DELAYED_IP], data[DD_DELAYED_IP],\
		data[DD_DELAYED_UID], data[DD_DELAYED_DIVEID], data[DD_DELAYED_CDKEY], minutes, reason, bantype)

	Ban(id, _data, "delayed ban")
}
stock AddDisconnectBan(id,playerID, minutes, reason[], sBantype[] = "") {
	new data[DiscData]
	ArrayGetArray(g_array, playerID, data)

	if ( data[dd_is_ban] ) // Already Banned
		return Print(id,"Player %s already banned", data[dd_name])

	if ( !has_player_access_flag(id, playerID, .disconnect = true) )
		return Print(id, "Player %s has immune",data[dd_name])

	new bantype[36]
	if(g_bantype_option[id][BT_ON])		copy(bantype, 35, g_bantype_option[id][BT_TYPE])
	else if (sBantype[0])				copy(bantype, 35, sBantype)
	else								GetBanType( bantype,charsmax(bantype))

	new _data[BannedData];
	API_ConvertToBannedData(_data, data[dd_name], data[dd_steamid],data[dd_ip], data[dd_ip],\
	data[dd_uid], dd_cdkey[dd_diveid], data[dd_cdkey], minutes, reason, bantype)

	Ban(id, _data, "ban")
}

public TaskAddBan ( banid )
{
	if (banid > TASK_BAN_PLAYER)	banid -= TASK_BAN_PLAYER;
	new data[BanData],ban_id = get_ban_id(banid);

	ArrayGetArray(g_ban_data, ban_id, data);

	if ( (!is_user_connected(data[BAN_ADMIN_ID]) && data[BAN_ADMIN_ID > 0] ) || !is_user_connected(data[BAN_PLAYER_ID])) return;

	new check_player = get_pcvar_num(g_Cvars[CVAR_CHECK_BAN]);

	switch (check_player)
	{
		case 1: 	CheckBan(banid);
		default: 	Cmd_BanPlayer(data[BAN_ADMIN_ID],data[BAN_PLAYER_ID], data[BAN_TIME],data[BAN_REASON])
	}

}