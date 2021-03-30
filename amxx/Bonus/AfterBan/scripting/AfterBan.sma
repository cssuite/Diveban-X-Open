 /* Plugin generated by AMXX-Studio */

#include <amxmodx>
#include <amxmisc>
#include <fakemeta>
#include <time>

#include <sqlx>

#define PLUGIN "AfterBan"
#define VERSION "2021.0"
#define AUTHOR "RevCrew"

#define DATE "4.11.2018"

#if AMXX_VERSION_NUM < 183
	#include <ColorChat>
#else
	#define RED print_team_red
	#define DontChange print_team_default
	#define register_dictionary_colored register_dictionary
#endif

//#define _ctrlchar

/*	
	AfterBan | Edition 2018 - Version 2019.0
	AMX Mod X Plugin
	Copyright (C) 2014-2017 <CS-Suite>
	Our website "http://cs-suite.ru"

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
	Update from 1.07.2017:
	* Now admins get admin flags only on server(s) that has their privilege
*/

const SIZE_T = 63;
const ID_LEN = 25

const TASK_SNAP = 1056

enum CVARS
{
	CVAR_ADMIN_ON,

	CVAR_ADMIN_TABLE,
	CVAR_BAN_PREFIX,
	CVAR_SHOW_ADMIN,
	CVAR_SNAPSHOT_TIMES,
	
	CVAR_BANHUD_X,
	CVAR_BANHUD_Y,
	CVAR_BANHUD_COLOR,
	
	CVAR_FREEZE_PLAYER,
	CVAR_STRIP_PLAYER
}

new g_Cvars[CVARS];
new Handle:iCore;

new amx_passwd_info[22];
new amx_def_flag[2]

const BAN_STRING_LEN = 64;
const BAN_IP_LEN = 22

enum _:BannedData {

	/* Required Params */
	BD_BAN_NAME[BAN_STRING_LEN],BD_BAN_STEAM[BAN_IP_LEN],BD_BAN_IP[BAN_IP_LEN],BD_BAN_COOKIE[BAN_IP_LEN],BD_BAN_UID[BAN_IP_LEN],BD_BAN_DIVEID[BAN_IP_LEN],BD_BAN_CDKEY[33],
	BD_BAN_LEN,BD_BAN_REASON[BAN_STRING_LEN],BD_BAN_TIME,BD_BAN_UNBAN_TIME,BD_BAN_ADMIN_NAME[BAN_STRING_LEN],BD_BAN_ADMIN_ID[BAN_IP_LEN],BD_BAN_TYPE[BAN_IP_LEN],
	BD_BAN_SERVER[BAN_STRING_LEN],BD_BAN_SERVER_IP[BAN_IP_LEN],BD_BAN_ADMIN_PLAYER_ID,BD_BAN_BANTYPE,BD_BAN_PLAYER_ID
}

forward divebanx_addban(id, data[BannedData], const bantype);
forward divebanx_kick_player(const player,const admin_name[],const reason[]);
forward divebanx_history_bans(const id, const bans_count);

enum _:AdminData
{
	Nick[32],
	Authid[32],
	TLast,
	Passwd[32],
	Flags[32],
	_To[32],
	_From[32]
}

new Array: g_admins;
new g_admin_time[33];

