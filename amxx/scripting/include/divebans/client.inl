#if defined _client_included
    #endinput
#endif

#define _client_included

#include <amxmodx>
#include <sqlx>
 
public client_connect(id)
{
	clear_bit(g_ready, id);
	clear_bit(g_being_banned, id);

	for(new i; i< StructArray; i++) g_struct_array[id][i] = 0;

	new name[26];
 	get_user_authid(id, g_info_player[id][CS_PLAYER_ID], 25);
	get_user_ip(id, g_info_player[id][CS_PLAYER_IP], 25,1);
	get_user_uniqueid(id,g_info_player[id][CS_PLAYER_UID],25)
	
	get_user_name(id, name, sizeof(name) - 1);
	MakeStringSQLSafe(name,g_info_player[id][CS_PLAYER_NAME], 25)
	
	arrayset(g_iCDKey[id], '0', 31)
	g_iCDKey[id][32] = EOS;
	
	DisconnectSave_Connect(id)
}
public client_putinserver(id)
{
	clear_bit(g_being_banned, id);
	clear_bit(g_disc_ban, id);
	clear_bit(g_in_confirm, id);
	
	g_bantype_option[id][BT_ON] = false;
	g_bantype_option[id][BT_TYPE][0] = '^0'; 

	if ( get_pcvar_float(g_Cvars[CVAR_CHECK_TIME]) >= 1.0 ) set_task( get_pcvar_float(g_Cvars[CVAR_CHECK_TIME]), "TaskCheckPlayer",id+TASK_CHECK_PLAYER);
	else	TaskCheckPlayer(id)
	
	return PLUGIN_CONTINUE;
}
SetCDKey(id)
{
	new pClientUserInfo = engfunc(EngFunc_GetInfoKeyBuffer, id);
       
	for (new i = 0; i < 32; i += 4)
	{
        new ch = get_tr2(pClientUserInfo + offs + i, TR_AllSolid);
               
        g_iCDKey[id][i]     =  ch        & 0xFF;
        g_iCDKey[id][i + 1] = (ch >> 8)  & 0xFF;
        g_iCDKey[id][i + 2] = (ch >> 16) & 0xFF;
        g_iCDKey[id][i + 3] = (ch >> 24) & 0xFF;
	}
	
	g_iCDKey[id][32] = EOS;

	new tmp[34];
	md5(g_iCDKey[id], tmp);

	g_iCDKey[id][0] = '^0';
	copy(g_iCDKey[id], charsmax(g_iCDKey[]), tmp);
}
public client_disconnect(id)
{
	if(task_exists(id))
		remove_task(id)
		
	if(task_exists(id+200))
		remove_task(id+200)
		
	if(task_exists(id+TASK_CHECK_PLAYER))
		remove_task(id+TASK_CHECK_PLAYER)

	if(task_exists(id+TASK_KICK_PLAYER))
		remove_task(id+TASK_KICK_PLAYER)

	if(task_exists(id+TASK_BAN_PLAYER))
		remove_task(id+TASK_BAN_PLAYER)

	if(get_bit(g_being_banned, id))
	{
		clear_bit(g_being_banned, id);
	}
	
	RefreshDisconnect(id)
}
