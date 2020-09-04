#if defined _cmd_delayed_ban_inc
    #endinput
#endif

#define _cmd_delayed_ban_inc

#include <amxmodx>

stock AddDelayedBan(id, data[DelayedData], minutes, reason[], szBanType[] = "") {
	new bantype[36]

	if (szBanType[0])			copy(bantype, 35, szBanType)
	else						GetBanType( bantype,charsmax(bantype))

	new _data[BannedData];
	API_ConvertToBannedData(_data, data[DD_DELAYED_NAME], data[DD_DELAYED_STEAM], data[DD_DELAYED_IP], data[DD_DELAYED_IP],\
		data[DD_DELAYED_UID], data[DD_DELAYED_DIVEID], data[DD_DELAYED_CDKEY], minutes, reason, bantype)

	Ban(id, _data, 'e')
}