public plugin_precache()
{
	g_Cvars[CVAR_ADMIN_ON] = register_cvar("af_admin_on","1");

	g_Cvars[CVAR_ADMIN_TABLE] = register_cvar("af_admin_table","diveban_admins");
	g_Cvars[CVAR_BAN_PREFIX] = register_cvar("af_ban_prefix","AfterBan");
	g_Cvars[CVAR_SHOW_ADMIN] = register_cvar("af_show_adminname","1");
	g_Cvars[CVAR_SNAPSHOT_TIMES] = register_cvar("af_snapshot_times","2");
	
	g_Cvars[CVAR_BANHUD_X] = register_cvar("af_banhud_x","0.05");
	g_Cvars[CVAR_BANHUD_Y] = register_cvar("af_banhud_y","0.4");
	g_Cvars[CVAR_BANHUD_COLOR] = register_cvar("af_banhud_color","255x0x0");

	g_Cvars[CVAR_FREEZE_PLAYER] = register_cvar("af_freeze_player","1");
	g_Cvars[CVAR_STRIP_PLAYER] = register_cvar("af_strip_player","1");

	new cfg[64];
	get_configsdir(cfg, charsmax(cfg))
	
	add(cfg, charsmax(cfg), "/AfterBan.cfg");
	
	server_cmd("exec %s", cfg)
	server_exec();
	
	g_admins = ArrayCreate(AdminData);
	
}
public plugin_init()
{
	register_plugin(PLUGIN, VERSION, AUTHOR)
	
	register_srvcmd("amx_reloadadmins", "reloadAdmins")

	register_dictionary_colored("afterban.txt");
	register_dictionary("time.txt")

	if(!get_pcvar_num(g_Cvars[CVAR_ADMIN_ON]))
		return

	register_cvar("amx_password_field", "_pw")
	register_cvar("amx_default_access", "z")
	
	register_cvar("amx_vote_ratio", "0.02")
	register_cvar("amx_vote_time", "10")
	register_cvar("amx_vote_answers", "1")
	register_cvar("amx_vote_delay", "60")
	register_cvar("amx_last_voting", "0")
	register_cvar("amx_show_activity", "2")
	register_cvar("amx_votekick_ratio", "0.40")
	register_cvar("amx_voteban_ratio", "0.40")
	register_cvar("amx_votemap_ratio", "0.40")

	set_cvar_float("amx_last_voting", 0.0)
	remove_user_flags(0, read_flags("z"))		// Remove 'user' flag from server rights

	new configsDir[64]
	get_configsdir(configsDir, 63)
	
	server_cmd("exec %s/amxx.cfg", configsDir)	// Execute main configuration file
	server_exec();

	SQL_Init();
}
public plugin_natives()
{
	register_native("af_get_admin_time", "_admin_time", 1)
}
public _admin_time(id)
{
	if(!get_pcvar_num(g_Cvars[CVAR_ADMIN_ON]))	return -3;
	
	return g_admin_time[id];
}
public SQL_Init()
{

	static host[SIZE_T+1], user[SIZE_T+1], pass[SIZE_T+1], db[SIZE_T+1];
	
	get_cvar_string("divebans_host", host, SIZE_T)
	get_cvar_string("divebans_user", user, SIZE_T)
	get_cvar_string("divebans_pass", pass, SIZE_T)
	get_cvar_string("divebans_db", 	 db,   SIZE_T)

	iCore= strlen(host) ? SQL_MakeDbTuple(host,user,pass,db) : SQL_MakeStdTuple();
	
	static err,error[128]
	new Handle:iConnect = SQL_Connect(iCore,err,error,127)
	
	if(iConnect == Empty_Handle)
	{
		PrintMessage("Can't connect to Mysql (Error %s)",error)
		set_fail_state("Can't connect to Mysql (Check Logs)")
	}
	
	SQL_FreeHandle(iConnect)

	PrintMessage(" Plugin running [Version: %s] ", VERSION);
	
	get_cvar_string("amx_password_field", amx_passwd_info, charsmax(amx_passwd_info))
	get_cvar_string("amx_default_access", amx_def_flag, charsmax(amx_def_flag))
	
	static server_ip[26];
	get_user_ip(0, server_ip, charsmax(server_ip))
	
	static szTemp[256], Table[64];
	get_pcvar_string(g_Cvars[CVAR_ADMIN_TABLE], Table, charsmax(Table))
	formatex(szTemp,255,"SELECT * FROM `%s` Where `access` LIKE '%%%s%%'",Table, server_ip)
	
	SQL_ThreadQuery(iCore,"LoadAdminData",szTemp)
}
public LoadAdminData(FailState,Handle:Query,Error[],Errcode,Data[],DataSize)
{
	if(FailState)
		return SQL_Error(Query, Error, Errcode, FailState);
	
	if(SQL_AffectedRows(Query) < 1) 
		return SQL_FreeHandle(Query);
	
	new data[AdminData];

	while(SQL_MoreResults(Query))
	{
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query, "steamid"), data[Authid], 31);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query, "nick"), data[Nick], 31);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"passwd"), data[Passwd], 31);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query, "timelast"), data[_To], 31);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query, "timedo"), data[_From], 31);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"flags"), data[Flags], 31);
		
		data[TLast] = !equali(data[_To], "0") ? parse_time(data[_To], "%Y-%m-%d") : 0;
		
		ArrayPushArray(g_admins, data);

		SQL_NextRow(Query);
	}
	
	PrintMessage("Load %d admin(s)", ArraySize(g_admins));
	
	if ( get_playersnum() > 0 )
	{
		static maxplayers; maxplayers = get_maxplayers();
		for(new id = 1; id < maxplayers; id++)
			if(is_user_connected(id))	client_authorized(id);	
	}
	return SQL_FreeHandle(Query);
}
public reloadAdmins( const server_id, level, sid )
{
	ArrayClear(g_admins);
	PrintMessage("ClearAllAdmins...")

	static server_ip[26];
	get_user_ip(0, server_ip, charsmax(server_ip))
	
	PrintMessage("Start to load admins from %s", server_ip)

	static szTemp[256], Table[64];
	get_pcvar_string(g_Cvars[CVAR_ADMIN_TABLE], Table, charsmax(Table))
	formatex(szTemp,255,"SELECT * FROM `%s` Where `access` LIKE '%%%s%%'",Table, server_ip)
	
	SQL_ThreadQuery(iCore,"LoadAdminData",szTemp)
}
GetAdminID(authid[], nick[])
{
	new data[AdminData];
	new size = ArraySize(g_admins);
	
	new bool:onlySteamID = false;
	new bool:onlyNick= false;
	for(new i; i<size; i++)
	{
		ArrayGetArray(g_admins, i, data);

		onlySteamID = bool:(containi(data[Flags], "*") != -1);
		onlyNick = bool:(containi(data[Flags], "#") != -1);

		if( equali(data[Authid], authid) && onlySteamID ) {
			//PrintMessage("Auth by SteamID (%s) [Flags %s] [OnlySteamID]", authid, data[Flags])
			return i;
		}
			
		if( equali(data[Nick], nick) && onlyNick ) {
			//PrintMessage("Auth by NickName (%s) [Flags %s] [OnlyNickName]", nick, data[Flags])
			return i;
		}
			

		if( (equali(data[Authid], authid) || equali(data[Nick], nick)) && !onlyNick && !onlySteamID )
		{
			//PrintMessage("Auth by Steam/Nick: Player %s (%s) | DataBase %s (%s) [Flags %s]", nick, authid, data[Nick], data[Authid], data[Flags])
			return i;
		}
	}
	
	return -1;
}
stock AuthAdmin(id)
{
	if(!get_pcvar_num(g_Cvars[CVAR_ADMIN_ON]))	return 0;

	static authid[ID_LEN+1],name[ID_LEN+1], ip[22];
	
	get_user_authid(id,authid,ID_LEN)
	get_user_name(id, name,ID_LEN);
	get_user_ip(id, ip, charsmax(ip), 1)

	log_amx("Start check Player '%s'", name)

	new admin_id = GetAdminID(authid, name)
	
	log_amx("AdminID '%s' : '%d'", name, admin_id)

	if(admin_id == -1)
	{
		g_admin_time[id] = -1
		return 0;
	}
		
	new data[AdminData], status[16], info[32];
	
	ArrayGetArray(g_admins, admin_id, data);
	get_user_info(id, amx_passwd_info,info, charsmax(info))	
	
	new sys = get_systime(0);
		
	if( data[TLast] - sys < 0 && data[TLast] != 0)
	{
		g_admin_time[id] = -1
		return 0;
	}

	if(equali(data[Authid], authid))	status = "SteamID";
	else if(equali(data[Nick], name)) 	status = "NickName";
	
	if( strlen(data[Passwd]) >= 3 && !equal(data[Passwd],info) )
	{
		PrintMessage("Player %s (%s)(%s) invalid password [Has:%s][Need:%s] [ID:%s]",name,authid,ip, info , data[Passwd], status)
		return server_cmd("kick #%d ^"[Af] Invalid Password^"",get_user_userid(id));
	}
	
	log_amx("SetAdminTime '%s' : '%d'", name, admin_id)

	g_admin_time[id] = !data[TLast] ? 0 : (data[TLast] - sys)/(3600*24);

	new mData[32];
	copy(mData, 31, data[_To]);
	
	PrintMessage("Player %s (%s)(%s) [%s] became an admin [From:%s][To:%s] [ID:%s][%d Days]",name,authid,ip, data[Flags], data[_From], !data[TLast] ? "permament" : mData, status, _admin_time(id));

	remove_user_flags(id,read_flags(amx_def_flag));
	set_user_flags(id,read_flags(data[Flags]));
	
	
	return 0;
}

