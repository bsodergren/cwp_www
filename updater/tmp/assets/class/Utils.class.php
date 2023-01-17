<?php
class Utils
{

    public static function toint($string)
	{
		
		$string_ret = str_replace(",","",$string);
		return $string_ret;
	}
}


