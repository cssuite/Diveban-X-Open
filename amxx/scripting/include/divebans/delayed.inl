#if defined _delayed_ban_inc
    #endinput
#endif

#define _delayed_ban_inc

#include <amxmodx>

enum _:DelayedData
{
	DD_DELAYED_NAME[32],
	DD_DELAYED_STEAM[MAX_IP_LENGHT],
	DD_DELAYED_IP[MAX_IP_LENGHT],
	DD_DELAYED_UID[16],
	DD_DELAYED_CDKEY[33],
	DD_DELAYED_DIVEID[16]
}

// Delayed Ban
new Array:g_DelayedBans;


enum FindType {
	FT_FIND_STEAM,
	FT_FIND_IP,
	FT_FIND_NAME,

	FT_FIND_ANY
}

enum DelayedCvars {
	DC_DELAYED_STATUS = 0,

	DC_DELAYED_STORE_DAYS,
	DC_DELAYED_OPTIMIZE

}

//#define DELAYED_DEBUG

new g_DelayedCvars[DelayedCvars];

stock Delayed_registerCvars() {
	g_DelayedCvars[DC_DELAYED_STATUS] = 		register_cvar("divebans_delayed_status", "1");
	g_DelayedCvars[DC_DELAYED_STORE_DAYS] = 	register_cvar("divebans_delayed_store_days", "7");
	g_DelayedCvars[DC_DELAYED_OPTIMIZE] = 		register_cvar("divebans_delayed_optimize", "1");
}

stock Delayed_Init() {
	g_DelayedBans = ArrayCreate(DelayedData);

	new DelayedDir[64];
	getDirByType(DirData:DIR_DATA, DelayedDir, charsmax(DelayedDir), "Delayed/")

	if (!dir_exists(DelayedDir) ) mkdir(DelayedDir);


	Delayed_registerCvars()

}

stock Delayed_End() {

	if (!Delayed_IsEnable())
		return

	Delayed_SaveToFile()
	Delayed_DeleteExpired();

	ArrayDestroy(g_DelayedBans);
}

stock void: Delayed_BanAdd(id, data[DelayedData], minutes, reason[], szBanType[] = "") {
	new bantype[36]

	if (szBanType[0])			copy(bantype, 35, szBanType)
	else						GetBanType( bantype,charsmax(bantype))

	new _data[BannedData];
	API_ConvertToBannedData(_data, data[DD_DELAYED_NAME], data[DD_DELAYED_STEAM], data[DD_DELAYED_IP], data[DD_DELAYED_IP],\
		data[DD_DELAYED_UID], data[DD_DELAYED_DIVEID], data[DD_DELAYED_CDKEY], minutes, reason, bantype)

	Ban(id, _data, "delayed ban")
}
stock void: Delayed_DeleteExpired() {
	new Array: files;
	files = ArrayCreate(64);

	new szFile[64];
	new DelayedDir[64];
	getDirByType(DirData:DIR_DATA, DelayedDir, charsmax(DelayedDir), "Delayed/")

	new dir = open_dir(DelayedDir, szFile, charsmax(szFile))
	new temp[64], delayed_time; new del_time = get_systime(0) - 60*60*24*get_pcvar_num(g_Cvars[CVAR_CLEAR_LOGS])
	if(dir)
	{
		do
		{
			if(!Delayed_isDelayedFile(szFile, strlen(szFile)))
				continue;

			copy(temp, charsmax(temp), szFile)
			replace(temp, charsmax(temp), "delayed", "")

			delayed_time = parse_time(temp, "%Y_%m_%d")

			if(delayed_time - del_time <= 0)
				ArrayPushString(files,szFile)
		}
		while(next_file(dir, szFile, charsmax(szFile)))

		close_dir(dir)
	}

	new size = ArraySize(files)
	for(new i; i<size; i++)
	{
		ArrayGetString(files, i, temp, charsmax(temp))

		formatex(szFile, charsmax(szFile), "%s%s",DelayedDir,temp)
		delete_file(szFile)
	}

	ArrayDestroy(files)
}

stock bool:Delayed_inArray(ip[]) {
	new data[DelayedData];
	new len = ArraySize(g_DelayedBans);

	for(new i; i<len; i++) {
		ArrayGetArray(g_DelayedBans, i, data)

		if (equali(data[DD_DELAYED_IP], ip)) {
			return true;
		}
	}

	return false;
}

stock void: Delayed_OnlineBan(name[], time_t, reason[], bantype[]) {
	new data[DelayedData];
	new res = Delayed_Find(data, name)

	if ( !res ) {
		PrintMessage("Can't find delayed ban by '%s'", name);
		return;
	}

	PrintMessage("[Online Ban][Delayed] Add ban to [Name:%s] [%s][%s][BanType:%s]", name, time_t, reason, bantype)
	AddDelayedBan( 0, data, time_t, reason, bantype)
}


stock void: Delayed_AddPlayer(id) {
	if (!Delayed_IsEnable()) {
		return
	}

	new data[DelayedData];
	copy(data[DD_DELAYED_NAME],31,g_info_player[id][CS_PLAYER_NAME]);

	copy(data[DD_DELAYED_STEAM], MAX_IP_LENGHT - 1, g_info_player[id][CS_PLAYER_ID]);
	copy(data[DD_DELAYED_IP], MAX_IP_LENGHT - 1, g_info_player[id][CS_PLAYER_IP]);
	copy(data[DD_DELAYED_UID], 15, g_info_player[id][CS_PLAYER_UID]);

	copy(data[DD_DELAYED_CDKEY],32,g_iCDKey[id])
	copy(data[DD_DELAYED_DIVEID], 15, g_DiveID[id]);

	#if defined DELAYED_DEBUG
		PrintMessage("[Delayed] Add Player %s (%s)", data[DD_DELAYED_NAME], data[DD_DELAYED_IP])
	#endif

	if ( get_pcvar_num(g_DelayedCvars[DC_DELAYED_OPTIMIZE]) && Delayed_inArray(data[DD_DELAYED_IP])) {
		return ;
	}

	ArrayPushArray(g_DelayedBans, data);
}

