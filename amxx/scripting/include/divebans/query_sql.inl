#if defined _querycvar_included
    #endinput
#endif

#define _querycvar_included

#include <amxmodx>
#include <sqlx>

const 	TABLE_LEN = 1256
new	check_map = 0;
public QuerySQL()
{
	new query[TABLE_LEN], len;
	
	if(!szTable[0])
		formatex(szTable, charsmax(szTable), "divebans")
		
	len = formatex(query, charsmax(query), "CREATE TABLE IF NOT EXISTS `%s` ( `banid` int(10) unsigned NOT NULL AUTO_INCREMENT,\
	`banname` varchar(35) NOT NULL, `steam` varchar(35) NOT NULL, `ip` varchar(35) NOT NULL, ",szTable)
	
	len += formatex(query[len], charsmax(query)-len,"`ipcookie` varchar(150) NOT NULL, `uid` varchar(35) NOT NULL, `diveid` varchar(32) NOT NULL,  `cdkey` varchar(35) NOT NULL,\
	`bantime` varchar(100) NOT NULL, `unbantime` varchar(100) NOT NULL, `reason` varchar(100) NOT NULL, `name` varchar(35) NOT NULL,")
	
	len += formatex(query[len], charsmax(query)-len,"`admin` varchar(35) NOT NULL, `adminip` varchar(35) NOT NULL, `time` varchar(100) NOT NULL,\
	`bantype` varchar(30) NOT NULL, `Server` varchar(100) NOT NULL, `ServerIp` varchar(100) NOT NULL, `map` varchar(100) NOT NULL,  `adminst` varchar(100) NOT NULL DEFAULT '',")
	
	len += formatex(query[len], charsmax(query)-len, "`Bans_Kick` int(10) NOT NULL, PRIMARY KEY (`banid`) )\
	ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;")

	SQL_ThreadQuery(g_SqlTuple,"CreateTableQuery",query)
}
public CreateTableQuery(FailState,Handle:Query,Error[],Errcode,Data[],DataSize)
{
	if(FailState)
	{
		return SQL_Error(Query, Error, Errcode, FailState);
	}
	
	check_map ++;

	if(check_map == 1)
	{
		//new query[128];
		//formatex(query, charsmax(query), "show columns FROM `%s` where `Field` = 'bid'", szTable );
	
		//SQL_ThreadQuery(g_SqlTuple,"CreateTableQuery", query)
	}
	else if(check_map == 2)
	{
		if(SQL_NumResults(Query) > 0)
			return SQL_FreeHandle(Query);
		
		//new query[128];
		//formatex(query, charsmax(query), "ALTER TABLE `%s` ADD `bid` int(10) NOT NULL AFTER `Bans_Kick`", szTable );
	
		//SQL_ThreadQuery(g_SqlTuple,"CreateTableQuery", query)
		
	}
	
	return SQL_FreeHandle(Query);
}
