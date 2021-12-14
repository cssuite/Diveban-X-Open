#if defined _license_included
    #endinput
#endif

#define _license_included

#include <amxmisc>
#include <sockets>

//new bool:PluginEnable = false;

stock bool:GetCFGKey(FileCFG[], key[], output[], len)
{
	new f = fopen(FileCFG, "r");
	if (!f)	return false;

	while( !feof(f) )
    {
		fgets(f, output, len)

		if ( containi(output, key ) == -1 )	continue;

		replace( output, len, key, "");

		trim(output);
		remove_quotes(output);

		return fclose(f) && strlen(output) > 1;
	}

	return !fclose(f)
}

stock bool: license ( id = 0 )
{
	// GG
	return id ? true : true;
}