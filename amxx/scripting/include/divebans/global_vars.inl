#if defined _global_vars_included
	#endinput
#endif

#define _global_vars_included

#define MAX_CONSOLE_LENGHT 128
#define MAX_IP_LENGHT 22

#define get_bit(%1,%2) 		( %1 &   1 << ( %2 & 31 ) )
#define set_bit(%1,%2)	 	( %1 |=  ( 1 << ( %2 & 31 ) ) )
#define clear_bit(%1,%2)	( %1 &= ~( 1 << ( %2 & 31 ) ) )

#define is_access(%0,%1)	bool:(get_user_flags(%0) & %1)

#define CONFIG "diveban.cfg"
#define MAX_PlAYERS 33

new const PREFIX[] = "DiveBan X"
const QUERY_MAX_LEN = 1256

enum 
{
	TASK_CHECK_PLAYER = 1655,TASK_KICK_PLAYER = 1831,TASK_BAN_PLAYER = 10002
}

enum _:BanData
{
	BAN_ID,BAN_ADMIN_ID,BAN_PLAYER_ID,BAN_TIME,BAN_REASON[64]
}

enum _:PlayerData
{
	CS_PLAYER_NAME,CS_PLAYER_ID,CS_PLAYER_IP,CS_PLAYER_UID
}

enum _:DiscData
{
	dd_name[32],dd_steamid[MAX_IP_LENGHT],dd_ip[MAX_IP_LENGHT],dd_uid[16],dd_cdkey[33],dd_diveid[16],dd_immune_flag,bool:dd_is_ban
};



enum _:FileData
{
	FileReason[64],FileTimes[32],FileCountTimes
}

enum UserStatus
{
	ST_UNKOWN = 0,ST_ADMIN,ST_SUB_ADMIN,ST_MAIN_ADMIN
}

enum _: Forwards
{
	DB_BAN_BAN,
	DB_BAN_PRE,
	DB_BAN_POST,
	
	DB_KICK_PRE,
	DB_HISTORY_BAN
}

enum _:StructArray
{
	PLAYERID = 0,REASON,TIME
} 

enum pCvars
{
	CVAR_HOST,CVAR_USER,CVAR_PASS,CVAR_DB,CVAR_TABLE,
	
	//CVAR_CSBANS_SUPPORT,CVAR_CSBANS_PREFIX,

	CVAR_SERVER_NAME,CVAR_DELAY_TIME,CVAR_PREVENT_IP,CVAR_SQLTIME,CVAR_MAX_EFFECT_BANTIME,

	CVAR_COOKIE_BANFILE,CVAR_COOKIE_CHECKFILE,
	
	CVAR_MAIN_FLAG,CVAR_SUB_FLAG,CVAR_CMD_TYPE,
	
	CVAR_LOG_LEVEL,CVAR_CLEAR_LOGS,
	
	CVAR_KICK_TIME,CVAR_CHECK_TIME,
	
	CVAR_MARKER_LOG,CVAR_BANCOUNT_LOG
}

enum Bantype
{
	BT_ON,
	BT_TYPE[36]
}

enum _:CacheArray {
	CA_IP[MAX_IP_LENGHT],
	CA_CACHE_COUNT,
	bool:CA_STATUS
}

enum _:BanTypes {
	BAN_TYPE_AUTHID,
	BAN_TYPE_IP,
	BAN_TYPE_COOKIE,

	BAN_TYPE_UID,

	BAN_TYPE_SUBNET,
	BAN_TYPE_FULL_SUBNET,

	BAN_TYPE_DIVEID,
	BAN_TYPE_CDKEY
}

new g_ban_types[BanTypes] = { 'A', 'I', 'C', 'U', 'S', 'F', 'D','K' }

new g_forward[Forwards];
new g_info_player[MAX_PlAYERS][PlayerData][26];
new g_struct_array[MAX_PlAYERS][StructArray];
new g_iCDKey[MAX_PlAYERS][33];
new g_Cvars[pCvars];
new g_DiveID[MAX_PlAYERS][16];
new g_bantype_option[MAX_PlAYERS][Bantype]

new Handle:g_SqlTuple;
new Trie: g_trie_id, Trie: g_trie_ip;
new Array: g_ban_data;
new Array:g_array, Array:gFile
new  Array: g_con_mess

// Cache
new Trie:gTrieCache
new Array:gArrayCache

//Bit
new g_being_banned
new g_disc_ban
new g_ready
new g_in_confirm

//bool
new bool: g_Color = false;
new bool:bCook[33];
new bool:g_web_ban = false;
new bool:g_player_banned[33];

// String
new server_ipaddr[26]
new g_url[255], szTable[26],hostname[64]
new g_own_reason[36]
new g_secret_key[32]

// INT
new TimeGap;
new offs;

new bool:PluginEnable = true;