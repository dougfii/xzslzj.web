<?php
class VendorMod extends BaseMod
{
	protected static $LIMIT_FORMAT_IMAGE = array ( 'gif' , 'jpg' , 'jpeg' , 'png' , 'bmp', 'pdf', 'zip' );
	protected static $MAX_SIZE = 20971520; // 20M
	

	public function index()
	{
	}
	
	//供应商列表
	public function VendorList()
	{
		$this->Header ();
		
		if (! P::Instance ( $this->Uid () )->isCanView ( R::VENDOR_VENDOR )) //供应商管理 42
		{
			UtilMod::MessageBoxUnableList ();
			return;
		}
		$findname = $this->Req ( 'findname', '', 'str' );
		
		$gid = UserCls::Instance()->GetGid($this->Uid());
		$GRWhere = GroupCls::GetChildIds($gid);
		$GRGID = implode(",",$GRWhere);
		$where = 'WHERE del=false AND gid in ('.$GRGID.')';
		
		if (! empty ( $findname ))
		{
			$findname = strtolower ( $findname );
			$where .= " AND (LOWER(code) like '%{$findname}%' OR LOWER(name) like '%{$findname}%' OR LOWER(taxnumber) like '%{$findname}%' OR LOWER(place) like '%{$findname}%')";
		}
		
		$v = View::Factory ( 'VendorList' );
		
		$url = Url::QUERY_STRING_DELETE ( array ( 'pn' ) );
		$pagesize = PAGE_SIZE_DEFAULT;
		$count = VendorBiz::Count ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
		$order = 'ORDER BY id DESC';
		
		$rs = VendorBiz::Items ( $where, $order, $pos, $pagesize );
		$v->rs = $rs;
		$v->orders = HTML::Orders ( $url, array ( 'username' , 'name' , 'serial' , 'act' , 'time' ) );
		$v->paged = HTML::PageGroup ( $count, $this->Page (), $url, $pagesize );
		$v->findname = $findname;
		
		echo $v->Render ();
		$this->Footer ();
	}
	
	//业务伙伴信息
	public function VendorSelect()
	{
		$this->Header ();
		
		if (! P::Instance ( $this->Uid () )->isCanView ( R::VENDOR_VENDORSELECT )) //业务伙伴信息 41
		{
			UtilMod::MessageBoxUnableList ();
			return;
		}
		$code = $this->Req( 'code', '', 'str'); 
		$name = $this->Req( 'name', '', 'str');
		$taxnumber = $this->Req( 'taxnumber', '', 'str');
		$iscustomer = $this->Req( 'iscustomer', 0, 'int');
		$isvendor = $this->Req( 'isvendor', 0, 'int');
		if ($iscustomer != 1) $iscustomer = 0;
		if ($isvendor != 1) $isvendor = 0;

		$gid = UserCls::Instance()->GetGid($this->Uid());
		$GRWhere = GroupCls::GetChildIds($gid);
		$GRGID = implode(",",$GRWhere);
		$where = 'WHERE del=false AND gid in ('.$GRGID.')';

		if (! empty ( $taxnumber )) $where .= ' AND (LOWER(taxnumber) like \'%' . strtolower ( $taxnumber ) . '%\') ';
		if (! empty ( $name )) $where .= ' AND (LOWER(name) like \'%' . strtolower ( $name ) . '%\') ';
		if (! empty ( $code )) $where .= ' AND (LOWER(code) like \'%' . strtolower ( $code ) . '%\') ';
		if ($iscustomer == 1) $where .= ' AND iscustomer=true ';
		if ($isvendor == 1) $where .= ' AND isvendor=true ';
	
		$v = View::Factory ( 'VendorSelect' );
	
		$url = Url::QUERY_STRING_DELETE ( array ( 'pn' ) );
		$pagesize = PAGE_SIZE_DEFAULT;
		$count = VendorBiz::Count ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
		$order = 'ORDER BY id DESC';
		
		$rs = VendorBiz::Items ( $where, $order, $pos, $pagesize );
		$v->rs = $rs;
		$v->orders = HTML::Orders ( $url, array ( 'username' , 'name' , 'serial' , 'act' , 'time' ) );
		$v->paged = HTML::PageGroup ( $count, $this->Page (), $url, $pagesize );
	
		echo $v->Render ();
		$this->Footer ();
	}
	
