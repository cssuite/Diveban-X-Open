#if defined _cmd_clear_inc
    #endinput
#endif

#define _cmd_clear_inc

#include <amxmodx>

/** 
 * Очистка Банлиста
 */
public CmdClear(id)
{
	new dummyStat[1], UserStatus:st;
	get_user_status(id, st, dummyStat, charsmax(dummyStat))

	if(id && st < UserStatus:ST_MAIN_ADMIN) return Print(id,"%L",id, "DB_NO_ACCESS");

	new title[32]; formatex(title, sizeof(title) - 1, "%L", id, "DB_CLEARBAN_CONFIRM")
	new menu = menu_create(title, "HandleClear")

	menu_additem(menu, "Yes", "1");
	menu_additem(menu, "No", "2");

	menu_display(id, menu, 0)

	return PLUGIN_HANDLED;
}

public HandleClear(id, menu, item)
{
	if(item == MENU_EXIT)
	{
		return menu_destroy(menu)
	}
	
	new s_Data[26], s_Name[2], i_Access, i_Callback
	menu_item_getinfo(menu, item, i_Access, s_Data, charsmax(s_Data), s_Name, charsmax(s_Name), i_Callback)

	if(str_to_num(s_Data) == 1)
	{
		new query[64];formatex(query, charsmax(query), "DELETE FROM `%s` WHERE 1",szTable)

		new Data[1];Data[0] = id;
		SQL_ThreadQuery(g_SqlTuple, "ClearBanlist", query, Data, 1)
	}

	return menu_destroy(menu)
}