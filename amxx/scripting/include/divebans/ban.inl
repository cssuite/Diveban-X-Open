#if defined _ban_inc
    #endinput
#endif

#define _ban_inc

#include <amxmodx>

/** 
	Forwards diveban_addban_post( const id, data[BannedData])
	Показ мотд окна после бана
	Кик игрока после бана
 */

stock const EMPTY_REASON[] = "empty_reason"

#define BAN_DEBUG

const BAN_STRING_LEN = 64;
const BAN_IP_LEN = 22

enum _:BannedData {

	/* Required Params */
	BD_BAN_NAME[BAN_STRING_LEN],
	BD_BAN_STEAM[BAN_IP_LEN],
	BD_BAN_IP[BAN_IP_LEN],
	BD_BAN_COOKIE[BAN_IP_LEN],
	BD_BAN_UID[BAN_IP_LEN],
	BD_BAN_DIVEID[BAN_IP_LEN],
	BD_BAN_CDKEY[33],

	/* Other */

	// Длительность бана в минутах
	BD_BAN_LEN,
	BD_BAN_REASON[BAN_STRING_LEN],

	// Дата бана и разбана
	BD_BAN_TIME,
	BD_BAN_UNBAN_TIME,

	BD_BAN_ADMIN_NAME[BAN_STRING_LEN],
	BD_BAN_ADMIN_ID[BAN_IP_LEN],

	BD_BAN_TYPE[BAN_IP_LEN],
	BD_BAN_SERVER[BAN_STRING_LEN],
	BD_BAN_SERVER_IP[BAN_IP_LEN],

	BD_BAN_ADMIN_PLAYER_ID,
	BD_BAN_BANTYPE,
	BD_BAN_PLAYER_ID

}

stock void:Ban_CheckApi(data[BannedData]) {
	// API
	Cache_RemoveByIP(data[BD_BAN_IP])

	// BanInfo
	if ( data[BD_BAN_PLAYER_ID] && is_user_connected(data[BD_BAN_PLAYER_ID]))	Ban_PrintBanInfo(data[BD_BAN_PLAYER_ID], data);

	// Sound
	client_cmd(0, "spk buttons/button5")
}

public API_ConvertToBannedData( data[BannedData], n[], s[], i[], cookie[], u[], d[], c[], minutes, reason[], banType[]) {

	copy(data[BD_BAN_NAME], BAN_STRING_LEN -1, n)
	copy(data[BD_BAN_STEAM], BAN_IP_LEN -1, s)
	copy(data[BD_BAN_IP], BAN_IP_LEN -1, i)
	copy(data[BD_BAN_COOKIE], BAN_IP_LEN -1, cookie)
	copy(data[BD_BAN_UID], BAN_IP_LEN -1, u)
	copy(data[BD_BAN_DIVEID], BAN_IP_LEN -1, d)
	copy(data[BD_BAN_CDKEY], 32, c)

	data[BD_BAN_LEN] = minutes;
	copy(data[BD_BAN_REASON], BAN_STRING_LEN -1, reason) 

	copy(data[BD_BAN_TYPE], BAN_STRING_LEN -1, banType)
}

public API_FillBannedData(data[BannedData], bantime, unbantime, admin_name[], admin_id[], server_name[], server_ip[]) {

	data[BD_BAN_TIME] = bantime;
	data[BD_BAN_UNBAN_TIME] = unbantime;

	copy(data[BD_BAN_ADMIN_NAME], BAN_STRING_LEN -1, admin_name)
	copy(data[BD_BAN_ADMIN_ID], BAN_STRING_LEN -1, admin_id)

	copy(data[BD_BAN_SERVER], BAN_STRING_LEN -1, server_name)
	copy(data[BD_BAN_SERVER_IP], BAN_STRING_LEN -1, server_ip)

}
public UnBan(id, unban_data[], len, bool:add_admin_id) {

	if (!license(id))
		return Print( id, "[BanCore] License Invalid");

	SQL_SafeString(unban_data, len)

	new admin_name[33], admin_id[33];
	Ban_GetMacroNameId(id, admin_name, charsmax(admin_name), admin_id, charsmax(admin_id))

	SQL_SafeString(admin_name, len)

	new AdminRow[64];
	if (add_admin_id)	formatex(AdminRow, charsmax(AdminRow), " %s <%s>", admin_name, admin_id)
	else				formatex(AdminRow, charsmax(AdminRow), " hidden_name <hidden_id>")

	new szTemp[512];
	formatex(szTemp, charsmax(szTemp),
	"UPDATE `%s` SET `unbantime` = '-1',`adminst` = '%s' WHERE (`steam` = '%s' OR `ip` = '%s' OR `uid` = '%s' OR `banname` = '%s' OR `name`='%s') AND (unbantime > '%d' OR unbantime = '0')",\
	szTable,AdminRow,unban_data,unban_data,unban_data,unban_data,unban_data,TimeGap + get_systime(0));
	
	PrintMessage("[UNBAN] Admin %s unban ID '%s'",AdminRow,unban_data)

	new Data[1]; Data[0] = id;
	return SQL_ThreadQuery(g_SqlTuple, "UnbanSQLResult", szTemp, Data, 1)

}
public Ban(id, data[BannedData], const szBanType) {
	data[BD_BAN_ADMIN_PLAYER_ID] = id;
	data[BD_BAN_BANTYPE] = szBanType

	Ban_CheckHistory(data);
	return 1;
}

