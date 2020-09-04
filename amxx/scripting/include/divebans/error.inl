#if defined _error_included
    #endinput
#endif

#define _error_included

#include <amxmodx>
#include <amxmisc>

stock ErrorDetect(const szMessage[], any:...)
{
	new szMsg[196];
	vformat(szMsg, charsmax(szMsg), szMessage, 3);
	
	new LogDat[16],LogFile[64]
	get_time("%Y_%m_%d", LogDat, 15);
	
	get_basedir(LogFile,63)
	formatex(LogFile,63,"%s/logs/DiveBan/Log_%s.log",LogFile,LogDat);
	
	PluginEnable = false;
	
	new error_msg[256];
	formatex(error_msg, 255, "Plugin [%s] take ERROR:[%s]", PLUGIN, szMsg);
	log_to_file(LogFile,error_msg);
	set_fail_state(error_msg);

	return -1;
}
