#if defined _tools_included
	#endinput
#endif

#define _tools_included

#include <amxmodx>

/* Набор глобальных функций и констант для плагина */
// -------------------------------------------------

new const pluginName[] = "DiveBanX";
const SERVER_ID = 0;

#define getDir(%0,%1,%2) get_localinfo(%0,%1,%2)

enum DirData {
	DIR_CONFIG = 0,
	DIR_LOG,
	DIR_DATA
}

stock dirTypeName[DirData][] = {
	"amxx_configsdir",
	"amxx_logs",
	"amxx_datadir"
}
/* Return path of dirType with plugin_name. For Example: dirType=DIR_LOGS, addons/amxmodx/logs/Plugin/
	@param DirData:dirType dir const
	@param out[] - result string
	@param len
	@param addToEnd[] - string that add in the end of path

	@return int
	
 */
stock getDirByType( DirData:dirType, out[], len, addToEnd[], any:...) {
	
	new base[64], szMsg[64];

	getDir( dirTypeName[dirType],base, charsmax(base));
	vformat(szMsg, charsmax(szMsg), addToEnd, 5);

	formatex(out, len, "%s/%s/", base, pluginName);
	if ( !dir_exists(out) ) mkdir(out);

	return formatex(out, len, "%s/%s/%s", base,pluginName, szMsg);
}
/* Create path of dirType with plugin_name. For Example: dirType=DIR_LOGS, addons/amxmodx/logs/Plugin/
	@param DirData:dirType - dir const

	@return bool
 */
stock bool:createDirByType(DirData:dirType) {
	new base[64];
	getDir( dirTypeName[dirType],base, charsmax(base));

	formatex(base, charsmax(base), "%s/%s", base,pluginName)
	return dir_exists(base) ? true : !mkdir(base);

}

stock SQL_SafeString(input[], len)
{
	replace_all(input, len, "'", "*");
	replace_all(input, len, "^"", "*"); // ^"
	replace_all(input, len, "`", "*");
}
stock Print( const id, szMessage[], any:...)
{
	new szMsg[196];
	vformat(szMsg, charsmax(szMsg), szMessage, 3);

	return  id ? client_print(id, print_chat, "[%s %s] %s", PLUGIN, VERSION, szMsg) : server_print("[%s %s] %s", PLUGIN, VERSION, szMsg)
}
stock bool:IsValidStr( str[] ) {
	return bool:str[0];
}
stock is_valid_steamid( UserAuthID[] )
{
	if (equali(UserAuthID, "STEAM_ID_LAN") || equali(UserAuthID, "STEAM_ID_PENDING") || equali(UserAuthID, "VALVE_ID_LAN") || equali(UserAuthID,"VALVE_ID_PENDING") || equali(UserAuthID, "STEAM_666:88:666"))
		return 0;

	return 1
}

stock get_ip_subnet( const type, ipData[], len, subnet[], slen) {
	new ip[22]; copy(ip, charsmax(ip), ipData)
	replace_all(ip,len,"."," ")

	new okteta1[4],okteta2[4],okteta3[4],okteta4[4]
	parse(ip,okteta1,charsmax(okteta1),okteta2,charsmax(okteta2),okteta3,charsmax(okteta3),okteta4,charsmax(okteta4))

	switch (type)
	{
		case 0: 	formatex(subnet, slen, "%s.%s.%s", okteta1, okteta2,okteta3);
		default: 	formatex(subnet, slen, "%s.%s", okteta1, okteta2);
	}
	
	subnet[slen] = '^0';
}

stock logQuery( qstring[] ) {
	new LogDat[16]
	get_time("%Y_%m_%d", LogDat, 15);

	new LogFile[64];
	getDirByType(DirData:DIR_LOG, LogFile, charsmax(LogFile), "QueryLog_debug_%s.log", LogDat)

	write_file(LogFile, qstring, -1);
}

/*
get_dir_vars( out, len, saveToPath[] = "") {
	if ( !strlen(saveToPath) )
		return getDirByType(DirData:DIR_DATA, dataDir, charsmax(dataDir), "assign.txt");

	if ( containi(saveToPath, "assign.txt") == -1 ) {
		if ( dir_exists(saveToPath) )
			add(saveToPath, charsmax(saveToPath), "/assign.txt")
		else return 0;
	}

	return formatex(out,len, "%s", saveToPath);
}
bool:set_stored_var( const key[], const value[], saveToPath[] = "" ) {
	new filename[64];

	get_dir_vars(filename, charsmax(filename), saveToPath);

	static const temp_vault_name[] = "assign_set_data.txt"

	new file = fopen(temp_vault_name, "wt");
	new vault = fopen(filename, "rt");

	new _data[128], _key[64], _other[3],bool:replaced = false;
	while( !feof(vault) )
	{
		fgets(vault, _data, sizeof(_data) - 1);
		parse(_data, _key, sizeof(_key) - 1, _other, sizeof(_other) - 1);
		
		if( equal(_key, key) && !replaced )
		{
			fprintf(file, "^"%s^" ^"%s^"^n", key, data);
			replaced = true;
		}
		else fputs(file, _data);
	}
	
	if( !replaced ) fprintf(file, "^"%s^" ^"%s^"^n", key, data);
	
	fclose(file);
	fclose(vault);
	
	delete_file(filename);
	
	while( !rename_file(temp_vault_name, filename, 1) ) { }
	return true;
}

bool:get_stored_var( const key[], out[], len, saveToPath[] = "") {
	new filename[64];
	get_dir_vars(filename, charsmax(filename), saveToPath);
	
	new vault = fopen(filename, "rt");
	new _data[128], _key[64];
	
	while( !feof(vault) )
	{
		fgets(vault, _data, sizeof(_data) - 1);
		parse(_data, _key, sizeof(_key) - 1, data, len);
		
		if( equal(_key, key) )
			return fclose(vault);
	}
	
	fclose(vault);
	return !copy(data, len, "");
}
*/
stock bool:strcopy(  const pre[], const post[], const str[], out[] ) {
	new find = containi(str, pre);
	new len = strlen(pre);
	if ( find == -1 ) return false;

	find += len;
	new end = containi(str, post);

	//server_print("Diff: %d | %d | %d",find,end, end-find)
	return bool:copy(out, end - find, str[find])
}