public client_authorized(id)
{
	g_admin_time[id] = -2;
	AuthAdmin(id);

	return PLUGIN_CONTINUE;
	
}
public client_putinserver(id)
{
	if( g_admin_time[id] == -2) AuthAdmin(id);
	if(!is_user_admin(id)) set_user_flags(id, read_flags(amx_def_flag))
}

public divebanx_addban(id, data[BannedData], const bantype)
{
	static player_name[32], ban_time[64], admin_name[32], reason[64];

	new player = data[BD_BAN_PLAYER_ID];
	
	copy(admin_name, charsmax(admin_name), data[BD_BAN_ADMIN_NAME])
	copy(player_name, charsmax(player_name), data[BD_BAN_NAME])
	copy(reason, charsmax(reason), data[BD_BAN_REASON])

	get_time_length(0, data[BD_BAN_LEN], timeunit_minutes, ban_time, 63)
  
	new prefix[32];
	get_pcvar_string(g_Cvars[CVAR_BAN_PREFIX], prefix, charsmax(prefix))

	if(get_pcvar_num(g_Cvars[CVAR_SHOW_ADMIN]))	client_print_color(0, RED, "^1[^3%s^1] %L",prefix, LANG_PLAYER, "AFTERBAN_BAN_AMESSAGE", admin_name, player_name, ban_time, reason);
	else						client_print_color(0, RED, "^1[^3%s^1] %L",prefix, LANG_PLAYER, "AFTERBAN_BAN_MESSAGE", player_name, ban_time, reason);

	//SnapShoot
	set_task(0.5, "SnapshotPlayer", player+TASK_SNAP, _,_, "a", get_pcvar_num(g_Cvars[CVAR_SNAPSHOT_TIMES]))
	
	static color[16];
	get_pcvar_string(g_Cvars[CVAR_BANHUD_COLOR], color, charsmax(color))
	
	replace_all(color, charsmax(color), "x", " ");
	
	new rgb[3][4];
	parse(color, rgb[0], 3, rgb[1], 3, rgb[2], 3)
	
	//HudMessage
	set_hudmessage(str_to_num(rgb[0]),str_to_num(rgb[1]),str_to_num(rgb[2]), get_pcvar_float(g_Cvars[CVAR_BANHUD_X]), get_pcvar_float(g_Cvars[CVAR_BANHUD_Y]),0,1.0,12.0,1.0,1.0,2);
	
	if(get_pcvar_num(g_Cvars[CVAR_SHOW_ADMIN]))	show_hudmessage(0, "%L", LANG_PLAYER, "AFTERBAN_HUD_ATEXT", admin_name,player_name,ban_time,reason) 
	else						show_hudmessage(0, "%L", LANG_PLAYER, "AFTERBAN_HUD_TEXT", player_name,ban_time,reason)
	if(is_user_alive(player))
	{
		if(get_pcvar_num(g_Cvars[CVAR_FREEZE_PLAYER]))	freeze_player(player)
		if(get_pcvar_num(g_Cvars[CVAR_STRIP_PLAYER]))	fm_strip_user_weapons(player)
	}
}