	//供应商列表
	public function VendorProductList()
	{
		$this->Header ();
		if (! P::Instance ( $this->Uid () )->isCanView ( R::VENDOR_PRODUCT )) //供应商商品审核 43
		{
			UtilMod::MessageBoxUnableList ();
			return;
		}
		
		$findname = $this->Req ( 'findname', '', 'str' );
		$status = $this->Req( 'status', 0, 'int');
		
		$gid = UserCls::Instance()->GetGid($this->Uid());
		$GRWhere = GroupCls::GetChildIds($gid);
		$GRGID = implode(",",$GRWhere);
		$where = 'WHERE del=false AND gid in ('.$GRGID.')';
		
		if (! empty ( $findname ))
		{
			$findname = strtolower ( $findname );
			$where .= " AND (LOWER(code) like '%{$findname}%' OR LOWER(name) like '%{$findname}%' OR LOWER(taxnumber) like '%{$findname}%' OR LOWER(place) like '%{$findname}%')";
		}
		if ($status == -1) $where .= ' AND status = -1 ';
		
		$v = View::Factory ( 'VendorProductList' );
	
		$url = Url::QUERY_STRING_DELETE ( array ( 'pn' ) );
		$pagesize = PAGE_SIZE_DEFAULT;
		$count = VendorBiz::Count ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
		$order = 'ORDER BY id DESC';
		
		$rs = VendorBiz::Items ( $where, $order, $pos, $pagesize );
		$v->rs = $rs;
		$v->orders = HTML::Orders ( $url, array ( 'username' , 'name' , 'serial' , 'act' , 'time' ) );
		$v->paged = HTML::PageGroup ( $count, $this->Page (), $url, $pagesize );
		$v->findname = $findname;
		
		echo $v->Render ();
		$this->Footer ();
	}
	
	//供应商审核
	public function VendorProductCheck()
	{
		$this->Header ();
		
		$id = $this->Req ( 'id', 0, 'int' );
	
		$v = View::Factory ( 'VendorProductCheck' );
		$v->rs = VendorBiz::Item ( $id );
	
		$where = '';
		$pagesize = PAGE_SIZE_DEFAULT;
		$count = VendorBiz::Count ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
		$order = 'ORDER BY id ASC';
		
		$rs2 = VendorCls::ItemVendorProductCheck ( $id, $where, $order, $pos, $pagesize );
		$v->rs2 = $rs2;
	
		echo $v->Render ();
		$this->Footer ();
	}
	
	//导入供应商
	public function VendorImport()
	{
		$this->Header ();
	
		$where = '';
		$order ='';
		$pagesize = PAGE_SIZE_MEDIUM;
		$count = ImportBiz::Count($where);
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
	
		$url = Url::QUERY_STRING_DELETE ( array ( 'pn' ) );
	
		$view = View::Factory ( 'ImportVendorAdd' );
		$view->rs = ImportCls::ImportVendorList( $where, $order, $pagesize, $pos);
		$view->paged = HTML::PageGroup ( $count, $this->Page (), $url, $pagesize );
	
		echo $view->render ();
		$this->Footer ();
	}
	
	//添加供应商
	public function VendorAdd()
	{
		$this->Header ();
		$v = View::Factory ( 'VendorAdd' );
		echo $v->Render ();
		$this->Footer ();
	}
	
	//添加供应商地址
	public function VendorAddressAdd()
	{
		$this->Header ();
		$v = View::Factory ( 'VendorAddressAdd' );
		echo $v->Render ();
		$this->Footer ();
	}
	
