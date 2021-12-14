/* Time specific functions
*
* by the AMX Mod X Development Team
*
* This file is provided as is (no warranties).
*/

#if defined _time_included
  #endinput
#endif
#define _time_included


#define _YEARS
/* Time unit types for get_time_length() */
enum 
{
    timeunit_seconds = 0,
    timeunit_minutes,
    timeunit_hours,
    timeunit_days,
    timeunit_weeks,
    #if defined _YEARS
    timeunit_month,
    timeunit_years,
    #endif
};

// seconds are in each time unit
#define SECONDS_IN_MINUTE 60
#define SECONDS_IN_HOUR   3600
#define SECONDS_IN_DAY    86400
#define SECONDS_IN_WEEK   604800
#if defined _YEARS
#define SECONDS_IN_MONTH  2592000
#define SECONDS_IN_YEAR   31536000
#endif

/* Stock by Brad */
stock get_time_length(id, unitCnt, type, output[], outputLen)
{
// IMPORTANT: 	You must add register_dictionary("time.txt") in plugin_init()

// id:          The player whose language the length should be translated to (or 0 for server language).
// unitCnt:     The number of time units you want translated into verbose text.
// type:        The type of unit (i.e. seconds, minutes, hours, days, weeks) that you are passing in.
// output:      The variable you want the verbose text to be placed in.
// outputLen:	The length of the output variable.

    if (unitCnt > 0)
    {
        // determine the number of each time unit there are
        new weekCnt = 0, dayCnt = 0, hourCnt = 0, minuteCnt = 0, secondCnt = 0;
	
	#if defined _YEARS
	new monthCnt = 0;
	new yearCnt = 0;
	#endif
        switch (type)
        {
            case timeunit_seconds: secondCnt = unitCnt;
            case timeunit_minutes: secondCnt = unitCnt * SECONDS_IN_MINUTE;
            case timeunit_hours:   secondCnt = unitCnt * SECONDS_IN_HOUR;
            case timeunit_days:    secondCnt = unitCnt * SECONDS_IN_DAY;
            case timeunit_weeks:   secondCnt = unitCnt * SECONDS_IN_WEEK;
	    #if defined _YEARS
	    case timeunit_month:   secondCnt = unitCnt * SECONDS_IN_MONTH;
	    case timeunit_years:   secondCnt = unitCnt * SECONDS_IN_YEAR;
	    #endif
        }

	#if defined _YEARS
	yearCnt = secondCnt / SECONDS_IN_YEAR;
        secondCnt -= (yearCnt * SECONDS_IN_YEAR);
	
	monthCnt = secondCnt / SECONDS_IN_MONTH;
        secondCnt -= (monthCnt * SECONDS_IN_MONTH);
	#endif

        weekCnt = secondCnt / SECONDS_IN_WEEK;
        secondCnt -= (weekCnt * SECONDS_IN_WEEK);

        dayCnt = secondCnt / SECONDS_IN_DAY;
        secondCnt -= (dayCnt * SECONDS_IN_DAY);

        hourCnt = secondCnt / SECONDS_IN_HOUR;
        secondCnt -= (hourCnt * SECONDS_IN_HOUR);

        minuteCnt = secondCnt / SECONDS_IN_MINUTE;
        secondCnt -= (minuteCnt * SECONDS_IN_MINUTE);

        // translate the unit counts into verbose text
        new maxElementIdx = -1;
	#if defined _YEARS
        new timeElement[7][33];
	#else
	new timeElement[5][33];
	#endif

	#if defined _YEARS
	if (yearCnt > 0)
            format(timeElement[++maxElementIdx], 32, "%i %L", yearCnt, id, (yearCnt == 1) ? "TIME_ELEMENT_YEAR" : "TIME_ELEMENT_YEARS");
	if (monthCnt > 0)
            format(timeElement[++maxElementIdx], 32, "%i %L", monthCnt, id, (monthCnt == 1) ? "TIME_ELEMENT_MONTH" : "TIME_ELEMENT_MONTHS");
	#endif
        if (weekCnt > 0)
            format(timeElement[++maxElementIdx], 32, "%i %L", weekCnt, id, (weekCnt == 1) ? "TIME_ELEMENT_WEEK" : "TIME_ELEMENT_WEEKS");
        if (dayCnt > 0)
            format(timeElement[++maxElementIdx], 32, "%i %L", dayCnt, id, (dayCnt == 1) ? "TIME_ELEMENT_DAY" : "TIME_ELEMENT_DAYS");
        if (hourCnt > 0)
            format(timeElement[++maxElementIdx], 32, "%i %L", hourCnt, id, (hourCnt == 1) ? "TIME_ELEMENT_HOUR" : "TIME_ELEMENT_HOURS");
        if (minuteCnt > 0)
            format(timeElement[++maxElementIdx], 32, "%i %L", minuteCnt, id, (minuteCnt == 1) ? "TIME_ELEMENT_MINUTE" : "TIME_ELEMENT_MINUTES");
        if (secondCnt > 0)
            format(timeElement[++maxElementIdx], 32, "%i %L", secondCnt, id, (secondCnt == 1) ? "TIME_ELEMENT_SECOND" : "TIME_ELEMENT_SECONDS");

        switch(maxElementIdx)
        {
            case 0: format(output, outputLen, "%s", timeElement[0]);
            case 1: format(output, outputLen, "%s %L %s", timeElement[0], id, "TIME_ELEMENT_AND", timeElement[1]);
            case 2: format(output, outputLen, "%s, %s %L %s", timeElement[0], timeElement[1], id, "TIME_ELEMENT_AND", timeElement[2]);
            case 3: format(output, outputLen, "%s, %s, %s %L %s", timeElement[0], timeElement[1], timeElement[2], id, "TIME_ELEMENT_AND", timeElement[3]);
            case 4: format(output, outputLen, "%s, %s, %s, %s %L %s", timeElement[0], timeElement[1], timeElement[2], timeElement[3], id, "TIME_ELEMENT_AND", timeElement[4]);
	    #if defined _YEARS
	    case 5: format(output, outputLen, "%s, %s, %s, %s %s %L %s", timeElement[0], timeElement[1], timeElement[2], timeElement[3], timeElement[4], id, "TIME_ELEMENT_AND", timeElement[5]);
	    case 6: format(output, outputLen, "%s, %s, %s, %s %s %s %L %s", timeElement[0], timeElement[1], timeElement[2], timeElement[3],timeElement[4], timeElement[5], id, "TIME_ELEMENT_AND", timeElement[6]);
	    #endif
        }
    }
    else if (!unitCnt)
	format(output, outputLen, "%L", id, "TIME_ELEMENT_PERMANENTLY");
}
