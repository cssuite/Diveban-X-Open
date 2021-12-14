#if defined _cmd_ban_inc
    #endinput
#endif

#define _cmd_ban_inc

#include <amxmodx>

#include <divebans/cmd/ban/AddBan.inl>
#include <divebans/cmd/ban/DisconnectBan.inl>
#include <divebans/cmd/ban/DelayedBan.inl>

enum FastBanData {
	FBD_PLAYER[32],
	FBD_TIME[32],
	FBD_REASON[64]
}

/* db_fban */
public CmdFastBan(id, level, cid)
{
	#if defined DIVEBAN_CORE_DEBUG
		PrintMessage("[db_fban] ID: %d", id)
	#endif

	CmdAddBan(id)
	return PLUGIN_HANDLED; 
}

/** 
 * Бан из главного меню
 */
public CmdMenuBan(id, player, minutes, reason[])
{
	#if defined DIVEBAN_CORE_DEBUG
		PrintMessage("[CmdMenuBan] ID: %d, player %d, minutes %d, reason %s", id, player, minutes, reason)
	#endif

	AddPlayerBan(id, player, minutes, reason)

	new ret;
	return ExecuteForward(g_forward[DB_BAN_PRE],ret,id,player,minutes,reason);
}

/**
 * Команда для оффлайн бана
 */
public CmdAddBan(id) {
	new target[MAX_IP_LENGHT], minutes[MAX_IP_LENGHT], reason[64];

	read_argv(1, minutes, sizeof(minutes)-1) 
	read_argv(2, target,  sizeof(target) -1)
	read_argv(3, reason,  sizeof(reason) -1)

	#if defined DIVEBAN_CORE_DEBUG
		PrintMessage("divebans/cmd/Ban.inl] Args minutes %s | target %s | reason %s", minutes, target, reason)
	#endif

	new time = Api_ArgParse_Time( minutes );
	new player = Api_ArgParse_Player( target );

	#if defined DIVEBAN_CORE_DEBUG
		PrintMessage("divebans/cmd/Ban.inl] time %d, player %d, reason %s",time, player, reason)
	#endif

	if( player) {
		
		#if defined DIVEBAN_CORE_DEBUG
			PrintMessage("divebans/cmd/Ban.inl] Triger Normal ban")
		#endif

		return AddPlayerBan(id, player, time, reason);
	}

	new discID = find_disconnect_ban(target)

	if ( discID != -1) {

		#if defined DIVEBAN_CORE_DEBUG
			PrintMessage("divebans/cmd/Ban.inl] Triger Disconnect ban")
		#endif

		return AddDisconnectBan(id, discID, time, reason)
	}


	new data[DelayedData];
	new res = Delayed_Find(data, target)

	if (!res) {
		Print(id, "Can't find player by %s", target)
	}

	#if defined DIVEBAN_CORE_DEBUG
			PrintMessage("divebans/cmd/Ban.inl] Triger Delayed ban")
		#endif

	AddDelayedBan(id, data, time, reason)
	return 1;

}