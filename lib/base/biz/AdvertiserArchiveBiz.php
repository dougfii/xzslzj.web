<?php

class AdvertiserArchiveBiz
{
    public static function Clear()
    {
        AdvertiserArchiveCls::Clear();
    }

    public static function Get()
    {
        return Data::FormatSuccess(AdvertiserArchiveCls::Instance()->Get());
    }

    public static function Set($elements)
    {
        AdvertiserArchiveCls::Instance()->Set($elements);
    }
}

?>