#if defined _sql_included
    #endinput
#endif

#define _sql_included

#include <amxmodx>
#include <sqlx>

#define function void:

public function SqlX() {
	SQL_SetAffinity("mysql");

	new host[32],user[32],pass[32],db[32]
		
	get_pcvar_string(g_Cvars[CVAR_HOST],host,31)
	get_pcvar_string(g_Cvars[CVAR_USER],user,31)
	get_pcvar_string(g_Cvars[CVAR_PASS],pass,31)
	get_pcvar_string(g_Cvars[CVAR_DB],db,31)

	g_SqlTuple = sqlInit(host,user,pass,db)
}

public SQL_Time()
{
  	if (get_pcvar_num(g_Cvars[CVAR_SQLTIME]) == 1)
	{
		new szQuery[64];
		formatex(szQuery, charsmax(szQuery), "SELECT UNIX_TIMESTAMP(NOW())");
	
		SQL_ThreadQuery(g_SqlTuple, "SQl_CheckTime", szQuery);
	}
	
	new host[64]
	get_cvar_string("hostname",host, sizeof(host) - 1)
	MakeStringSQLSafe(host,hostname,charsmax(hostname))
}



public Handle:sqlInit( 
	const sqlHost[],
	const sqlUser[],
	const sqlPass[],
	const sqlDb[]) { 

	if ( strlen(sqlHost) < 5 ) {
		// В этом случае, мы должны взять данные из sql.cfg
		//set_stored_var("initsql_host_len", "0");

		new Handle:Turple = SQL_MakeStdTuple(0)
		// Проверяем, есть ли соединение
		sqlConn(Turple, true, "sql.cfg")
		// Возвращаем стандартный дескриптор
		
		return Turple;
	}

	new Handle:Turple = SQL_MakeDbTuple(sqlHost,sqlUser,sqlPass,sqlDb);

	if ( !sqlConn(Turple, false, "diveban.cfg") ) {
		// Не смогли подключиться через divebans_host
		Turple = SQL_MakeStdTuple(0)
		// Проверяем, есть ли соединение
		sqlConn(Turple, true, "sql.cfg")
		// Возвращаем стандартный дескриптор
		return Turple;
	}

	return Turple;
}

stock bool:sqlConn( Handle:Turple = Empty_Handle, bool:offPlugin = false, const addToEnd[] = "sql") {
	static err,error[128]
	new Handle:connect = SQL_Connect(Turple,err,error,127)

	if(connect == Empty_Handle) {
		new message[128];
		formatex(message, charsmax(message), "[SQL Connect] Can't connect by %s",addToEnd)

		PrintMessage("%s", message)
		PrintMessage("[SQL Connect] Error %d '%s'", err, error)
		if ( offPlugin ) set_fail_state(message);
		return false;
	}

	SQL_FreeHandle(connect)
	return true;
}
