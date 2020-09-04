#if defined _cmd_unban_inc
    #endinput
#endif

#define _cmd_unban_inc

#include <amxmodx>

public CmdUnban(id, level, cid)
{
	if(!license(id))
		return PLUGIN_HANDLED;
		
	new arg[35];
	read_argv(1, arg, sizeof(arg) - 1);
	
	UnBan(id,arg, charsmax(arg), true)
	return PLUGIN_HANDLED;
}