public Ban_CheckHistory(data[BannedData]) {
	new query[QUERY_MAX_LEN]
	Ban_MakeQuery(data, query, charsmax(query))
	SQL_ThreadQuery(g_SqlTuple,"LoadBanHistory",query, data, sizeof data)
}

stock _Ban(data[BannedData]) {
	new id = data[BD_BAN_ADMIN_PLAYER_ID]
	new szBanType = data[BD_BAN_BANTYPE]

	if(!license(id))
		return Print( id, "[BanCore] License Invalid");

	// Required params
	if ( !Ban_CheckRequiredParams(data) ) {

		#if defined BAN_DEBUG
			Print(id, "Name: %s", data[BD_BAN_NAME])
			Print(id, "Steam: %s", data[BD_BAN_STEAM])
			Print(id, "Ip: %s", data[BD_BAN_IP])
			Print(id, "Cookie: %s", data[BD_BAN_COOKIE])
			Print(id, "Uid: %s", data[BD_BAN_UID])
			Print(id, "DiveId: %s", data[BD_BAN_DIVEID])
			Print(id, "CdKey: %s", data[BD_BAN_CDKEY])
		#endif

		return Print(id, "[BanCore] Invalid required params")
	}

	// Fixed
	Ban_FixBanInfo(id, data)

	// API 
	Ban_CheckApi(data)
	
	// BAN SQL Command
	Ban_addToSQL(data)

	new szBanMessage[64];

	switch (szBanType) {
		case 'b': formatex(szBanMessage, charsmax(szBanMessage), "ban");
		case 'd': formatex(szBanMessage, charsmax(szBanMessage), "offline ban")
		case 'e': formatex(szBanMessage, charsmax(szBanMessage), "delayed ban")
	}

	PrintMessage("%s<%s> %s %s<%s><%s> for <%d min> | reason <%s>", data[BD_BAN_ADMIN_NAME], data[BD_BAN_ADMIN_NAME], szBanMessage, data[BD_BAN_NAME],\
		data[BD_BAN_STEAM], data[BD_BAN_IP], data[BD_BAN_LEN], data[BD_BAN_REASON])

	new maxPlayers = get_maxplayers();

	if(!g_Color) {
		for(new i = 1; i< maxPlayers; i++) {
			if (!is_user_connected(i)) continue;
			Print(i, "%L",i, "DB_BAN_PLAYER_MESS", data[BD_BAN_ADMIN_NAME], data[BD_BAN_NAME], !data[BD_BAN_LEN] ? 10000000 : data[BD_BAN_LEN], data[BD_BAN_REASON])
		}
	}

	new prepareArray = PrepareArray(data, sizeof data)
	
	new ret;
	return ExecuteForward(g_forward[DB_BAN_BAN],ret,id, prepareArray, szBanType)
}