stock void: Delayed_SaveToFile() {
	new size = ArraySize(g_DelayedBans)
	new data[DelayedData];

	new DelayedDate[16];
	get_time("%Y_%m_%d", DelayedDate, 15);

	new file[64]; getDirByType( DirData:DIR_DATA, file, charsmax(file) , "Delayed/delayed%s.txt", DelayedDate)

	new f = fopen( file, "a+")

	#if defined DELAYED_DEBUG
		PrintMessage("[Delayed] Save to file", data[DD_DELAYED_NAME], data[DD_DELAYED_NAME])
	#endif

	if ( !f )
		return

	for(new i = 0; i< size; i++) {
		ArrayGetArray(g_DelayedBans, i, data)

		fprintf(f, "^"%s^" ^"%s^" ^"%s^" ^"%s^" ^"%s^" ^"%s^" ^n",
			data[DD_DELAYED_NAME], data[DD_DELAYED_STEAM], data[DD_DELAYED_IP], data[DD_DELAYED_UID], data[DD_DELAYED_CDKEY], data[DD_DELAYED_DIVEID])
	}

	fclose(f)
}

stock bool:Delayed_FindFromTemp( data[DelayedData], search[], FindType:type = FT_FIND_ANY) {
	new len = ArraySize(g_DelayedBans);

	for(new i; i<len; i++) {
		ArrayGetArray(g_DelayedBans, i, data)

		if (Delayed_Compare(search, data, type)) {
			return true;
		}
	}

	return false;
}

stock bool:Delayed_Find( data[DelayedData], search[], FindType:type = FT_FIND_ANY, fileName[] = "") {

	if (strlen(fileName) > 3) {
		return Delayed_FindInFile(data, search, type, fileName);
	}

	if (Delayed_FindFromTemp(data, search)) {
		return true;
	}

	new szFile[128];
	new DelayedDir[64];
	getDirByType(DirData:DIR_DATA, DelayedDir, charsmax(DelayedDir), "Delayed/")

	new dir = open_dir(DelayedDir, szFile, charsmax(szFile))
	if(dir)
	{
		do
		{
			if(!Delayed_isDelayedFile(szFile, strlen(szFile)))
				continue;

			format(szFile, charsmax(szFile), "%s%s", DelayedDir,szFile)

			#if defined DELAYED_DEBUG
				PrintMessage("[Delayed] Search File: %s", szFile)
			#endif

			if (Delayed_FindInFile(data, search, type, szFile)) {
				return true;
			}
		}
		while(next_file(dir, szFile, charsmax(szFile)))

		close_dir(dir)
	}

	return false;
}

stock bool:Delayed_FindInFile( data[DelayedData], search[], FindType:type = FT_FIND_ANY, fileName[] = "") {
	new f = fopen(fileName, "r");

	if (!f) {
		return false;
	}

	new fileData[256];
	while ( !feof(f) ) {
		fgets(f, fileData, charsmax(fileData));

		if (!fileData[0] || fileData[0] == '#')
			continue;

		parse(fileData, data[DD_DELAYED_NAME], 31, data[DD_DELAYED_STEAM], MAX_IP_LENGHT - 1, data[DD_DELAYED_IP], MAX_IP_LENGHT - 1, data[DD_DELAYED_UID], 15, \
		data[DD_DELAYED_CDKEY], 32, data[DD_DELAYED_DIVEID], 15);

		#if defined DELAYED_DEBUG
			PrintMessage("[Delayed] IP: %s | Search: %s", data[DD_DELAYED_IP],  search)
		#endif

		if (Delayed_Compare(search, data, type)) {
			return true;
		}

	}

	return false;
}

stock bool:Delayed_Compare(search[], data[DelayedData], FindType:type = FT_FIND_ANY) {
	switch( type ) {
			case FT_FIND_STEAM: if ( equali(search, data[DD_DELAYED_STEAM]) ) return true;
			case FT_FIND_IP: if ( equali(search, data[DD_DELAYED_IP]) ) return true;
			case FT_FIND_NAME: if ( equali(search, data[DD_DELAYED_NAME]) ) return true;

			default :
				if (
					equali(search, data[DD_DELAYED_NAME])	||
					equali(search, data[DD_DELAYED_STEAM])  ||
					equali(search, data[DD_DELAYED_IP])		||
					equali(search, data[DD_DELAYED_UID])	||
					equali(search, data[DD_DELAYED_CDKEY])  ||
					equali(search, data[DD_DELAYED_DIVEID])
				)
				return true;
	}

	return false;
}

stock bool:Delayed_isDelayedFile(CurrNAME[], len  )
{
	static S_TRY[] = ".txt"

	if ( ( len >= 4 ) && ( CurrNAME[ len - 1 ] == S_TRY[ 3 ] ) &&
	( CurrNAME[ len - 2 ] == S_TRY[ 2 ] ) && ( CurrNAME[ len - 3 ] == S_TRY[ 1 ] ) &&
	( CurrNAME[ len - 4 ] == S_TRY[ 0 ] ) )
	return true;

	return false;
}

stock bool:Delayed_IsEnable() {
	return bool:(get_pcvar_num(g_DelayedCvars[DC_DELAYED_STATUS]))
}