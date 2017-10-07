<?php

//momo
class CalendarStateBiz
{
    public static function Items()
    {
        return Data::FormatSuccess(CalendarStateCls::Count(), CalendarStateCls::Items());
    }
}

?>