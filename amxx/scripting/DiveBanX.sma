/* Plugin generated by AMXX-Studio */

#include <amxmodx>
#include <amxmisc>
#include <sqlx>
#include <fakemeta>
#include <sockets>

new const PLUGIN[]=		"DiveBanX"
new const VERSION[]=	"2020.1"
new const AUTHOR[]=		"RevCrew"

stock const DB_MENU_VERSION[] = "3.0";
stock const DB_CACHE_PLAYER_VERSION[] = "1.0";
stock const DB_DELAYED_BAN_VERSION[] = "1.0";
stock const DB_DISCONNECT_BAN_VERSION[] = "2.0";

#define DIVEBAN_CORE_DEBUG
#define DIVEBAN_X_DEBUG
#define BAN_DEBUG

//#pragma dynamic 32768
/**
	[2020.1]
	 - Исправлен баг с отображением причины бана в консоли
 */

// Переменные
#include <divebans/global_vars.inl>

/**
		API
*/

// Общие функции плагина
#include <divebans/tools.inl>
// Методы для работы с временем
#include <divebans/time.inl>
// Асбтрактный метод бана и разбана
#include <divebans/ban.inl>
// Модуль cache
#include <divebans/cache.inl>
// Модуль delayed
#include <divebans/delayed.inl>
// error
#include <divebans/error.inl>

#include <divebans/api/argParser.inl>
//#include <divebans/api/secretKey.inl>

#include <divebans/stock.inl>

/**
	Commands
 */

#include <divebans/cmd/BanMenu.inl>
#include <divebans/cmd/Ban.inl>
#include <divebans/cmd/UnBan.inl>
#include <divebans/cmd/Clear.inl>

/**
	Include's
 */

#include <divebans/register.inl>

#include <divebans/loadFromFiles.inl>
#include <divebans/client.inl>
#include <divebans/sql.inl>
#include <divebans/uid.inl>
#include <divebans/check_player.inl>
#include <divebans/disconnect.inl>
#include <divebans/query_sql.inl>
//#include <divebans/csbans.inl>

#include <divebans/license.inl>

/**
	SQL Query
 */
#include <divebans/query/Handlers.inl>


//#define DIVEBAN_X_DEBUG
//#define DIVEBAN_CORE_DEBUG

public plugin_precache()
{
	Cache_Init()
	Delayed_Init()

	g_trie_id = TrieCreate();
	g_trie_ip = TrieCreate();
	g_array = ArrayCreate(DiscData)

	LoadDisconnectBans();

	SetCvars()

	offs = is_linux_server() ? -19104 + 19368 : -19392 + 19656
}

public plugin_init()
{
	register_plugin(PLUGIN, VERSION, AUTHOR)

	register_dictionary("time.txt")
	register_dictionary("diveban.txt")


	RegisterCommands();

	g_ban_data = ArrayCreate(BanData);

	gFile = ArrayCreate(FileData)
	g_con_mess = ArrayCreate(MAX_CONSOLE_LENGHT)

	ClearLogs();
}

public plugin_cfg()
{
	get_pcvar_string(g_Cvars[CVAR_TABLE], szTable, charsmax(szTable))
	get_user_ip(0,server_ipaddr, sizeof(server_ipaddr) -1)

	RegisterForwards();

	set_task(0.25, "MainLoad")
	set_task(1.0,  "SQL_Time");
	set_task(0.50, "QuerySQL")
}

public plugin_end()
{
	Cache_End()
	Delayed_End()

	if( g_SqlTuple )	SQL_FreeHandle(g_SqlTuple);

	DestroyForward(g_forward[DB_BAN_BAN])
	DestroyForward(g_forward[DB_BAN_PRE])
	DestroyForward(g_forward[DB_BAN_POST])
	DestroyForward(g_forward[DB_KICK_PRE])
	DestroyForward(g_forward[DB_HISTORY_BAN])

	SaveDisconnectBans()

	ArrayDestroy(g_ban_data);
	ArrayDestroy(g_array);
	ArrayDestroy(g_con_mess);
	ArrayDestroy(gFile);

	TrieDestroy(g_trie_id);
	TrieDestroy(g_trie_ip);
}

public PreventSayNotReady(id)
{
	return get_bit(g_ready, id) ? PLUGIN_CONTINUE : PLUGIN_HANDLED;
}
public MainLoad()
{
	SqlX();

	new configsDir[64]; getDirByType(DirData:DIR_CONFIG, configsDir, charsmax(configsDir), "diveban.cfg");
	GetCFGKey(configsDir,"Secret_Key", g_secret_key, charsmax(g_secret_key))
	
	LoadReasons();
	LoadConsoleMessage();
	LoadMotd();
	LoadPrint();

	is_web_ban_exists();
}