public divebanx_kick_player(const player,const admin_name[],const reason[])
{
	static name[32],prefix[32];
	get_user_name(player, name, charsmax(name));
	get_pcvar_string(g_Cvars[CVAR_BAN_PREFIX], prefix, charsmax(prefix))
	
	client_print_color(0, RED, "^1[^3%s^1] %L",prefix, LANG_PLAYER, "AFTERBAN_KICKBAN_TEXT", name,reason);
}
public divebanx_history_bans(const id, const bans_count)
{
	new p[32], c, player;
	get_players(p,c, "ch")
	
	static name[32],prefix[32];
	get_user_name(id, name, charsmax(name));
	get_pcvar_string(g_Cvars[CVAR_BAN_PREFIX], prefix, charsmax(prefix))

	for(new i; i<c; i++)
	{
		player = p[i];
		
		if(!is_user_admin(player))
			continue;
			
		client_print_color(player, RED, "^1[^3%s^1] %L", prefix, player, "AFTERBAN_BANCOUNT_TEXT", name, bans_count);
	}
}
public divebans_disconnect_ban(const admin, const name[], const ip[], const minutes, const reason[])
{
	static aname[32], prefix[32], ban_time[64];
	get_user_name(admin, aname, charsmax(aname))
	
	get_time_length(0, minutes, timeunit_minutes, ban_time, 63)
	get_pcvar_string(g_Cvars[CVAR_BAN_PREFIX], prefix, charsmax(prefix))
	
	if(get_pcvar_num(g_Cvars[CVAR_SHOW_ADMIN]))	client_print_color(0, RED, "^1[^3%s^1] %L",prefix, LANG_PLAYER, "AFTERBAN_DISCBAN_AMESSAGE", aname, name, ban_time, reason);
	else						client_print_color(0, RED, "^1[^3%s^1] %L",prefix, LANG_PLAYER, "AFTERBAN_DISCBAN_MESSAGE", name, ban_time, reason);
}

