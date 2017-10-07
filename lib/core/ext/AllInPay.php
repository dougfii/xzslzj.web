<?php
class AllInPay
{
	const URL = 'https://service.allinpay.com/gateway/index.do';
	const KEY = '1234567890';
	const MERCHANT_ID = '109062111408005'; // 商户号
	const PICKUP_URL = 'http://www.gomro.cn/?a=allinpayresult&id=';
	const RECEIVE_URL = '';
	const INPUT_CHARSET = '1'; // 字符集
	const VERSION = 'v1.0'; // 支付请求接口版本
	const LANGUAGE = '';
	const SIGN_TYPE = '0';
	const PAYER_IDCARD = '';
	const PID = '';
	const ORDER_CURRENCY = '0'; // 金额币种类型
	const ORDER_EXPIRE_DATETIME = '1440'; // 订单过期时间, 整形数字，单位为分,不填默认60分钟后过期,最大值不超过24小时
	const PRODUCT_NAME = '订单支付';
	const PRODUCT_PRICE = '';
	const PRODUCT_NUM = '';
	const PRODUCT_ID = '';
	const PRODUCT_DESC = '';
	const EXT1 = '';
	const EXT2 = '';
	const EXT_TL = '';
	const PAN = '';
	const TRADE_NATURE = '';
	// const PAYER_NAME = '';
	// const PAYER_EMAIL = '';
	// const PAYER_TELEPHONE = '';
	// const ORDER_NO = ''; // 订单号
	// const ORDER_AMOUNT = ''; // 商户订单金额
	// const ORDER_DATETIME = ''; //商户订单提交时间
	public static function Pay($id, $code, $amount, $datetime, $member_name, $member_email, $member_mobile, $paytype, $issureid)
	{
		$pickupUrl = self::PICKUP_URL . UUID::ToString ( $id );
		$payerName = $member_name;
		$payerEmail = $member_email;
		$payerTelephone = $member_mobile;
		$orderNo = $code;
		$orderAmount = $amount;
		$orderDatetime = $datetime;
		$productName = APP_COMPANY_TITLE . $code . self::PRODUCT_NAME;
		
		$signMsg = self::GetSign ( self::INPUT_CHARSET, $pickupUrl, self::RECEIVE_URL, self::VERSION, self::LANGUAGE, self::SIGN_TYPE, self::MERCHANT_ID, $payerName, $payerEmail, $payerTelephone, self::PAYER_IDCARD, self::PID, $orderNo, $orderAmount, self::ORDER_CURRENCY, $orderDatetime, self::ORDER_EXPIRE_DATETIME, $productName, self::PRODUCT_PRICE, self::PRODUCT_NUM, self::PRODUCT_ID, self::PRODUCT_DESC, self::EXT1, self::EXT2, self::EXT_TL, $paytype, $issureid, self::PAN, self::TRADE_NATURE, self::KEY );
		
		$post_data = array ( "inputCharset" => self::INPUT_CHARSET , "pickupUrl" => $pickupUrl , "receiveUrl" => self::RECEIVE_URL , "version" => self::VERSION , "language" => self::LANGUAGE , "signType" => self::SIGN_TYPE , "merchantId" => self::MERCHANT_ID , "payerName" => $payerName , "payerEmail" => $payerEmail , "payerTelephone" => $payerTelephone , "payerIDCard" => self::PAYER_IDCARD , "pid" => self::PID , "orderNo" => $code , "orderAmount" => $amount , "orderCurrency" => self::ORDER_CURRENCY , "orderDatetime" => $datetime , "orderExpireDatetime" => self::ORDER_EXPIRE_DATETIME , "productName" => $productName , "productPrice" => self::PRODUCT_PRICE , "productNum" => self::PRODUCT_NUM , "productId" => self::PRODUCT_ID , "productDesc" => self::PRODUCT_DESC , "ext1" => self::EXT1 , "ext2" => self::EXT2 , "extTL" => self::EXT_TL , "payType" => $paytype , "issuerId" => $issureid , "pan" => self::PAN , "tradeNature" => self::TRADE_NATURE , "signMsg" => $signMsg );
		
		return self::URL . '?' . http_build_query ( $post_data );
	}

	public static function GetDecSign($merchantId, $paymentOrderId, $code, $orderDatetime, $orderAmount, $payDatetime, $payAmount, $ext1, $ext2, $payResult, $errorCode, $returnDatetime, $version, $language, $signType, $payType, $issuerId)
	{
		return self::DecSign ( $merchantId, $version, $language, $signType, $payType, $issuerId, $paymentOrderId, $code, $orderDatetime, $orderAmount, $payDatetime, $payAmount, $ext1, $ext2, $payResult, $errorCode, $returnDatetime );
	}