stock Ban_addToSQL(data[BannedData]) {

	new Query[1256],len;
	new bantime[12],unbantime[12]
	new mapname[36];
	get_mapname(mapname, charsmax(mapname))

	num_to_str(data[BD_BAN_TIME], bantime, charsmax(bantime));
	num_to_str(data[BD_BAN_UNBAN_TIME], unbantime, charsmax(unbantime));

	new cdKey[33];
	if ( containi(data[BD_BAN_CDKEY], "00000000") == -1)	copy(cdKey, charsmax(cdKey), data[BD_BAN_CDKEY])
	else													formatex(cdKey, charsmax(cdKey), "ErrorCD-Key")

	len = formatex(Query, charsmax(Query), "SET NAMES UTF8;INSERT INTO `%s` \
	(`banname`, `steam`, `ip`, `ipcookie`, `uid`, `diveid`, `cdkey`, \
	`bantime`, `unbantime`,`reason`,`name`,\
	`admin`,`adminip`,`time`, `bantype`,`Server`,`ServerIp`, `map`, `Bans_Kick`)",szTable)
	len += formatex(Query[len], charsmax(Query)- len," VALUES ")
	len += formatex(Query[len], charsmax(Query)- len," ('%s', '%s','%s', '%s','%s', '%s','%s', ",\
		data[BD_BAN_NAME], data[BD_BAN_STEAM],data[BD_BAN_IP],data[BD_BAN_COOKIE],data[BD_BAN_UID],data[BD_BAN_DIVEID], cdKey)

	len += formatex(Query[len], charsmax(Query)- len," '%s', '%s', '%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '0');",\
		bantime, unbantime, data[BD_BAN_REASON], data[BD_BAN_NAME],\
		data[BD_BAN_ADMIN_NAME],data[BD_BAN_ADMIN_ID],bantime,data[BD_BAN_TYPE],data[BD_BAN_SERVER],data[BD_BAN_SERVER_IP], mapname)

	#if defined BAN_DEBUG
		write_file("addons/amxmodx/data/db_sql.txt", Query)
	#endif

	return SQL_ThreadQuery(g_SqlTuple, "QuerySqlOK", Query);
}

stock Ban_FixBanInfo(id, data[BannedData]) {

	if ( !IsValidStr(data[BD_BAN_REASON]) ) {
		copy(data[BD_BAN_REASON], 63, "Empty reason")
	}

	data[BD_BAN_TIME] = TimeGap + get_systime(0);
	data[BD_BAN_UNBAN_TIME] = data[BD_BAN_LEN] == 0 ? 0 : (TimeGap + get_systime(0) + data[BD_BAN_LEN] * 60);

	if ( !IsValidStr(data[BD_BAN_ADMIN_NAME])) {
		Ban_GetMacroNameId(id, data[BD_BAN_ADMIN_NAME], BAN_STRING_LEN -1, data[BD_BAN_ADMIN_ID], BAN_IP_LEN - 1)
	}

	if ( !IsValidStr(data[BD_BAN_SERVER])) {
		Ban_GetMacroNameId(0, data[BD_BAN_SERVER], BAN_STRING_LEN -1, data[BD_BAN_SERVER_IP], BAN_IP_LEN - 1)
	}

	new szType[6];
	formatex(szType, charsmax(szType), "%c",g_ban_types[BAN_TYPE_AUTHID])
	if (!is_valid_steamid(data[BD_BAN_STEAM]))	Ban_FixInvalidType(data, szType)


	formatex(szType, charsmax(szType), "%c",g_ban_types[BAN_TYPE_CDKEY])
	if ( !data[BD_BAN_CDKEY][0]) Ban_FixInvalidType(data, szType)

	formatex(szType, charsmax(szType), "%c",g_ban_types[BAN_TYPE_DIVEID])
	if ( !data[BD_BAN_DIVEID][0]) Ban_FixInvalidType(data, szType)
}

