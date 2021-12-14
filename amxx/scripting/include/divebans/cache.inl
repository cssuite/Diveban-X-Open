#if defined _cache_inc
    #endinput
#endif

#define _cache_inc

#include <amxmodx>

new cacheMapFile[] = "dbx_cache.cache"

// Кэш на 2 карты
#define CACHE_UNTIL_MAP_END 0
#define CACHE_FOR_ONE_MAP 1

//#define CACHE_DEBUG

const CACHE_TIME_IN_MAPS = CACHE_FOR_ONE_MAP;

enum CacheCvars {
	CACHE_CVAR_STATUS = 0,
	CACHE_CVAR_TIME,

}

new g_CacheCvars[CacheCvars]

stock Cache_registerCvars() {
	g_CacheCvars[CACHE_CVAR_STATUS] = 	register_cvar("divebans_cache_status", "0");
	g_CacheCvars[CACHE_CVAR_TIME] = 	register_cvar("divebans_cache_time", "2");
}

stock bool:Cache_IsEnabled() {
	return bool:(get_pcvar_num(g_CacheCvars[CACHE_CVAR_STATUS]) > 0);
}

stock Cache_Init() {
	gTrieCache = TrieCreate()
	gArrayCache = ArrayCreate(CacheArray)

	Cache_registerCvars()
	Cache_LoadFromFile(gTrieCache, gArrayCache)
}

stock Cache_End() {
	// Make Cache at the end of map
	Cache_MakeCache()

	TrieDestroy(gTrieCache)
	ArrayDestroy(gArrayCache);
}

stock Cache_LoadFromFile(&Trie:trie, &Array:array) {

	if (!Cache_IsEnabled()) return;


	new file[64]; getDirByType( DirData:DIR_DATA, file, charsmax(file), cacheMapFile)
	new f = fopen( file, "r")

	#if defined CACHE_DEBUG
		PrintMessage("[Cache] Load Cached players from [%s]", file);
	#endif

	if ( !f ) {
		return ;
	}

	new fData[256], num[6]

	new data[CacheArray];
	while( !feof(f) ) {
		fgets( f, fData, charsmax(fData) );

		if ( !fData[0] || fData[0] != '"' )
			continue;

		#if defined CACHE_DEBUG
		PrintMessage("[Cache] File_LineData: [%s]", fData);
		#endif

		parse( fData, data[CA_IP], MAX_IP_LENGHT - 1, num, charsmax(num))

		data[CA_CACHE_COUNT] = str_to_num(num)
		data[CA_STATUS] = true;

		ArrayPushArray(array, data)
		TrieSetCell(trie, data[CA_IP], ArraySize(array) - 1)
	}

	PrintMessage("Load %d cached players", ArraySize(array))

	fclose(f)
}

stock bool:Cache_IsPlayerCached(id) {
	return bool:TrieKeyExists(gTrieCache, g_info_player[id][CS_PLAYER_IP]) && bool:Cache_IsEnabled();
}

stock Cache_AddPlayer(id) {
	if ( g_player_banned[id] || Cache_IsPlayerCached(id) || !Cache_IsEnabled()) return;

	new data[CacheArray];
	copy(data[CA_IP], MAX_IP_LENGHT - 1, g_info_player[id][CS_PLAYER_IP])
	data[CA_CACHE_COUNT] = get_pcvar_num(g_CacheCvars[CACHE_CVAR_TIME]);
	data[CA_STATUS]	= true;

	#if defined CACHE_DEBUG
		PrintMessage("[Cache] Add Player [%s | %d]", data[CA_IP], data[CA_CACHE_COUNT]);
	#endif

	ArrayPushArray(gArrayCache, data)
	TrieSetCell(gTrieCache, g_info_player[id][CS_PLAYER_IP], ArraySize(gArrayCache) - 1);
}

stock bool:Cache_RemoveByIP( ip[] ) {
	if (! TrieKeyExists(gTrieCache, ip)) {
		return false;
	}

	static arrayID;
	TrieGetCell(gTrieCache, ip,  arrayID);

	new data[CacheArray];
	ArrayGetArray(gArrayCache, arrayID, data);

	#if defined CACHE_DEBUG
		PrintMessage("[Cache] Remove from Cache [IP %s | Cnt %d | ArrayId %d]", data[CA_IP], data[CA_CACHE_COUNT], arrayID);
	#endif

	data[CA_STATUS] = false;
	ArraySetArray(gArrayCache, arrayID, data)

	ArrayDeleteItem(gArrayCache, arrayID)
	TrieDeleteKey(gTrieCache, ip)

	return true;
}

stock bool:Cache_RemovePlayer(id) {
	return Cache_RemoveByIP(g_info_player[id][CS_PLAYER_IP]);
}

stock Cache_MakeCache() {
	if ( !Cache_IsEnabled() ) return;

	new file[64]; getDirByType( DirData:DIR_DATA, file, charsmax(file), cacheMapFile)
	new f = fopen( file, "w")

	new size = ArraySize(gArrayCache);
	new data[CacheArray]
	for(new i; i<size; i++) {
		ArrayGetArray(gArrayCache, i, data)
		data[CA_CACHE_COUNT] --;

		if (data[CA_CACHE_COUNT] <= 0 || data[CA_STATUS] == false) {
			continue;
		}

		#if defined CACHE_DEBUG
		PrintMessage("[Cache] Save [IP %s | Cnt %d]", data[CA_IP], data[CA_CACHE_COUNT]);
		#endif

		fprintf(f, "^"%s^" ^"%d^"^n", data[CA_IP], data[CA_CACHE_COUNT])

	}

	fclose(f)
}