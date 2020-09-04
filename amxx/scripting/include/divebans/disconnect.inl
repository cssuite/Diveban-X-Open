#if defined _disconnect_included
    #endinput
#endif

#define _disconnect_included

#include <amxmodx> 

static tmpFile[] = "DisconnectPlayers.dbx";

new saveFrom = 0;

SaveDisconnectBans() {
	
	new size = ArraySize(g_array)
	new data[DiscData];

	new file[64]; getDirByType( DirData:DIR_DATA, file, charsmax(file), tmpFile)

	new f = fopen( file, "w")
	if ( !f ) 
		return

	for(new i = saveFrom; i< size; i++) {
		ArrayGetArray(g_array, i, data)

		fprintf(f, "^"%s^" ^"%s^" ^"%s^" ^"%s^" ^"%s^" ^"%s^" ^"%d^" ^n", 
			data[dd_name], data[dd_steamid], data[dd_ip], data[dd_uid], data[dd_cdkey], data[dd_diveid], data[dd_immune_flag])
	}

	fclose(f)
}

LoadDisconnectBans() {
	new file[64]; getDirByType( DirData:DIR_DATA, file, charsmax(file), tmpFile)

	new f = fopen( file, "r")

	if ( !f ) return

	new fData[256]
	new data[DiscData];
	new immune_flag[6]

	while( !feof(f) ) {
		fgets( f, fData, charsmax(fData) );

		if ( !fData[0] || fData[0] != '"' )
			continue;

		parse( fData, data[dd_name], 31, data[dd_steamid], MAX_IP_LENGHT-1, data[dd_ip], MAX_IP_LENGHT-1, data[dd_uid], 15,
			data[dd_cdkey], 32, data[dd_diveid], 15, immune_flag, 5)

		data[dd_immune_flag] = str_to_num(immune_flag)
		data[dd_is_ban] = false;

		ArrayPushArray(g_array, data)
		TrieSetCell(g_trie_id, data[dd_steamid], ArraySize(g_array)-1);
		TrieSetCell(g_trie_ip, data[dd_ip], ArraySize(g_array)-1);
	}

	saveFrom = ArraySize(g_array)

	fclose(f)
}

DisconnectSave_Connect(id)
{
	if(getDisconnectID(id) > -1) return;		
	
	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	if( st == UserStatus:ST_MAIN_ADMIN) return;

	new data[DiscData];
	copy(data[dd_name],31,g_info_player[id][CS_PLAYER_NAME]);

	copy(data[dd_steamid], 21, g_info_player[id][CS_PLAYER_ID]);
	copy(data[dd_ip], MAX_IP_LENGHT - 1, g_info_player[id][CS_PLAYER_IP]);
	copy(data[dd_uid], 15, g_info_player[id][CS_PLAYER_UID]);
	
	new flags[4];
	get_pcvar_string(g_Cvars[CVAR_SUB_FLAG], flags, charsmax(flags));

	if ( get_user_flags(id) & read_flags(flags) && strlen(flags) > 0 )
		data[dd_immune_flag] = 2;
	else if ( get_user_flags(id) & ADMIN_IMMUNITY)
		data[dd_immune_flag] = 3;

	data[dd_is_ban] = false;

	ArrayPushArray(g_array, data);

	TrieSetCell(g_trie_id, g_info_player[id][CS_PLAYER_ID], ArraySize(g_array)-1);
	TrieSetCell(g_trie_ip, g_info_player[id][CS_PLAYER_IP], ArraySize(g_array)-1);

	PrintMessage("[Offline] Connect player %s", data[dd_name])
}
RefreshDisconnect(id)
{
	new playerID = getDisconnectID(id)
	
	if(playerID == -1 || playerID >= ArraySize(g_array))
		return;

	new data[DiscData];
	ArrayGetArray(g_array,playerID,data)
	
	if ( strlen(g_iCDKey[id]) <= 5 || strlen(g_DiveID[id]) <= 5) {
		PrintMessage("[Offline] Fail Update %s [CDkey %s][DivID %s]", data[dd_name], g_iCDKey[id], g_DiveID[id])
	}

	copy(data[dd_cdkey],32,g_iCDKey[id])
	copy(data[dd_uid], 15, g_info_player[id][CS_PLAYER_UID]);
	copy(data[dd_diveid], 15, g_DiveID[id]);

	new flags[4];
	get_pcvar_string(g_Cvars[CVAR_SUB_FLAG], flags, charsmax(flags));

	if ( get_user_flags(id) & read_flags(flags) && strlen(flags) > 0 )
		data[dd_immune_flag] = 2;
	else if ( get_user_flags(id) & ADMIN_IMMUNITY)
		data[dd_immune_flag] = 3;

	ArraySetArray(g_array, playerID, data)
	PrintMessage("[Offline] Update player %s", data[dd_name])
}
/* Return ID of disc ban */
find_disconnect_ban( const ID[] )
{
	new data[DiscData]
	new size = ArraySize(g_array)

	for(new i; i< size; i++)
	{
		ArrayGetArray(g_array, i, data)

		if( equali(data[dd_ip],ID) || equali(data[dd_steamid], ID) )
			return i
	}

	return -1
}

