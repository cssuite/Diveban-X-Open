#if defined _uid_included
    #endinput
#endif

#define _uid_included

#include <amxmodx>

new UidNames[] = "_uid"
const DIVEID_MAX_LEN = 16
const UID_MAX_LEN = 12

static const COMMAND[][] = { 
	"_cl_autowepswitch",
	"bottomcolor",
	"cl_dlmax",
	"cl_updaterate",
	"_pw",
	"topcolor",
	"rate"
};

get_user_diveid(id, divid[], len)
{
	if(is_user_bot(id) || is_user_hltv(id))
		return formatex(divid, len, "UNKOWN");
	
	new szString[128], str[22];
	for(new i; i<sizeof(COMMAND); i++)
	{
		get_user_info(id, COMMAND[i], str, charsmax(str))
		if(strlen(str) <= 0 || !str[0])	continue;
	
		formatex(szString, charsmax(szString), "%s|%s",szString, str)
	}
	
	add(szString, charsmax(szString),"db%d_#0$1",_:is_user_steam(id))
	
	new md5buf[34]
	md5(szString, md5buf)
	
	md5buf[10] = EOS;
	
	divid[0] = _:0;
	copy(divid, len, md5buf)
	return 1;
}

set_user_diveid(id)
{	
	get_user_diveid(id, g_DiveID[id], DIVEID_MAX_LEN-1)
	
	new skin[DIVEID_MAX_LEN];
	get_user_info(id,"skin",skin,charsmax(skin))
	
	if(!equal(g_DiveID[id], skin))
	{
		new temp[32];
		formatex(temp, sizeof(temp) -1,"%s ^"%s^"","skin",g_DiveID[id]);
		
		get_pcvar_num(g_Cvars[CVAR_CMD_TYPE]) ? SVC_DIRECTOR_STUFFTEXT_CMD(temp, id) : client_cmd(id, temp)
		if(get_pcvar_num(g_Cvars[CVAR_LOG_LEVEL]))
			PrintMessage("Player %s get DiveID [%s]. PrevValue [%s]", g_info_player[id][CS_PLAYER_NAME], g_DiveID[id], skin)
	}
	else 
		if(get_pcvar_num(g_Cvars[CVAR_LOG_LEVEL]))
			PrintMessage("Player %s authorized with DiveID [%s]", g_info_player[id][CS_PLAYER_NAME],g_DiveID[id])
}

stock bool:is_diveid_exists(id)
{
	new uid[DIVEID_MAX_LEN];
	get_user_diveid(id, uid, DIVEID_MAX_LEN-1)
	
	if(equal(uid, g_DiveID[id]))
		return true;
		
	return false
}

stock Create_UID(id)
{
	new uid[UID_MAX_LEN]
	format(uid, sizeof(uid) - 1,"%c%c%cD%c%c%c%c%c%c",random_num('A','Z'),random_num('a','z'),random_num('A','Z'),random_num('a','z'),random_num('A','Z'),random_num('A','Z'),random_num('A','Z'), random_num('a','z'), random_num('A','Z'))
	new temp[32];

	formatex(temp, sizeof(temp) -1,"setinfo %s ^"%s^"",UidNames,uid)		
	get_pcvar_num(g_Cvars[CVAR_CMD_TYPE]) ? SVC_DIRECTOR_STUFFTEXT_CMD(temp, id) : client_cmd(id, temp)
	
	copy(g_info_player[id][CS_PLAYER_UID], sizeof(uid)-1,uid)
	
	if(get_pcvar_num(g_Cvars[CVAR_LOG_LEVEL]))
		PrintMessage("Player ^"%s^" get UID ^"%s^"", g_info_player[id][CS_PLAYER_NAME],g_info_player[id][CS_PLAYER_UID])
}

stock is_uniqueid_exists(id)
{
	new str[UID_MAX_LEN]
	
	get_user_info(id, UidNames ,str, sizeof(str) - 1)
	
	if(equali(str, g_info_player[id][CS_PLAYER_UID]))
		return 1; // UID is normal
		
	return 0;
}
stock get_user_uniqueid(id,uid[],len)
{
	get_user_info(id,UidNames,uid,len)

	if(!uid[0] || contain(uid,"D") == -1)
		formatex(uid,len,"UNKOWN")
}
stock Replace_Uid(id)
{
	new temp[32]
	formatex(temp,31,"setinfo %s ^"%s^"", UidNames, g_info_player[id][CS_PLAYER_UID])
	get_pcvar_num(g_Cvars[CVAR_CMD_TYPE]) ? SVC_DIRECTOR_STUFFTEXT_CMD(temp, id) : client_cmd(id, temp)
}
