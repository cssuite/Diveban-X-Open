#if defined _cmd_banmenu_inc
    #endinput
#endif

#define _cmd_banmenu_inc

#include <amxmodx>

/** 
 * Проверка на админа и сброс параметров
 */
public CmdBanMenu(id,level,cid)
{
	if(!license(id))
		return PLUGIN_HANDLED;

	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	if( st < UserStatus:ST_ADMIN) return Print(id,"%L", id, "DB_NO_ACCESS");
	clear_bit(g_disc_ban, id);

	CmdMainBanMenu(id)
	return PLUGIN_HANDLED;
}

CmdMainBanMenu(id)
{
	new status[32], UserStatus:st;
	get_user_status(id, st, status, charsmax(status))

  	new title[128]; formatex(title, sizeof(title) - 1, "\r%L^n\w%L:%s", id, "DB_MENU_MAIN_TITLE", id, "DB_MENU_MAIN_STATUS", status)
	new Menu = menu_create(title, "menu_main")
	MenuSetProps( id, Menu)
	
	formatex(title, sizeof(title) - 1, "\w%L", id, "DB_MENU_MAIN_BAN")
	menu_additem(Menu, title, "1", 0)
	
	formatex(title, sizeof(title) - 1, "\w%L^n", id, "DB_MENU_MAIN_OFFBAN")
	menu_additem(Menu, title, "2", 0)

	formatex(title, sizeof(title) - 1, "\w%L", id, "DB_MENU_MAIN_UNBAN")
	menu_additem(Menu, title, "3", 0)
	
	formatex(title, sizeof(title) - 1, "%s%L^n", st == UserStatus:ST_MAIN_ADMIN ? "\y" : "\d", id, "DB_MENU_MAIN_CLEARBAN")
	menu_additem(Menu, title, "4", 0)
	
	menu_display(id, Menu, 0)
	return PLUGIN_HANDLED;
}

public menu_main(id, menu, item)
{
	if (item == MENU_EXIT)
		return menu_destroy(menu)

	new s_Data[2], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)
	
	clear_bit(g_in_confirm, id)
	
	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	switch (str_to_num(s_Data))
	{
		case 1:
		{
			g_bantype_option[id][BT_ON] = false;
			g_bantype_option[id][BT_TYPE][0] = '^0';
			cmdsBanMenu(id);
		}
		case 2: CmdDisconnectMenu(id)
		case 3: 
		{
			new query[256],Data[1];Data[0]=id
			formatex(query, sizeof(query) - 1,"SELECT * FROM `%s` WHERE (`unbantime` > '%d' OR `unbantime` = '0') AND `unbantime` != '-1'",szTable,get_systime(0)+ TimeGap);
			if (st != UserStatus:ST_MAIN_ADMIN) // Check only bans that made admin
			{
				new adminID[26];
				get_user_authid(id, adminID, charsmax(adminID));

				if ( !is_valid_steamid(adminID) )	get_user_ip(id,adminID, charsmax(adminID), .without_port = 1)
				formatex(query, sizeof(query) - 1,"%s AND (`admin`='%s' OR `adminip`='%s')", query, g_info_player[id][CS_PLAYER_NAME], adminID)
			}

			add(query, charsmax(query), " ORDER BY `banid` DESC")

			#if defined DIVEBAN_X_DEBUG
				PrintMessage("[UnbanMenu] %s", query)
			#endif

			SQL_ThreadQuery(g_SqlTuple,"LoadUnbanData",query, Data, 1)
		}
		case 4: 
		{
			client_cmd(id, "messagemode Db_EnterKey");
		}
	}

	return menu_destroy(menu)
}

public menu_unban(id, menu, item)
{
	if (item == MENU_EXIT)
	{
		CmdMainBanMenu(id)
		return menu_destroy(menu);	
	}
	new s_Data[MAX_IP_LENGHT], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)
	
	UnBan(id,s_Data, charsmax(s_Data), true)

	CmdMainBanMenu(id)
	return menu_destroy(menu);	
}



public menu_chooose_user(id, menu, item)
{
	if (item == MENU_EXIT)
	{
		CmdMainBanMenu(id)
		return menu_destroy(menu)
	}
	
	new s_Data[12], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)
	
	g_struct_array[id][PLAYERID] = str_to_num(s_Data)

	// ##		
	if(!has_player_access_flag(id,g_struct_array[id][PLAYERID]))
		return Print(id,"%L", id, "DB_MENU_BAN_IMMUNE", g_info_player[g_struct_array[id][PLAYERID]][CS_PLAYER_NAME])
	
	if(get_bit(g_in_confirm, id))	MenuBeforeBan(id)
	else							MenuBanReason(id)

	return menu_destroy(menu)
}

