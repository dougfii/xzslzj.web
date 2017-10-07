<?php
class ApproveFieldCls
{
	private static $map = array ( 	//
	
	ApproveObjectCls::CUSTOMER => array ( 	//
	'code' => array ( 'name' => '客户代码' , 'type' => 'TEXT' ) , 	//
	'name' => array ( 'name' => '客户名称' , 'type' => 'TEXT' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::SUPPLIER => array ( 	//
	'code' => array ( 'name' => '供应商代码' , 'type' => 'TEXT' ) , 	//
	'name' => array ( 'name' => '供应商名称' , 'type' => 'TEXT' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::QUOTATION => array ( 	//
	'code' => array ( 'name' => '报价单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '到期日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::CONTRACT => array ( 	//
	'code' => array ( 'name' => '合同号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '到期日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::ORDER => array ( 	//
	'code' => array ( 'name' => '订单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '到期日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'salesid' => array ( 'name' => '销售员' , 'type' => 'USERID' ) , 	//
	'salesdepartmentid' => array ( 'name' => '销售员部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::PURCHASE => array ( 	//
	'code' => array ( 'name' => '采购号' , 'type' => 'TEXT' ) , 	//
	'odate' => array ( 'name' => '到期日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_IN => array ( 	//
	'code' => array ( 'name' => '入库单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '入库日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_OUT => array ( 	//
	'code' => array ( 'name' => '出库单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '出库日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_BORROW => array ( 	//
	'code' => array ( 'name' => '借出单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '借出日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_REVERT => array ( 	//
	'code' => array ( 'name' => '归还单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '归还日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_RETURN => array ( 	//
	'code' => array ( 'name' => '退货单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '退货日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_SENDBACK => array ( 	//
	'code' => array ( 'name' => '退回单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '退回日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_CASTOFF => array ( 	//
	'code' => array ( 'name' => '报废单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '报废日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::INVENTORY_MOVE => array ( 	//
	'code' => array ( 'name' => '移库单号' , 'type' => 'TEXT' ) , 	//
	'repositoryid' => array ( 'name' => '移出仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'dstrepositoryid' => array ( 'name' => '移入仓库' , 'type' => 'REPOSITORYID' ) , 	//
	'odate' => array ( 'name' => '移库日期' , 'type' => 'DATE' ) , 	//
	'totalprice' => array ( 'name' => '产品总价' , 'type' => 'PRICE' ) , 	//
	'carriage' => array ( 'name' => '运费' , 'type' => 'PRICE' ) , 	//
	'expense' => array ( 'name' => '其他费用' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::FINANCE_IMPORT => array ( 	//
	'code' => array ( 'name' => '收款单号' , 'type' => 'TEXT' ) , 	//
	'customerid' => array ( 'name' => '客户' , 'type' => 'CUSTOMERID' ) , 	//
	'inventoryid' => array ( 'name' => '库存单号' , 'type' => 'INVENTORYID' ) , 	//
	'odate' => array ( 'name' => '收款日期' , 'type' => 'DATE' ) , 	//
	'money' => array ( 'name' => '收款金额' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '收款人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '收款人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	ApproveObjectCls::FINANCE_EXPORT => array ( 	//
	'code' => array ( 'name' => '付款单号' , 'type' => 'TEXT' ) , 	//
	'customerid' => array ( 'name' => '客户' , 'type' => 'CUSTOMERID' ) , 	//
	'inventoryid' => array ( 'name' => '库存单号' , 'type' => 'INVENTORYID' ) , 	//
	'odate' => array ( 'name' => '付款日期' , 'type' => 'DATE' ) , 	//
	'money' => array ( 'name' => '付款金额' , 'type' => 'PRICE' ) , 	//
	'ownerid' => array ( 'name' => '付款人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '付款人部门' , 'type' => 'DEPARTMENTID' ) , 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 	//
	                                                                                    //
	
	ApproveObjectCls::PRODUCT_PRODUCT => array ( 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 

	ApproveObjectCls::PAYMENT_APPLY => array ( 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) , 'price' => array ( 'name' => '订货金额' , 'type' => 'PRICE' ) ) , 

	ApproveObjectCls::INQURY => array ( 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 

	ApproveObjectCls::BORROW_APPLY => array ( 	//
	'creatorid' => array ( 'name' => '创建者' , 'type' => 'USERID' ) , 	//
	'creatordepartmentid' => array ( 'name' => '创建者部门' , 'type' => 'DEPARTMENTID' ) ) , 

	ApproveObjectCls::REPLY => array ( 	//
	'ownerid' => array ( 'name' => '负责人' , 'type' => 'USERID' ) , 	//
	'ownerdepartmentid' => array ( 'name' => '负责人部门' , 'type' => 'DEPARTMENTID' ) ) );
	
	//
	private static $operator = array ( '==' => '==' , '>' => '>' , '>=' => '>=' , '<' => '<' , '<=' => '<=' , '!=' => '!=' );

	public static function AllItems()
	{
		return self::$map;
	}

	public static function getFields($object)
	{
		if (array_key_exists ( $object, self::$map )) return self::$map [$object];
		return array ();
	}

	public static function getFieldsMap($object)
	{
		$rx = array ();
		$rs = self::getFields ( $object );
		
		foreach ( $rs as $k => $v )
		{
			$rx [$k] = "{$k} ({$v ['name']})";
		}
		
		return $rx;
	}

	public static function getOperatorMap()
	{
		return self::$operator;
	}

	public static function getFieldName($object, $field)
	{
		$rs = self::getFields ( $object );
		if (array_key_exists ( $field, $rs )) return $rs [$field] ['name'];
		return '';
	}

	public static function getFieldType($object, $field)
	{
		$rs = self::getFields ( $object );
		if (array_key_exists ( $field, $rs )) return $rs [$field] ['type'];
		return '';
	}
}
?>