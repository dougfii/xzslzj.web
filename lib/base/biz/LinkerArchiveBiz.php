<?php

class LinkerArchiveBiz
{
    public static function Clear()
    {
        LinkerArchiveCls::Clear();
    }

    public static function Get()
    {
        return Data::FormatSuccess(LinkerArchiveCls::Instance()->Get());
    }

    public static function Set($elements)
    {
        LinkerArchiveCls::Instance()->Set($elements);
    }
}

?>