#if defined _api_secret_key
    #endinput
#endif

#define _api_secret_key

#include <amxmodx>

#define SK_CONFIG_PATH

#if !defined __ARG_PARSER_VERSION
	#define __IMPORT_FROM_ARG_PARSER

	enum iArgs {
		API_ARG_AUTO_FIND = -1,

		API_ARG_COMMAND = 0,

		API_ARG_FIRST,
		API_ARG_SECOND,
		API_ARG_THIRD
	}
#endif

stock Api_isSecrectKeyArg( const arg = API_ARG_AUTO_FIND) {
	new api[32];

	new secretKey[32];
	__GetSecretKey()

	if ( arg = API_ARG_AUTO_FIND ) {
		for(new i; i< read_args(); i++) {
			read_argv(i, api, charsmax(api))

		}
	}
}

stock __GetSecretKey(secretKey[], len) {
	return formatex(secretKey, len, "unkown")
}