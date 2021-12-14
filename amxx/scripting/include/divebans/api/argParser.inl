#if defined _api_arg_parser
    #endinput
#endif

#define _api_arg_parser

#define __ARG_PARSER_VERSION "1"

const API_ARG_PARSE_MAX_LEN = 32;
const API_ARG_PARSE_DEFAULT_TIME = 10080; // 1 week
const API_ARG_PARSE_AUTO_SEARCH = 0;

enum iArgs {
	API_ARG_AUTO_FIND = -1,

	API_ARG_COMMAND = 0,

	API_ARG_FIRST,
	API_ARG_SECOND,
	API_ARG_THIRD
}

/** Parse bantime from console. Support h,d,w,m,y shorts. Example : 1d, 2w, 1m, 5y
 * @param arg_num - time arg | 0 - auto
 * @return int time
 *  */
stock Api_ArgParse_Time(api[]) {
	if ( !api[0] || !isdigit(api[0])) {
		return API_ARG_PARSE_DEFAULT_TIME;
	}

	server_print("Time: %s", api)
	return __ArgParse_TimeShort(api);
}

/** Parse player from arg
 * @param arg_num
 * @param search_type: combine of 
 * 	@option "u" - find by UserID
 * 	@option "s" - find by SteamID
 * 	@option "i" - find by IP
 *  @option "n" - find by Name
 *  */
stock Api_ArgParse_Player(api[], search_type[] = "") {
	new player = -1;

	new search[16];

	if (!search_type[0])
		formatex(search, charsmax(search), "uisn")
	else
		copy(search, charsmax(search), search_type)


	server_print("Player: %s (%s)", api,search)
	new _len = strlen(search)
	for(new i; i < _len; i++) {

		switch (search[i])
		{
			case 'u' : player = find_player("k", str_to_num(api[1]))
			case 'i' : player = find_player("d", api)
			case 's' : player = find_player("c", api)
			case 'n' : player = find_player("ab", api)
		}

		if (player > 0) 
			break
	}

	return player;
}

stock __ArgParse_Time_Auto() {
	new api[API_ARG_PARSE_MAX_LEN];

	new arg_len = read_argc();
	new is_ip, is_uid, is_steam;
	for(new i = 1; i<arg_len; i++) {
		read_argv( i, api, charsmax(api));

		is_ip = contain(api, ".");
		is_uid = contain(api, "#");
		is_steam = contain(api, "STEAM");

		if(is_ip == -1 && is_uid == -1 && is_steam == -1 && isdigit(api[0])) {
			return __ArgParse_TimeShort(api);
		}

	}

	return API_ARG_PARSE_DEFAULT_TIME
}

stock __ArgParse_TimeShort(const str[])
{
	new temp[API_ARG_PARSE_MAX_LEN];
	new coof = 1;

	for (new i = 0; str[i] != 0 && i < API_ARG_PARSE_MAX_LEN - 1; i++)
	{
		if (isdigit(str[i]))
		{
			temp[i] = str[i];
			continue;
		}

		switch (tolower(str[i]))
		{
			case 'h':
				coof = 60;
			case 'd':
				coof = 60 * 24;
			case 'w':
				coof = 60 * 24 * 7;
			case 'm':
				coof = 60 * 24 * 30;
			case 'y':
				coof = 60 * 24 * 365;
		}

		break;
	}

	new ret = 0;
	if (is_str_num(temp))
		ret = str_to_num(temp);

	return ret * coof;
}