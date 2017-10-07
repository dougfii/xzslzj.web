<?php
class CodeCls
{
	const PREFIX = '';
	const SUFFIX = '';
	//
	const QUOTATION = 'QU';
	const CONTRACT = 'CT';
	const ORDER = 'OD';
	const PURCHASE = 'PU';
	const INVENTORY_IN = 'IN';
	const INVENTORY_OUT = 'OU';
	const INVENTORY_BORROW = 'BR';
	const INVENTORY_REVERT = 'RV';
	const INVENTORY_RETURN = 'RT';
	const INVENTORY_SENDBACK = 'SD';
	const INVENTORY_CASTOFF = 'CA';
	const INVENTORY_MOVE = 'MV';
	const CUSTOMER = 'CU';
	const SUPPLIER = 'SU';
	const FINANCE_IMPORT = 'IM';
	const FINANCE_EXPORT = 'EX';
	const PRODUCT = 'GT';
	const INQURY = 'IQ';
	const PAYMENT_APPLY = 'AP';
	const BORROW_APPLY = 'BA';
	//
	const SHOP_ORDER = 'GD';
	const SHOP_QUOTATION = 'GQ';

	public static function date()
	{
		return date ( 'Ymd', time () );
	}

	public static function datetime()
	{
		return date ( 'YmdHis', time () );
	}

	private static function serial($num, $len)
	{
		return sprintf ( "%0{$len}d", $num );
	}

	public static function make($type = '', $num = -1, $len = 3, $middle = true)
	{
		if ($num == - 1) return '自动编号';
		$s = $type . self::PREFIX;
		if ($middle) $s .= self::date ();
		$s .= self::SUFFIX . self::serial ( $num, $len );
		return $s;
	}

	public static function split($code)
	{
		return preg_split ( '/([A-Z]{2})(\d{8})(\d{3})/', $code, - 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
	}

	public static function splitSerial($code, $len)
	{
		return preg_split ( '/([A-Z]{2})(\d{' . $len . '})/', $code, - 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
	}

	public static function MakeShopSerial($type)
	{
		return $type . self::datetime () . sprintf ( "%04d", rand ( 1, 9999 ) );
	}

	public static function MakeRandomPassword($len = 6, $upper = true)
	{
		$pass = substr ( md5 ( uniqid ( rand (), true ) ), 0, $len );
		if ($upper) strtoupper ( $pass );
		return $pass;
	}
}
?>