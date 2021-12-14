#if defined _cmd_disc_ban_inc
    #endinput
#endif

#define _cmd_disc_ban_inc

#include <amxmodx>

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
	data[dd_uid], data[dd_diveid], data[dd_cdkey], minutes, reason, bantype)

	return Ban(id, _data, 'd')
}