public SnapshotPlayer( taskid )
{
	new id = taskid - TASK_SNAP;
	
	if(!is_user_connected(id))
		return remove_task( id+TASK_SNAP);
		
	client_cmd(id,";snapshot")
	
	return 0;
}
stock freeze_player(id) 
{
	set_pev(id, pev_velocity, Float:{0.0, 0.0, 0.0})
	engfunc(EngFunc_SetClientMaxspeed, id, 0.00001)
	
	return set_pev(id , pev_flags, pev(id, pev_flags) | FL_FROZEN)
}
stock PrintMessage(const szMessage[], any:...)
{
	static szMsg[256];
	vformat(szMsg, charsmax(szMsg), szMessage, 2);
	
	log_amx("%s", szMsg)
	server_print("%s", szMsg)
	return 1;
}
stock SQL_Error(Handle:query, const error[], errornum, failstate)
{
	static qstring[512]
	SQL_GetQueryString(query, qstring, 511)
	
	if(failstate == TQUERY_CONNECT_FAILED) 
	{
		PrintMessage("[SQLX] Error connected to database")
	} 
	else if (failstate == TQUERY_QUERY_FAILED) 
	{
		PrintMessage("[SQLX] Failed")
	}
	PrintMessage("[SQLX] Error '%s' with '%s'", error, errornum)
	PrintMessage("[SQLX] %s", qstring)

	return SQL_FreeHandle(query)
}

#define fm_create_entity(%1) engfunc(EngFunc_CreateNamedEntity, engfunc(EngFunc_AllocString, %1))

stock fm_strip_user_weapons(index) {
	new ent = fm_create_entity("player_weaponstrip");
	if (!pev_valid(ent))
		return 0;

	dllfunc(DLLFunc_Spawn, ent);
	dllfunc(DLLFunc_Use, ent, index);
	engfunc(EngFunc_RemoveEntity, ent);

	return 1;
}
stock mysql_escape_string(const source[],  dest[],  len)
{
        copy(dest, len, source);
 
        replace_all(dest, len, "^\", "^\^\");
        
        replace_all(dest, len, "'", "^\'");
        replace_all(dest, len, "`", "^\`");
        replace_all(dest, len, "^"", "^\^"");
}
stock bool: is_user_steam(client)
{
    new dp_pointer;
	
    if(dp_pointer || (dp_pointer = get_cvar_pointer("dp_r_id_provider")))
    {
        server_cmd("dp_clientinfo %d", client);
        server_exec();
        return bool:((get_pcvar_num(dp_pointer) == 2) ? 1 : 0);
    }
	
    return bool:0;
}
