<?php
class UploadBiz
{

	public static function Item($id)
	{
		return VendorCls::Instance ()->Item ( $id );
	}

	public static function Count($where)
	{
		return VendorCls::Count ( $where );
	}

	public static function Items($where, $order, $start, $size)
	{
		return VendorCls::Items ( $where, $order, $start, $size );
	}

	public static function Add($furl,$filename,$savename,$downloads)
	{
		$rs = VendorCls::Add ( $furl,$filename,$savename,0 );
		Json::ReturnSuccessID ('', '成功');
		return $rs;
	}

	public static function Edit($id, $code, $name, $taxnumber, $place, $iscustomer, $isvendor, $memo)
	{
		VendorCls::Edit ( $id, $code, $name, $taxnumber, $place, $iscustomer, $isvendor, $memo );
	}

	public static function Delete($id)
	{
		try
		{
			DB::db ()->BeginTransaction ();
			
			// TODO: 检查是否能够删除, 如 存在发布的商品等
			
			VendorCls::Delete ( $id );
			
			DB::db ()->Commit ();
		}
		catch ( Exception $e )
		{
			DB::db ()->RollBack ();
			Json::ReturnError ( ALERT_ERROR );
		}
		
		Json::ReturnSuccess ();
	}
}
?>