<?php

class CopyrightArchiveBiz
{
    public static function Clear()
    {
        CopyrightArchiveCls::Clear();
    }

    public static function Get()
    {
        return Data::FormatSuccess(CopyrightArchiveCls::Instance()->Get());
    }

    public static function Set($elements)
    {
        CopyrightArchiveCls::Instance()->Set($elements);
    }
}

?>