stock Ban_PrintBanInfo( const id, data[BannedData]) {
	new filedata[256], ban_len[64],expired_time[64];
	new date_ban[64], date_unban[64];

	new size = ArraySize(g_con_mess)
	if(size <= 0) return;

	new systime= TimeGap + get_systime(0);

	// Осталось времени бана
	new exp_time = !data[BD_BAN_UNBAN_TIME] ? 0 : (data[BD_BAN_UNBAN_TIME]-systime) / 60;

	get_time_length(id,data[BD_BAN_LEN], timeunit_minutes, ban_len, sizeof(ban_len)-1)
	get_time_length(id,exp_time, timeunit_minutes, expired_time,sizeof(expired_time)-1)
	
	format_time(date_ban, charsmax(date_ban), "%d.%m.%Y - %H:%M:%S", data[BD_BAN_TIME])
	
	if(data[BD_BAN_UNBAN_TIME] > 0)
		format_time(date_unban, charsmax(date_unban), "%d.%m.%Y - %H:%M:%S", data[BD_BAN_UNBAN_TIME])
	else
		formatex(date_unban, charsmax(date_unban), "-------------")

	client_cmd(id,"echo ^"^"")
	
	for(new i; i< size; i++)
	{
		ArrayGetString(g_con_mess, i, filedata, sizeof(filedata) - 1)
		
		replace(filedata, charsmax(filedata), "%player_ip%", data[BD_BAN_IP])
		replace(filedata, charsmax(filedata), "%player_name%",data[BD_BAN_NAME])
		replace(filedata, charsmax(filedata), "%player_steamid%", data[BD_BAN_STEAM])
		replace(filedata, charsmax(filedata), "%admin_name%", data[BD_BAN_ADMIN_NAME])
		replace(filedata, charsmax(filedata), "%admin_id%",data[BD_BAN_ADMIN_ID])
		replace(filedata, charsmax(filedata), "%bantime%", ban_len)
		replace(filedata, charsmax(filedata), "%expired_time%", expired_time)
		replace(filedata, charsmax(filedata), "%reason%", data[BD_BAN_REASON])
		replace(filedata, charsmax(filedata), "%server_name%", data[BD_BAN_SERVER])
		replace(filedata, charsmax(filedata), "%server_id%", data[BD_BAN_SERVER_IP])
	
		replace(filedata, charsmax(filedata), "%date_of_ban%", date_ban)
		replace(filedata, charsmax(filedata), "%date_of_unban%", date_unban)

		replace(filedata, charsmax(filedata), "%plugin_name%", PLUGIN)
		replace(filedata, charsmax(filedata), "%plugin_version%", VERSION)

		replace_all(filedata,charsmax(filedata),"^n", "")
		replace_all(filedata,charsmax(filedata),"^r", "")
		client_cmd(id,"echo ^"%s^"",filedata)
	}
	
	client_cmd(id,"echo ^"^"")
}

