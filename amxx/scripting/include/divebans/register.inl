#if defined _register_inc
    #endinput
#endif

#define _register_inc

#include <amxmodx>

public RegisterCommands() {

	// Команды для блокировка чата если игрок не прошел проверку на бан
	register_clcmd("say", "PreventSayNotReady")
	register_clcmd("say_team", "PreventSayNotReady")

	// Команда в чат для открытия бан-меню
	register_clcmd("say /db", "CmdBanMenu")
	// Команда в консоль для открытия бан-меню
	register_concmd("db_banmenu", "CmdBanMenu", ADMIN_BAN, "<Display Bans Menu>")
	// Альтернативная команда в консоль для открытия бан-меню
	register_concmd("amx_banmenu", "CmdBanMenu", ADMIN_BAN, "<Display Bans Menu>")

	// Команда в консоль для открытия  меню отсоединившихся игроков
	register_concmd("db_disconnect_menu", "CmdDisconnectMenu", -1, "Show Disconnect Menu");

	// Команда для ввода секретного ключа
	register_clcmd("Db_EnterKey", "EnterSecretKey");

	// Команды для ввода времени и причины бана
	register_clcmd("Db_SetProperty", "SetPropertyReason");
	register_clcmd("Db_SetPropertyTime", "SetPropertyTime");

	// Команды для бана\разбана\очистики банлиста
	register_srvcmd("db_fban", "CmdFastBan", -1, "<time in min> <#userid or ip>  <reason>")
	register_srvcmd("db_unban", "CmdUnban",  -1, "<Name or UniqueID or SteamID or Ip>")
	register_srvcmd("db_clear", "CmdClear",  -1, "Clear the Banlist")

	// Альтернативная команда для бана
	register_srvcmd("amx_ban", "CmdFastBan", -1, "<time in min> <#userid or ip>  <reason>")

	// Команда для оффлайн бана
	register_srvcmd("db_addban", "CmdAddBan", -1, "<Time in min> < Name|SteamID|IP|#userid > < Reason >")
}

public RegisterForwards() {
	 g_forward[DB_BAN_BAN] = CreateMultiForward("divebanx_addban", ET_IGNORE, FP_CELL, FP_ARRAY, FP_CELL) // id, data[BannedData], bantype
	 g_forward[DB_BAN_PRE] = CreateMultiForward("divebanx_ban_pre", ET_IGNORE,FP_CELL,FP_CELL,FP_CELL,FP_STRING) //admin,player,reason,minutes
	 g_forward[DB_KICK_PRE] = CreateMultiForward("divebanx_kick_player",ET_IGNORE,FP_CELL,FP_STRING,FP_STRING) //player,admin_name,reason
	 g_forward[DB_HISTORY_BAN] = CreateMultiForward("divebanx_history_bans",ET_IGNORE,FP_CELL,FP_CELL) // id, bans_count
}