	//查看供应商
	public function VendorSee()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		$this->Header ();
		$v = View::Factory ( 'VendorSee' );
		$v->rs = VendorBiz::Item ( $id );
	
		$where = '';
		$pagesize = PAGE_SIZE_DEFAULT;
		$count = VendorBiz::Count ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
	
		$rs2 = VendorCls::ItemsTo ( $id, $where, $this->Order (), $pos, $pagesize );
		$v->rs2 = $rs2;
		
		$rs3 = UploadCls::ItemsTo( $id, $where, $this->Order (), $pos, $pagesize );
		$v->rs3 = $rs3;

 		$rs4 = VendorCls::ItemsToContact ( $id );
 		$v->rs4 = $rs4;
	
		echo $v->Render ();
		$this->Footer ();
	}
	
	//编辑供应商
	public function VendorEdit()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		$this->Header ();
		$v = View::Factory ( 'VendorEdit' );
		$v->rs = VendorBiz::Item ( $id );
		
		$where = '';
		$pagesize = PAGE_SIZE_DEFAULT;
		$count = VendorBiz::Count ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
		
		$rs2 = VendorCls::ItemsTo ( $id, $where, $this->Order (), $pos, $pagesize );
		$v->rs2 = $rs2;
		
		$rs4 = VendorCls::ItemsToContact ( $id );
		$v->rs4 = $rs4;

		echo $v->Render ();
		$this->Footer ();
	}
	
	//编辑供应商地址
	public function VendorAddressEdit()
	{
		$id = $this->Req( 'id', 0, 'int');
		$this->Header ();
		$v = View::Factory ( 'VendorAddressEdit' );
		
		$rs2 = VendorCls::ItemsAddressTo ( $id );
		$v->rs2 = $rs2;

		echo $v->Render ();
		$this->Footer ();
	}
	
	//添加供应商
	public function OnVendorAdd()
	{
		$code = $this->Req ( 'code', '', 'str' );
		$name = $this->Req ( 'name', '', 'str' );
		$taxnumber = $this->Req ( 'taxnumber', '', 'str' );
		$place = $this->Req ( 'place', '', 'str' );
		//$iscustomer = $this->Req ( 'iscustomer', 0, 'int' );
		//$isvendor = $this->Req ( 'isvendor', 0, 'int' );
		$memo = $this->Req ( 'memo', '', 'str' );
		$gid = UserCls::Instance()->GetGid($this->Uid());
		
		$id = VendorBiz::Add($gid, $code, $name, $taxnumber, $place, $memo);

		$_SESSION['vid']=$id;
		
	}
	
	//供应商上传
	public function VendorIcon()
	{
		$id=$_SESSION['vid'];
		$upfile = $_FILES ['upfile'];
		$name = $_FILES ['upfile'] ['name'];
		$filesize = $_FILES ['upfile'] ['size'];

		if (empty ( $upfile )) Json::ReturnError ( '请选择文件' );
	
		$ext = IO::GetFileExtension ( $name );
		if (! in_array ( $ext, self::$LIMIT_FORMAT_IMAGE )) Json::ReturnError ( '文件格式错误' );
		if ($filesize > self::$MAX_SIZE) Json::ReturnError ( '超过最大上传限制' );
	
		$filename = IO::GetFileBaseName ( $name );
		$furl = BASE_UPLOAD_VENDOR . $filename;
		$savename = UUID::Make() . '.' .$ext;
		$downloads = 0;
	
		UploadCls::Add($id,$furl,$filename,$savename,$filesize,$downloads);
	
		try
		{
			IO::MakeDir ( BASE_UPLOAD_VENDOR, 0755, false );
				
			if (! $this->UploadFile ( $upfile, BASE_UPLOAD_VENDOR . $filename )) Json::ReturnError ( '文件上传失败' );
		}
		catch ( Exception $e )
		{
			Json::ReturnError ( ALERT_ERROR );
		}
	
		Json::ReturnSuccess ( BASE_UPLOAD_VENDOR . $filename );
	}
	
	//供应商上传文件
	private function UploadFile($upfile, $filname)
	{
		if (empty ( $upfile )) return false;
		if ($upfile ['error'] != 0)
		{
			@unlink ( $upfile ['tmp_name'] );
			return false;
		}
	
		if (@move_uploaded_file ( $upfile ['tmp_name'], $filname )) return true;
	
		return false;
	}
	
	//供应商下载文件
	public function VendorDoloads()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		$rs = UploadCls::ItemsDoloadTo($id);

		$filename = $rs[0]["filename"];	//文件名
		$filesize = $rs[0]["filesize"];	//文件大小
		$furl = BASE_UPLOAD_VENDOR . $filename; //下载文件存放目录
		
		$file_name=iconv("utf-8","gb2312",$filename); //下载文件名 
		
		//检查文件是否存在
		if (! file_exists ( $furl )) {
			echo "文件找不到";
			exit ();
		} else {
			$file_size=filesize($furl);	//获取文件大小
			
			Header ( "Content-Type: application/force-download" );	//返回的文件
			Header("Content-Transfer-Encoding: binary");	//转换二进制编码
			Header ( "Accept-Ranges: bytes" );	//按照字节格式返回
			Header ( "Content-Disposition: attachment; filename=".$file_name);	//弹出客户端对话框，对应的文件名
			Header ( "Content-Length:".$file_size);	//返回文件大小 
			
			readfile($furl);	//读取文件路径
		}
	}
	
	public function VendorContactList()
	{
		$this->Header ();
	
		if (! P::Instance ( $this->Uid () )->isCanView ( R::VENDOR_CONTACTLIST ))
		{
			UtilMod::MessageBoxUnableList ();
			return;
		}
		
		$vendorid = $this->Req( 'vendorid', '', 'str');
		$name = $this->Req( 'name', '', 'str');
		$where = 'WHERE del=false';
		$order ='ORDER BY id ASC';
		if (! empty ( $vendorid )) $where .= ' AND vendorid = '.$vendorid;
		if (! empty ( $name )) $where .= ' AND (LOWER(name) like \'%' . strtolower ( $name ) . '%\') ';
	
		$v = View::Factory ( 'VendorContactList' );
		
		$url = Url::QUERY_STRING_DELETE ( array ( 'pn' ) );
		$pagesize = PAGE_SIZE_DEFAULT;
		$count = VendorBiz::CountVCon ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
		$order = 'ORDER BY id ASC';
	
		$rs = VendorBiz::ItemsVCon ( $where, $order, $pos, $pagesize );
		if ($rs && count ( $rs ) > 0)
		{
			for($i = 0; $i < count ( $rs ); $i ++)
			{
				$rs [$i] ['vendorid'] = VendorBiz::GetNameByID($rs [$i] ['vendorid']);
			}
		}
			
		$v->rs = $rs;
		$v->orders = HTML::Orders ( $url, array ( 'username' , 'name' , 'serial' , 'act' , 'time' ) );
		$v->paged = HTML::PageGroup ( $count, $this->Page (), $url, $pagesize );
		
		echo $v->Render ();
		$this->Footer ();
	}
	
	public function VendorContactAdd()
	{
		$this->Header ();
		
		if (! P::Instance ( $this->Uid () )->isCanView ( R::VENDOR_CONTACTADD ))
		{
			UtilMod::MessageBoxUnableList ();
			return;
		}
	
		$v = View::factory ( 'VendorContactAdd' );
	
		echo $v->Render ();
	
		$this->Footer ();
	}
	
	public function VendorContactEdit()
	{
		$this->Header ();
		
		$id = $this->Req( 'id', 0, 'int');
		
		$v = View::Factory ( 'VendorContactEdit' );
	
		$rs = VendorBiz::ItemToCon ( $id );
		$rs['vendorname'] = VendorBiz::GetNameByID($rs['vendorid']);
		$v->rs = $rs;
	
		echo $v->Render ();
		
		$this->Footer ();
	}
	
	public function OnVendorContactAdd()
	{
		$vendorid = $this->Req ( 'vendorid', 0, 'int' );
		$name = $this->Req ( 'name', '', 'str' );
		$isgender = $this->Req ( 'isgender', 0, 'int' );
		$birthday = $this->Req ( 'birthday', '', 'str' );
		$position = $this->Req ( 'position', '', 'str' );
		$phone = $this->Req ( 'phone', '', 'str' );
		$mobilephone = $this->Req ( 'mobilephone', '', 'str' );
		$address = $this->Req ( 'address', '', 'str' );
		$fax = $this->Req ( 'fax', '', 'str' );
		$code = $this->Req ( 'code', '', 'str' );
		$email = $this->Req ( 'email', '', 'str' );
		$memo = $this->Req ( 'memo', '', 'str' );
	
		$isgender = Util::Bool2Int($isgender);
	
		VendorBiz::AddVendorContact($vendorid, $name, $isgender, $birthday, $position, $phone, $mobilephone, $address, $fax, $code, $email, $memo);
		echo 1;
	}
	
	public function OnVendorContactEdit()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		$vendorid = $this->Req ( 'vendorid', 0, 'int' );
		$name = $this->Req ( 'name', '', 'str' );
		$isgender = $this->Req ( 'isgender', 0, 'int' );
		$birthday = $this->Req ( 'birthday', '', 'str' );
		$position = $this->Req ( 'position', '', 'str' );
		$phone = $this->Req ( 'phone', '', 'str' );
		$mobilephone = $this->Req ( 'mobilephone', '', 'str' );
		$address = $this->Req ( 'address', '', 'str' );
		$fax = $this->Req ( 'fax', '', 'str' );
		$code = $this->Req ( 'code', '', 'str' );
		$email = $this->Req ( 'email', '', 'str' );
		$memo = $this->Req ( 'memo', '', 'str' );
	
		$isgender = Util::Bool2Int($isgender);
	
		VendorBiz::EditVendorContact ( $id, $vendorid, $name, $isgender, $birthday, $position, $phone, $mobilephone, $address, $fax, $code, $email, $memo );
		
		echo 1;
	}
	
	//添加供应商地址
	public function OnVendorAddressAdd()
	{
		$id=$_SESSION['vid'];
		$nation = $this->Req( 'nation', '', 'str');
		$contact = $this->Req( 'contact', '', 'str');
		$contactphone = $this->Req( 'contactphone', '', 'str');
		$act = $this->Req ( 'act', 1, 'int' );
		$province = $this->Req( 'province', '', 'str');
		$city = $this->Req( 'city', '', 'str');
		$area = $this->Req( 'area', '', 'str');
		$place = $this->Req( 'place', '', 'str');
		$procityarea = $this->Req( 'procityarea', '', 'str');
	
		$act = Util::Bool2Int ( $act );
		
		VendorBiz::AddAddress($id, $nation, $contact, $contactphone, $act, $province, $city, $area, $place, $procityarea);
		echo 1;
		unset ($_SESSION['vid']);
	}
	
	//添加多个供应商地址
	public function OnVendorAddressAddOr()
	{
		$id = $this->Req ( 'id', '', 'int' );
		$nation = $this->Req( 'nation', '', 'str');
		$contact = $this->Req( 'contact', '', 'str');
		$contactphone = $this->Req( 'contactphone', '', 'str');
		$act = $this->Req ( 'act', 1, 'int' );
		$province = $this->Req( 'province', '', 'str');
		$city = $this->Req( 'city', '', 'str');
		$area = $this->Req( 'area', '', 'str');
		$place = $this->Req( 'place', '', 'str');
		$procityarea = $this->Req( 'procityarea', '', 'str');
		
		$act = Util::Bool2Int ( $act );
	
		VendorBiz::AddAddress($id, $nation, $contact, $contactphone, $act, $province, $city, $area, $place, $procityarea);
		echo 1;
	}
	
	//编辑供应商
	public function OnVendorEdit()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		$code = $this->Req ( 'code', '', 'str' );
		$name = $this->Req ( 'name', '', 'str' );
		$taxnumber = $this->Req ( 'taxnumber', '', 'str' );
		$place = $this->Req ( 'place', '', 'str' );
		//$iscustomer = $this->Req ( 'iscustomer', 0, 'int' );
		//$isvendor = $this->Req ( 'isvendor', 0, 'int' );
		$memo = $this->Req ( 'memo', '', 'str' );
		
		//$iscustomer = Util::Bool2Int($iscustomer);
		//$isvendor = Util::Bool2Int($isvendor);
		
		VendorBiz::Edit ( $id, $code, $name, $taxnumber, $place, $memo );
		
		echo 1;
	}
	
	//编辑供应商地址
	public function OnVendorAddressEdit()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		$address = $this->Req ( 'address', '', 'str' );
		$nation = $this->Req ( 'nation', '', 'str' );
		$contact = $this->Req ( 'contact', '', 'str' );
		$contactphone = $this->Req ( 'contactphone', '', 'str' );
		$act = $this->Req ( 'act', 1, 'int' );

		$act = Util::Bool2Int ( $act );
		
		VendorBiz::AddressEdit ( $id, $address, $nation, $contact, $contactphone, $act );
		echo 1;
	}
	
	//供应商审核	通过
	public function OnVendorProductCheckOK()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		VendorBiz::OnVendorProductCheckOK ( $id );
	}
	
	//供应商审核	不通过
	public function OnVendorProductCheckNOK()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		VendorBiz::OnVendorProductCheckNOK ( $id );
	}

	public function OnVendorDelete()
	{
		$id = $this->Req ( 'id', 0, 'int' );
		
		echo VendorBiz::Delete ( $id );
	}
	
	//查询客户
	public function PopVendorSelect()
	{
		$this->Head ();
		if (! P::Instance ( $this->Uid () )->isCanView ( R::VENDOR_VENDOR ))
		{
			UtilMod::MessageBoxUnableList ();
			return;
		}
		
		$id = $this->Req ( 'id', 0, 'int' );
		$code = $this->Req ( 'code', '', 'str' );
		$name = $this->Req ( 'name', '', 'str' );
		$taxnumber = $this->Req ( 'taxnumber', '', 'str' );
		
		$gid = UserCls::Instance()->GetGid($this->Uid());

		$GRWhere = GroupCls::GetChildIds($gid);
		
		$GRGID = implode(",",$GRWhere);
		
		$where = 'WHERE del=false AND gid in ('.$GRGID.')';
		
		if (! empty ( $code )) $where .= ' AND (LOWER(code) like \'%' . strtolower ( $code ) . '%\') ';
		if (! empty ( $name )) $where .= ' AND (LOWER(name) like \'%' . strtolower ( $name ) . '%\') ';
		if (! empty ( $taxnumber )) $where .= ' AND (LOWER(taxnumber) like \'%' . strtolower ( $taxnumber ) . '%\') ';
		
		$v = View::Factory ( 'PopVendorSelect' );
		
		$pagesize = PAGE_SIZE_MINI;
		$count = VendorBiz::Count ( $where );
		$pos = HTML::PagePos ( $count, $this->Page (), $pagesize );
		
		$rs = VendorBiz::Items ( $where, $this->Order (), $pos, $pagesize );
		
		$url = Url::QUERY_STRING_DELETE ( array ( 'pn' ) );
		
		$v->rs = $rs;
		
		$v->orders = HTML::Orders ( $url, array ( 'username' , 'name' , 'serial' , 'act' , 'time' ) );
		$v->paged = HTML::PageGroup ( $count, $this->Page (), $url, $pagesize );
				
		echo $v->Render ();
		
		$this->Footer ();
	}
	
}
?>