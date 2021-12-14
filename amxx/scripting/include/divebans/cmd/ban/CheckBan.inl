#if defined _api_check_ban
    #endinput
#endif

#define _api_check_ban

CheckBan( const banid)
{
	new data[BanData],ban_id = get_ban_id(banid);

	ArrayGetArray(g_ban_data, ban_id, data);

	new Data[1],szTemp[1256];Data[0]=banid;
	FormatBanQuery(data[BAN_PLAYER_ID], szTemp, charsmax(szTemp))
	SQL_ThreadQuery(g_SqlTuple,"LoadLastBans",szTemp, Data, 1)
}