public MenuChooseTime(id, key)
{
	new title[86]; 		formatex(title, sizeof(title) - 1, "\r%L", id, "DB_MENU_TIME_TITLE")
	new Menu;			Menu = menu_create(title, "menu_chooose_time")
	new str[10];		MenuSetProps(id, Menu);
	new data[FileData];
	new bantime[64], j;		
	
	ArrayGetArray(gFile, key, data)
	
	if(strlen(data[FileTimes]) <= 1) return;
	
	new part[6], _data[32]
	copy(_data, charsmax(_data), data[FileTimes]);
	
	if(containi(_data, " ") == -1)
		remove_quotes(_data);
		
	while ( strlen(_data) > 0 )
	{
		strbreak(_data, part, charsmax(part), _data, charsmax(_data))
		
		if(equal(part, "^"")) // ^"
			continue;

		j = __ArgParse_TimeShort(part);
		num_to_str(j, str, charsmax(str))
		get_time_length(id, j, timeunit_minutes,  bantime, charsmax(bantime) )
		menu_additem(Menu, bantime, str);
	}
	
	menu_display(id, Menu, 0)
}

public menu_chooose_time(id, menu, item)
{
	if (item == MENU_EXIT)
	{
		clear_bit(g_disc_ban, id);
		CmdMainBanMenu(id)
		return menu_destroy(menu)
	}
	
	new s_Data[12], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)
	
	g_struct_array[id][TIME] = str_to_num(s_Data);
	MenuBeforeBan(id)

	return menu_destroy(menu)
}

cmdBanTypeMenu(id)
{
	new title[86]; formatex(title, sizeof(title) - 1, "\r%L", id, "DB_MENU_BANTYPE_TITLE")
	
	//Create the menu
	new Menu = menu_create(title, "HandleBantype")
	MenuSetProps(id, Menu)
	
	
	formatex(title,sizeof(title) - 1,"%sIP", contain(g_bantype_option[id][BT_TYPE], "I") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "2", 0)

	formatex(title,sizeof(title) - 1,"%sSteamID", contain(g_bantype_option[id][BT_TYPE], "A") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "3", 0)

	formatex(title,sizeof(title) - 1,"%sUniqueID", contain(g_bantype_option[id][BT_TYPE], "U") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "4", 0)

	formatex(title,sizeof(title) - 1,"%sCookie", contain(g_bantype_option[id][BT_TYPE], "C") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "6", 0)

	formatex(title,sizeof(title) - 1,"%sMini Subnet", contain(g_bantype_option[id][BT_TYPE], "S") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "7", 0)
	
	formatex(title,sizeof(title) - 1,"%sFull Subnet", contain(g_bantype_option[id][BT_TYPE], "F") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "8", 0)

	formatex(title,sizeof(title) - 1,"%sCD-Key", contain(g_bantype_option[id][BT_TYPE], "K") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "9", 0)

	formatex(title,sizeof(title) - 1,"%sDiveID", contain(g_bantype_option[id][BT_TYPE], "D") == -1 ? "\w" : "\y")
	menu_additem(Menu, title, "10", 0)

	return menu_display(id, Menu, 0)
}
public HandleBantype(id, menu, item)
{
	if (item == MENU_EXIT)
	{
		MenuBeforeBan(id)
		return menu_destroy(menu)
	}
	
	new s_Data[3], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)
	

	g_bantype_option[id][BT_ON] = true;
	new key = str_to_num(s_Data)
	
	switch(key)
	{
		case 2: if(contain(g_bantype_option[id][BT_TYPE], "I") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "I")
			else							replace(g_bantype_option[id][BT_TYPE],35, "I", "")
		case 3: if(contain(g_bantype_option[id][BT_TYPE], "A") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "A")
			else							replace(g_bantype_option[id][BT_TYPE],35, "A", "")
		case 4: if(contain(g_bantype_option[id][BT_TYPE], "U") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "U")
			else							replace(g_bantype_option[id][BT_TYPE],35, "U", "")
		case 6: if(contain(g_bantype_option[id][BT_TYPE], "C") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "C")
			else							replace(g_bantype_option[id][BT_TYPE],35, "C", "")
		case 7: if(contain(g_bantype_option[id][BT_TYPE], "S") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "S")
			else							replace(g_bantype_option[id][BT_TYPE],35, "S", "")
		case 8: if(contain(g_bantype_option[id][BT_TYPE], "F") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "F")
			else							replace(g_bantype_option[id][BT_TYPE],35, "F", "")
		case 9: if(contain(g_bantype_option[id][BT_TYPE], "K") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "K")
			else							replace(g_bantype_option[id][BT_TYPE],35, "K", "")
		case 10: if(contain(g_bantype_option[id][BT_TYPE], "D") == -1)	add(g_bantype_option[id][BT_TYPE], 35, "D")
			else							replace(g_bantype_option[id][BT_TYPE],35, "D", "")
	}

	cmdBanTypeMenu(id)
	return menu_destroy(menu)
}


public SetPropertyTime(id)
{
	new arg[8];
	read_argv(1, arg, charsmax(arg));
	
	if ( !strlen(arg) )
	{
		Print( id, "You can't set a property blank! Please type a new value")
		return client_cmd(id, "messagemode Db_SetPropertyTime");
	}
	else if ( !IsStrFloat(arg) )
	{
		Print( id, "You can't use letters in a property! Please type a new value.")
		return client_cmd(id, "messagemode Db_SetPropertyTime");
	}
	new check = abs(str_to_num(arg));
	
	check = !check ? 112 : check;
	g_struct_array[id][TIME] = -check
	
	client_cmd(id, "messagemode Db_SetProperty");
	return PLUGIN_HANDLED;
}

stock MenuSetProps( id,menu)
{
	static const plugin_x_format[] = "\yDive\rBan \wX"

	new szText[64]
	formatex(szText, sizeof(szText) - 1, "%L", id, "DB_MENU_PREV")
	menu_setprop(menu, MPROP_BACKNAME, szText)
	
	formatex(szText, sizeof(szText) - 1, "%L", id, "DB_MENU_NEXT")
	menu_setprop(menu, MPROP_NEXTNAME, szText)
	
	formatex(szText, sizeof(szText) - 1, "%L^n^n%s |\y %s", id, "DB_MENU_EXIT", plugin_x_format, VERSION)
	menu_setprop(menu, MPROP_EXITNAME, szText)
	
	return 1
} 