stock Ban_MakeQuery( data[BannedData], szTemp[], tmp_len ) {
	new systime = get_systime()

	new max_effect_bantime = TimeGap + systime - get_pcvar_num(g_Cvars[CVAR_MAX_EFFECT_BANTIME])*300
	new cdkey_time = TimeGap + systime - get_pcvar_num(g_Cvars[CVAR_MAX_EFFECT_BANTIME])*45
	new sub_time = TimeGap + systime - get_pcvar_num(g_Cvars[CVAR_MAX_EFFECT_BANTIME])*120

	// Validate DivID
	new divid[32]; formatex(divid, charsmax(divid), "%s",data[BD_BAN_DIVEID]);

	// Validate CDKey
	new cdkey[34]; 	copy(cdkey, charsmax(cdkey), "InvalidCDKey")
	if ( strlen(data[BD_BAN_CDKEY])>=30 ) formatex(cdkey, charsmax(cdkey), "%s",data[BD_BAN_CDKEY]);

	// Validate Uid
	new uid[32]; 	copy(uid, charsmax(uid), "NOT_UID")
	if ( containi(data[BD_BAN_UID], "d") != -1 ) formatex(uid, charsmax(uid), "%s",data[BD_BAN_UID]);

	// Global Time Offset
	new szTimeOffset[128]; formatex(szTimeOffset, charsmax(szTimeOffset), "AND ((unbantime > '%d' OR unbantime='0') AND unbantime != '-1') ", TimeGap + systime);

	new szAuthid[128], szSteam[32]; copy(szSteam, charsmax(szSteam), "Error STEAM")
	if (is_valid_steamid(data[BD_BAN_STEAM]))	formatex(szSteam, charsmax(szSteam), "%s",data[BD_BAN_STEAM]);
	formatex(szAuthid, charsmax(szAuthid), "( steam='%s' AND bantype LIKE '%%%c%%' )", szSteam, Ban_GetBanType(BAN_TYPE_AUTHID))

	new szIp[128];
	formatex(szIp, charsmax(szIp), "(ip='%s' AND bantype LIKE '%%%с%%' AND `bantime` > '%d')",data[BD_BAN_IP], Ban_GetBanType(BAN_TYPE_IP), max_effect_bantime)

	new szCookie[128];
	formatex(szCookie, charsmax(szCookie), "( ipcookie LIKE '%%%s%%' AND `bantype` LIKE '%%%c%%' AND `bantime` > '%d')",data[BD_BAN_COOKIE], Ban_GetBanType(BAN_TYPE_COOKIE), max_effect_bantime)

	new szSubnet[256], subnet[BAN_IP_LEN]
	get_ip_subnet( 0, data[BD_BAN_IP], BAN_IP_LEN - 1, subnet, BAN_IP_LEN - 1);
	formatex(szSubnet, charsmax(szSubnet), "(( (ip like '%%%s%%' OR ipcookie LIKE '%%%s%%') AND bantype LIKE '%%%с%%')  AND `bantime` > '%d')", subnet, subnet, Ban_GetBanType(BAN_TYPE_SUBNET), sub_time)

	new szFullSubnet[256]
	get_ip_subnet( 1, data[BD_BAN_IP], BAN_IP_LEN - 1, subnet, BAN_IP_LEN - 1);
	formatex(szFullSubnet, charsmax(szFullSubnet), "( ( (ip like '%%%s%%' OR ipcookie LIKE '%%%s%%') AND bantype LIKE '%%%с%%') AND `bantime` > '%d')",subnet, subnet, Ban_GetBanType(BAN_TYPE_FULL_SUBNET), sub_time)

	new szUid[128];
	formatex(szUid, charsmax(szUid), "( uid='%s' AND bantype LIKE '%%%c%%' )",uid, Ban_GetBanType(BAN_TYPE_UID))

	new szCdKey[128];
	formatex(szCdKey, charsmax(szCdKey), "( cdkey='%s' AND bantype LIKE '%%%c%%' and `bantime` > '%d' )",cdkey, Ban_GetBanType(BAN_TYPE_CDKEY), cdkey_time)

	new szDivID[128];
	formatex(szDivID, charsmax(szDivID), " ( diveid='%s' AND bantype LIKE '%%%c%%' and `bantime` > '%d' )",divid, Ban_GetBanType(BAN_TYPE_DIVEID), cdkey_time)

	new len = formatex(szTemp,tmp_len,\
		" 	SELECT * FROM `%s` WHERE (\
		(\
			%s OR\
			%s OR\
			%s OR\
			%s OR\
			%s OR\
			%s OR\
			%s OR\
			%s\
		)", szTable,\
		szAuthid, szIp, szCookie, szSubnet, szFullSubnet, szUid, szCdKey, szDivID)

	len += formatex(szTemp[len], tmp_len- len, \
		"%s) ORDER BY `banid` DESC LIMIT 1",szTimeOffset)
}

stock Ban_FixInvalidType(data[BannedData], sBanType[]) {
	replace(data[BD_BAN_TYPE], BAN_STRING_LEN -1, sBanType, "")
	//return formatex(data[BD_BAN_STEAM], BAN_IP_LEN - 1, "STEAM_INVALID_%d%d%d%d", random_num('a', 'Z'), random_num('a', 'Z'), random_num('a', 'Z'), random_num('a', 'Z'))
}
stock Ban_GetMacroNameId( id,  server_name[], len, server_ip[], iplen) {

	new ip[22];
	get_user_authid(id, ip, charsmax(ip))
	copy(server_ip,iplen,ip)

	if (!id) {
		get_user_ip(id, ip, charsmax(ip), 1)
		copy(server_ip,iplen,ip)
		
		get_pcvar_string(g_Cvars[CVAR_SERVER_NAME],server_name,63)

		if(!server_name[0])	get_cvar_string("hostname",server_name,63)

	} else {
		new name[33];
		get_user_name(id, name, charsmax(name))

		copy(server_name, len, name)
	}

	SQL_SafeString(server_name,len)
	
}
stock bool:Ban_CheckRequiredParams( data[BannedData] ) {
	if ( 
		!data[BD_BAN_NAME][0] ||
		!data[BD_BAN_STEAM][0] ||
		!data[BD_BAN_IP][0] ||
		!data[BD_BAN_COOKIE][0] ||
		!data[BD_BAN_UID][0]
	) {
		return false;
	}

	return true;
}

stock Ban_GetBanType( const banTypeID ) {
	return g_ban_types[banTypeID];
}

stock GetBanType( iGet[], iLen )
{
	return format(iGet,iLen,"AIU%s%s", !get_pcvar_num(g_Cvars[CVAR_PREVENT_IP]) ? "S" : "", g_web_ban ? "C" : "");
}