public CmdDisconnectMenu(id)
{
	if(!license(id)) return Print(id, "Invalid License");
		
	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	if( st < ST_ADMIN)
		return Print(id,"%L", id, "DB_NO_ACCESS"); 
	
	new size = ArraySize(g_array)

	if ( size <= 0 )
		return Print(id, "Disconnect menu is empty")
		
	new title[86]; formatex(title, sizeof(title) - 1, "%L", id, "DB_MENU_OFFBAN_TITLE")
	new menu = menu_create(title,"Handler")
	MenuSetProps(id, menu)
	
	new data[DiscData],text[64], int[6];

	while(--size >= 0)
	{
		num_to_str(size,int,5)
		ArrayGetArray(g_array, size, data)

		formatex(text,63,"\w%s %s",data[dd_name], get_player_online( data[dd_steamid], data[dd_ip]) ? "[\yOnline\w]" : " ")
		menu_additem(menu, text,int)
	}
		
	return menu_display(id,menu,0)
}
public Handler(id, menu, item)
{
	if( item == MENU_EXIT )
		return menu_destroy(menu);
	
	new datas[6], iName[2];
	new access, callback;
	menu_item_getinfo(menu, item, access, datas,5, iName, 1, callback);
	
	set_bit(g_disc_ban, id)
	g_struct_array[id][PLAYERID] = str_to_num(datas)
	
	if(get_bit(g_in_confirm, id))
		MenuBeforeBan(id)
	else
		MenuBanReason(id)
		
	return menu_destroy(menu);
}
DeleteDiscBan( const id) 
{
	new playerID = getDisconnectID(id);

	new size = ArraySize(g_array)
	if ( playerID == -1 || size <= 0 || playerID >= size)	return;

	ArrayDeleteItem(g_array, playerID);
	TrieDeleteKey(g_trie_id, g_info_player[id][CS_PLAYER_ID]);
	TrieDeleteKey(g_trie_ip, g_info_player[id][CS_PLAYER_IP]);
}

/* Возвращает игрока если он онлайн */
stock bool:get_player_online(const SteamID[], const IP[])
{	
	return find_player( "c", SteamID) || find_player( "d", IP);
}

/* Возвращает ID disc бана */
stock getDisconnectID(id = -1)
{	
	new array_pos;

	if(TrieGetCell(g_trie_id, g_info_player[id][CS_PLAYER_ID], array_pos))
		return array_pos;
	
	else if (TrieGetCell(g_trie_ip,g_info_player[id][CS_PLAYER_IP], array_pos))
		return array_pos;
		
	return -1;
} 

stock discBansToLog() {
	new data[DiscData];
	new size = ArraySize(g_array)

	for(new i; i< size; i++) {
		ArrayGetArray(g_array, i, data)

		PrintMessage("[Online] #%i %s", i, data[dd_name])
	}
}

stock getDisconnectBanByName( name[] ) {
	new data[DiscData];
	new size = ArraySize(g_array)

	for(new i; i< size; i++) {
		ArrayGetArray(g_array, i, data)

		if (equali(data[dd_name], name) )
			return i;
	}

	return -1;
}