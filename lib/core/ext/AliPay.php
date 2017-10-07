<?php
require_once ("../../lib/third/Alipay/alipay_core.function.php");
require_once ("../../lib/third/Alipay/alipay_md5.function.php");
// 淘宝
class AliPay
{
	// 支付类型
	const TYPE = '1';
	// 服务器异步通知页面路径
	const NOTIFYURL = '';
	// 页面跳转同步通知页面路径
	const RETURNURL = 'http://www.gomro.cn/alipayresult.html';
	// 卖家支付宝帐户 ,必填
	const SELLEREMAIL = 'payment6@gomro.cn';
	// 超时时间
	const EXPIREDATETIME = '24h';
	
	//HTTPS形式消息验证地址
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';	
	//HTTP形式消息验证地址
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	
	/**
	 * ************************基本信息*************************
	 */
	private $alipay_config = array ( "partner" => "2088511051634222" , 	// 合作身份者id,以2088开头的16位纯数字
	"key" => "q9cs6bb6arr0dl5g6228ichsu2h2gj0b" , 	// 安全检验码，以数字和字母组成的32位字符
	"sign_type" => "MD5" , 	// 签名方式
	"input_charset" => "utf-8" , 	// 字符编码格式 目前支持 gbk 或 utf-8
	"cacert" => "\\cacert.pem" , 	// ca证书路径地址，用于curl中ssl校验。请保证cacert.pem文件在当前文件夹目录中
	"transport" => "http" ); // 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	public function Pay($no, $pname, $amount, $id)
	{
		/**
		 * ***********************请求参数**************************
		 */
		// 商户订单号，必填
		$out_trade_no = $no;
		
		// 订单名称，必填
		$subject = $pname;
		
		// 付款金额，必填
		$total_fee = $amount;
		
		// 订单描述，存放用户id
		$body = $id;
		
		// 商品展示地址，收银台页面上，商品展示的超链接
		$show_url = '';
		
		// 防钓鱼时间戳，若要使用，调用类文件alipay_submit中的query_timestamp函数
		$anti_phishing_key = '';
		// 客户端的IP地址,如果商户申请后台开通防钓鱼IP地址检查选项，此字段必填，校验用
		$exter_invoke_ip = '';
		
		// 构造要请求的参数数组，无需改动
		$parameter = array ( "service" => "create_direct_pay_by_user" , 		// 接口名称
		"partner" => $this->alipay_config ['partner'] , "payment_type" => self::TYPE , "notify_url" => self::NOTIFYURL , "return_url" => self::RETURNURL , "seller_email" => self::SELLEREMAIL , "out_trade_no" => $out_trade_no , "subject" => $subject , "total_fee" => $total_fee , "body" => $body , "show_url" => $show_url , "anti_phishing_key" => $anti_phishing_key , "exter_invoke_ip" => $exter_invoke_ip , "_input_charset" => trim ( strtolower ( $this->alipay_config ['input_charset'] ) ) );
		
		// 建立请求
		$alipaySubmit = new AlipaySubmit ( $this->alipay_config );
		
		$html_text = $alipaySubmit->buildRequestForm ( $parameter, "post", "" );
		echo $html_text;
	}
	

	/**
	 * 针对notify_url验证消息是否是支付宝发出的合法消息
	 *
	 * @return 验证结果
	 */
	private function verifyNotify()
	{
		if (empty ( $_POST ))
		{
			return false;
		}
		else
		{
			// 生成签名结果
			$isSign = $this->getSignVeryfy ( $_POST, $_POST ["sign"] );
			// 获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'true';
			if (! empty ( $_POST ["notify_id"] ))
			{
				$responseTxt = $this->getResponse ( $_POST ["notify_id"] );
			}
			
			// 验证
			// $responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			// isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if (preg_match ( "/true$/i", $responseTxt ) && $isSign)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * 针对return_url验证消息是否是支付宝发出的合法消息
	 *
	 * @return 验证结果
	 */
	public function verifyReturn()
	{
		if (empty ( $_GET ))
			return false;
		else
		{
			// 生成签名结果
			$isSign = $this->getSignVeryfy ( $_GET, $_GET ["sign"] );
			
			// 获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'true';
			if (! empty ( $_GET ["notify_id"] ))
				$responseTxt = $this->getResponse ( $_GET ["notify_id"] );
			
			// 验证
			// $responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			// isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关

			if (preg_match ( "/true$/i", $responseTxt ) && $isSign)
				return true;
			else
				return false;
		}		
	}

	/**
	 * 获取返回时的签名验证结果
	 *
	 * @param $para_temp 通知返回来的参数数组        	
	 * @param $sign 返回的签名结果        	
	 * @return 签名验证结果
	 */
	private function getSignVeryfy($para_temp, $sign)
	{
		// 除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter ( $para_temp );
		
		// 对待签名参数数组排序
		$para_sort = argSort ( $para_filter );
		
		// 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring ( $para_sort );
		
		//由于返回时参数带有a=alipayresult&，要去掉
		$prestr=str_replace('a=alipayresult&','',$prestr);
		
		$isSgin = false;
		switch (strtoupper ( trim ( $this->alipay_config ['sign_type'] ) ))
		{
			case "MD5" :
				$isSgin = md5Verify ( $prestr, $sign, $this->alipay_config ['key'] );
				break;
			default :
				$isSgin = false;
		}
		return $isSgin;
	}

	/**
	 * 获取远程服务器ATN结果,验证返回URL
	 *
	 * @param $notify_id 通知校验ID        	
	 * @return 服务器ATN结果 验证结果集：
	 *         invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
	 *         true 返回正确信息
	 *         false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
	 */
	private function getResponse($notify_id)
	{
		$transport = strtolower ( trim ( $this->alipay_config ['transport'] ) );
		$partner = trim ( $this->alipay_config ['partner'] );
		$veryfy_url = '';
		if ($transport == 'https')
		{
			$veryfy_url = $this->https_verify_url;
		}
		else
		{
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url . "partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = getHttpResponseGET ( $veryfy_url, $this->alipay_config ['cacert'] );
		
		return $responseTxt;
	}
}
?>
	