#if defined _load_included
    #endinput
#endif

#define _load_included

/**
	FileName: 	load.inl
	Date:		06.11.2018
	Author:		RevCrew
 */

#include <amxmodx>

//#define DEBUG_LOAD_INL

LoadReasons() {
	new fileDir[64];
	getDirByType(DirData:DIR_CONFIG, fileDir, charsmax(fileDir), "reasons.ini");

	if ( !file_exists(fileDir) )
		return ErrorDetect("Can't find file [%s]", fileDir)
	
	new f = fopen(fileDir, "r");
	new filedata[128], data[FileData];

	while( !feof(f) ) {
		fgets(f, filedata, charsmax(filedata))

		trim(filedata)
		if( filedata[0] != '"') continue;

		replace_all(filedata, charsmax(filedata), "^t"," ")
		replace_all(filedata, charsmax(filedata), "  "," ")

		strbreak(filedata, data[FileReason], 63, data[FileTimes], 31);

		data[FileCountTimes] = _parse_time(data[FileTimes]);
		ArrayPushArray(gFile, data);
	}

	#if defined DEBUG_LOAD_INL
	new size = ArraySize(gFile);
	server_print("[LoadReasons] Size gFile: %d", size);
	for(new i; i< size; i++) {
		ArrayGetArray(gFile, i, data)
		server_print("[%i] Reason -> [%s], Times -> [%s], CountTimes -> [%d]", i, data[FileReason], data[FileTimes], data[FileCountTimes])
	}
	#endif

	return fclose(f);
}

LoadConsoleMessage() {
	new fileDir[64];
	getDirByType(DirData:DIR_CONFIG, fileDir, charsmax(fileDir), "console_message.ini");
	
	if ( !file_exists(fileDir) )
		return ErrorDetect("Can't find file [%s]", fileDir)

	new f = fopen(fileDir, "rt");
		
	new filedata[128];
	while( !feof(f) )
	{
		fgets(f, filedata, sizeof(filedata) - 1);
		
		trim(filedata)
		if( filedata[0] == '#' || strlen(filedata) < 3 ) continue;

		ArrayPushString(g_con_mess, filedata);
	}

	#if defined DEBUG_LOAD_INL
		new size = ArraySize(g_con_mess);
		server_print("[LoadConsoleMessage] Size g_con_mess: %d", size);

		new message[128];
		for(new i; i< size; i++) {
			ArrayGetString(g_con_mess, i, message, 127)

			server_print("[%i] Message -> [%s]", i, message)
		}
	#endif
	return fclose(f);
}

LoadMotd()
{
	if( !g_web_ban ) return server_cmd("motdfile ^"motd.txt^"");

	new url[128];get_pcvar_string(g_Cvars[CVAR_COOKIE_CHECKFILE],url, charsmax(url))
	
	static const motdFile[] = "db_motd.txt";

	new f = fopen(motdFile, "w");
	fprintf(f, "<html><meta http-equiv=^"Refresh^" content=^"0; URL=%s^"><head><title>Cstrike MOTD</title></head></html>",url)

	#if defined DEBUG_LOAD_INL
	new test = fopen(motdFile, "r");
	new data[512]; fgets(test, data, 511);
	server_print("[Motd] %s", data);
	fclose(test);
	#endif

	return fclose(f);
}

LoadPrint() {
	if( find_plugin_byfile("AfterBan.amxx") != INVALID_PLUGIN_ID ) g_Color = true;
	return PrintMessage("[ColorPrint] %s", g_Color ? "ENABLED by 'Afterban.amxx'" : "DISABLED")
}