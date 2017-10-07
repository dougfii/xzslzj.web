<?php
class ApproveObjectCls
{
	const CUSTOMER = 10;
	const SUPPLIER = 20;
	const QUOTATION = 100;
	const CONTRACT = 110;
	const ORDER = 120;
	const INQURY = 140;
	const PURCHASE = 800;
	const INVENTORY_IN = 900;
	const INVENTORY_OUT = 910;
	const INVENTORY_BORROW = 920;
	const INVENTORY_REVERT = 930;
	const INVENTORY_RETURN = 940;
	const INVENTORY_SENDBACK = 950;
	const INVENTORY_CASTOFF = 960;
	const INVENTORY_MOVE = 970;
	const INVENTORY_INIT = 980; // 无用,初始化库存不需要审批
	const FINANCE_IMPORT = 1200;
	const FINANCE_EXPORT = 1210;
	const PRODUCT_PRODUCT = 9000;
	const PRODUCT_PRODUCT2 = 10000;
	const PAYMENT_APPLY = 810;
	const BORROW_APPLY = 820;
	const REPLY = 160;
	//
	private static $map = array ( 	//
	10 => '客户' , 	//
	20 => '供应商' , 	//
	
	100 => '报价单' , 	//
	110 => '合同' , 	//
	120 => '订单' , 	//
	140 => '询价单' , 160 => '报价单(询)' , 

	800 => '采购' , 	//
	
	900 => '库存-入库' , 	//
	910 => '库存-出库' , 	//
	920 => '库存-借出' , 	//
	930 => '库存-归还' , 	//
	940 => '库存-客户退货' , 	//
	950 => '库存-退回供应商' , 	//
	960 => '库存-报废' , 	//
	970 => '库存-移库' , 	//
	980 => '库存-初始化' , 	//
	
	1200 => '财务-收款' , 	//
	1210 => '财务-付款' , 	//
	
	9000 => '产品-列表' , 	//
	10000 => '电商产品-列表' , 	//
	820 => '订单-借出申请' , 	//
	810 => '采购-付款申请' );

	public static function AllItems()
	{
		return self::$map;
	}

	public static function GetName($id)
	{
		if (array_key_exists ( $id, self::$map ))
		{
			return self::$map [$id];
		}
		return false;
	}
}
?>