/////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Нужна поддержка русского языка
cmdsBanMenu(id)
{
	new title[64]; 
	formatex(title, sizeof(title) - 1, "\r%L", id, "DB_MENU_BAN_TITLE")
	
	//Create the menu
	new Menu,players[32],num,i_Player
	Menu = menu_create(title, "menu_chooose_user")
	new s_Name[32], s_Player[10]
	new szTemp[64];
	
	get_players(players, num)
	MenuSetProps(id,Menu)
	
	new uStatus[22], UserStatus:uSt;

	new iStatus[22], UserStatus:iSt;
	get_user_status(id, iSt, iStatus, charsmax(iStatus));

	for (new i = 0; i < num; i++)
	{
		i_Player = players[i]
		get_user_name(i_Player, s_Name, charsmax(s_Name))
		num_to_str(i_Player, s_Player, charsmax(s_Player))

		get_user_status(i_Player, uSt, uStatus, charsmax(uStatus));

		switch (iSt)
		{
			case ST_MAIN_ADMIN:
			{
				if ( uSt != ST_UNKOWN )		formatex ( szTemp, sizeof ( szTemp ) - 1, "\w%s [%s\w]", s_Name, uStatus);
				else						formatex ( szTemp, sizeof ( szTemp ) - 1, "\w%s", s_Name);
			}
			default:
			{
				formatex ( szTemp, sizeof ( szTemp ) - 1, "\w%s %s", s_Name, uSt == ST_UNKOWN ? "" : "\r*");
			}
		}
		

		menu_additem(Menu, szTemp, s_Player, 0)
	}
	
	return menu_display(id, Menu, 0);
}

stock ShowUnbanMenu(id, Handle:Query) {
	new title[64]; formatex(title, sizeof(title) - 1, "\r%L", id, "DB_MENU_UNBAN_TITLE")
	new Menu = menu_create(title, "menu_unban");
	MenuSetProps(id,Menu)
		
	new szTemp[86];
	new name[32],szReason[32], admin[32];
		
	new ip[MAX_IP_LENGHT]
	while( SQL_MoreResults(Query))
	{
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"reason"), szReason, sizeof(szReason) - 1);	
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"banname"), name, sizeof(name) - 1);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"admin"), admin, sizeof(admin) - 1);
		SQL_ReadResult(Query, SQL_FieldNameToNum(Query,"ip"), ip, sizeof(ip) - 1);
			
		formatex(szTemp,sizeof(szTemp) - 1,"\w%s \y%s \r| %s",name,szReason, admin)
		menu_additem(Menu,szTemp,ip,0)

		SQL_NextRow(Query);
	}
		
	menu_setprop(Menu,MPROP_PERPAGE, 5)
	menu_display(id, Menu, 0)
}

MenuBanReason(id)
{
	new title[86]; formatex(title, sizeof(title) - 1, "\r%L", id, "DB_MENU_REASON_TITLE")
	
	//Create the menu
	new Menu = menu_create(title, "menu_chooose_times")
	new str[3];
	MenuSetProps(id, Menu)
	
	new data[FileData];
	
	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	formatex(title, sizeof(title) - 1, "%s%L", st >= UserStatus:ST_SUB_ADMIN ? "\y" : "\d", id, "DB_MENU_REASON_OWN")
	menu_additem(Menu, title, "0", 0)
	
	for(new j = 0;j< ArraySize(gFile);j++)
	{
		ArrayGetArray(gFile, j, data)
		
		num_to_str(j+1, str, charsmax(str))
		formatex(title,sizeof(title) - 1,"\w%s\R%d",data[FileReason],data[FileCountTimes])
		menu_additem(Menu, title, str, 0)
	}
	
	menu_display(id, Menu, 0)
}
public menu_chooose_times(id, menu, item)
{
	if (item == MENU_EXIT)
	{
		clear_bit(g_disc_ban, id);
		
		CmdMainBanMenu(id)
		return menu_destroy(menu)
	}
	
	new s_Data[3], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)
	
	new key = str_to_num(s_Data)
	
	if( !key )
	{
		new dummyStat[1], UserStatus:st;
		get_user_status(id, st, dummyStat, charsmax(dummyStat))

		if( st >= UserStatus:ST_SUB_ADMIN)	client_cmd(id, "messagemode Db_SetPropertyTime");
		else 								MenuBanReason(id)
		
		g_struct_array[id][REASON] = -1;
		return menu_destroy(menu)
	}
	g_struct_array[id][REASON] = key -1;

	MenuChooseTime(id, key-1)
	return menu_destroy(menu)
}