	Private static function GetSign($inputCharset, $pickupUrl, $receiveUrl, $version, $language, $signType, $merchantId, $payerName, $payerEmail, $payerTelephone, $payerIDCard, $pid, $orderNo, $orderAmount, $orderCurrency, $orderDatetime, $orderExpireDatetime, $productName, $productPrice, $productNum, $productId, $productDesc, $ext1, $ext2, $extTL, $payType, $issuerId, $pan, $tradeNature, $key)
	{
		// 生成签名字符串
		$s = "";
		if ($inputCharset != "") $s = $s . "inputCharset=" . $inputCharset . "&";
		if ($pickupUrl != "") $s = $s . "pickupUrl=" . $pickupUrl . "&";
		if ($receiveUrl != "") $s = $s . "receiveUrl=" . $receiveUrl . "&";
		if ($version != "") $s = $s . "version=" . $version . "&";
		if ($language != "") $s = $s . "language=" . $language . "&";
		if ($signType != "") $s = $s . "signType=" . $signType . "&";
		if ($merchantId != "") $s = $s . "merchantId=" . $merchantId . "&";
		if ($payerName != "") $s = $s . "payerName=" . $payerName . "&";
		if ($payerEmail != "") $s = $s . "payerEmail=" . $payerEmail . "&";
		if ($payerTelephone != "") $s = $s . "payerTelephone=" . $payerTelephone . "&";
		if ($payerIDCard != "") $s = $s . "payerIDCard=" . $payerIDCard . "&";
		if ($pid != "") $s = $s . "pid=" . $pid . "&";
		if ($orderNo != "") $s = $s . "orderNo=" . $orderNo . "&";
		if ($orderAmount != "") $s = $s . "orderAmount=" . $orderAmount . "&";
		if ($orderCurrency != "") $s = $s . "orderCurrency=" . $orderCurrency . "&";
		if ($orderDatetime != "") $s = $s . "orderDatetime=" . $orderDatetime . "&";
		if ($orderExpireDatetime != "") $s = $s . "orderExpireDatetime=" . $orderExpireDatetime . "&";
		if ($productName != "") $s = $s . "productName=" . $productName . "&";
		if ($productPrice != "") $s = $s . "productPrice=" . $productPrice . "&";
		if ($productNum != "") $s = $s . "productNum=" . $productNum . "&";
		if ($productId != "") $s = $s . "productId=" . $productId . "&";
		if ($productDesc != "") $s = $s . "productDesc=" . $productDesc . "&";
		if ($ext1 != "") $s = $s . "ext1=" . $ext1 . "&";
		if ($ext2 != "") $s = $s . "ext2=" . $ext2 . "&";
		if ($extTL != "") $s = $s . "extTL=" . $extTL . "&";
		if ($payType != "") $s = $s . "payType=" . $payType . "&";
		if ($issuerId != "") $s = $s . "issuerId=" . $issuerId . "&";
		if ($pan != "") $s = $s . "pan=" . $pan . "&";
		if ($tradeNature != "") $s = $s . "tradeNature=" . $tradeNature . "&";
		
		$s = $s . "key=" . $key;
		
		return strtoupper ( md5 ( $s ) );
	}

	Private static function DecSign($merchantId, $version, $language, $signType, $payType, $issuerId, $paymentOrderId, $orderNo, $orderDatetime, $orderAmount, $payDatetime, $payAmount, $ext1, $ext2, $payResult, $errorCode, $returnDatetime)
	{
		$s = "";
		if ($merchantId != "") $s = $s . "merchantId=" . $merchantId . "&";
		if ($version != "") $s = $s . "version=" . $version . "&";
		if ($language != "") $s = $s . "language=" . $language . "&";
		if ($signType != "") $s = $s . "signType=" . $signType . "&";
		if ($payType != "") $s = $s . "payType=" . $payType . "&";
		if ($issuerId != "") $s = $s . "issuerId=" . $issuerId . "&";
		if ($paymentOrderId != "") $s = $s . "paymentOrderId=" . $paymentOrderId . "&";
		if ($orderNo != "") $s = $s . "orderNo=" . $orderNo . "&";
		if ($orderDatetime != "") $s = $s . "orderDatetime=" . $orderDatetime . "&";
		if ($orderAmount != "") $s = $s . "orderAmount=" . $orderAmount . "&";
		if ($payDatetime != "") $s = $s . "payDatetime=" . $payDatetime . "&";
		if ($payAmount != "") $s = $s . "payAmount=" . $payAmount . "&";
		if ($ext1 != "") $s = $s . "ext1=" . $ext1 . "&";
		if ($ext2 != "") $s = $s . "ext2=" . $ext2 . "&";
		if ($payResult != "") $s = $s . "payResult=" . $payResult . "&";
		if ($errorCode != "") $s = $s . "errorCode=" . $errorCode . "&";
		if ($returnDatetime != "") $s = $s . "returnDatetime=" . $returnDatetime;
		
		$s = $s . "&key=" . self::KEY;
		
		return strtoupper ( md5 ( $s ) );
	}
}
?>