public MenuBeforeBan(id)
{
	if(!license(id)) return;
	
	new title[64]; formatex(title, sizeof(title) - 1, "\r%L", id, "DB_MENU_CONFIRM_TITLE")
	new Menu= menu_create(title, "menu_before")
	new bantime[64];MenuSetProps(id,Menu)
	
	if(get_bit(g_disc_ban, id))
	{
		new disc[DiscData]
		ArrayGetArray(g_array, g_struct_array[id][PLAYERID], disc)
		formatex(title,sizeof(title) - 1,"%L\y %s", id, "DB_MENU_CONFIRM_PLAYER", disc[dd_name])
	}
	else
		formatex(title,sizeof(title) - 1,"%L\y %s", id, "DB_MENU_CONFIRM_PLAYER",g_info_player[g_struct_array[id][PLAYERID]][CS_PLAYER_NAME])
		
	menu_additem(Menu, title, "1", 0)
	
	if( g_struct_array[id][REASON] == -1)
	{	
		get_time_length(id,g_struct_array[id][TIME] == -112 ? 0 : abs(g_struct_array[id][TIME]), timeunit_minutes, bantime, charsmax(bantime))
		formatex(title,sizeof(title) - 1,"%L\y %s |\r %s", id, "DB_MENU_CONFIRM_RT",g_own_reason, bantime)
	}
	else
	{
		new data[FileData]
		ArrayGetArray(gFile,g_struct_array[id][REASON], data)
		
		get_time_length(id,g_struct_array[id][TIME], timeunit_minutes, bantime, charsmax(bantime))
		formatex(title,sizeof(title) - 1,"%L\y %s |\r %s",id, "DB_MENU_CONFIRM_RT",data[FileReason], bantime)
		
	}
	
	menu_additem(Menu, title, "2", 0)
	
	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	formatex(title,sizeof(title) - 1,"%L%s", id, "DB_MENU_BANPLAYER", st >= UserStatus:ST_SUB_ADMIN ? "^n" : "" )
	menu_additem(Menu, title, "4", 0)
	
	if( st >= UserStatus:ST_SUB_ADMIN )
	{
		formatex(title,sizeof(title) - 1,"%L", id, "DB_MENU_BANTYPE")
		menu_additem(Menu, title, "5", 0)
	}
	
	menu_display(id, Menu, 0)
}
public menu_before(id, menu, item)
{
	if (item == MENU_EXIT)
	{
		clear_bit(g_disc_ban, id)
		CmdMainBanMenu(id)
		return menu_destroy(menu)
	}
	
	new s_Data[2], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)
	
	if(!PluginEnable) return PLUGIN_HANDLED;

	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	switch (str_to_num(s_Data))
	{
		case 5:
		{
			return st == UserStatus:ST_MAIN_ADMIN ? cmdBanTypeMenu(id) : menu_destroy(menu)
		}
		case 1:
		{
			set_bit(g_in_confirm, id);
			get_bit(g_disc_ban, id) ? CmdDisconnectMenu(id) : cmdsBanMenu(id)
		}
		case 2:
		{
			set_bit(g_in_confirm, id);
			MenuBanReason(id)
		}
		case 4:
		{
			if( g_struct_array[id][REASON] == -1 )
			{
				if(get_bit(g_disc_ban, id))
					AddDisconnectBan(id, g_struct_array[id][PLAYERID], g_struct_array[id][TIME] == -112 ? 0 : abs(g_struct_array[id][TIME]), g_own_reason)
				else
					CmdMenuBan(id,g_struct_array[id][PLAYERID], g_struct_array[id][TIME] == -112 ? 0 : abs(g_struct_array[id][TIME]), g_own_reason)
				
				formatex(g_own_reason, 35, "")
				clear_bit(g_disc_ban, id);
			}
			else
			{
				new data[FileData]
				ArrayGetArray(gFile,g_struct_array[id][REASON], data)
				if(get_bit(g_disc_ban, id))
					AddDisconnectBan(id, g_struct_array[id][PLAYERID], g_struct_array[id][TIME] == -112 ? 0 : abs(g_struct_array[id][TIME]), data[FileReason])
				else
					CmdMenuBan(id,g_struct_array[id][PLAYERID], g_struct_array[id][TIME] == -112 ? 0 : abs(g_struct_array[id][TIME]), data[FileReason])
				
				clear_bit(g_disc_ban, id);
			}
				
			clear_bit(g_in_confirm, id)
		}
		default: MenuBeforeBan(id);
	}

	return menu_destroy(menu)
}

public SetPropertyReason(id)
{
	new arg[36];
	read_argv(1, arg, charsmax(arg));
	
	if ( !strlen(arg) )
	{
		Print( id, "You can't set a property blank! Please type a new value")
		return client_cmd(id, "messagemode Db_SetProperty");
	}
	
	copy(g_own_reason, 35, arg)
	MenuBeforeBan(id)
	return PLUGIN_HANDLED;
}

public EnterSecretKey(id)
{
	new arg[32];
	read_argv(1, arg, charsmax(arg));

	if ( !strlen(arg) )
	{
		Print( id, "You can't set a property blank! Please type a new value")
		return client_cmd(id, "messagemode Db_EnterKey");
	}

	if ( !equali(arg, g_secret_key) )
	{
		Print( id, " Invalid SecretKey, after 3 fails you will be blocked")
		return client_cmd(id, "messagemode Db_EnterKey");
	}

	CmdClear(id)
	return PLUGIN_HANDLED;

}
