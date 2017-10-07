<?php
require_once 'PHPExcel.php';
require_once 'PHPExcel/Writer/Excel2007.php';
class ExcelPrinter
{

	public static function PrintOutWarehouse($vendor_order_products)
	{
		if (empty ( $vendor_order_products ))
			return 0;

		function convertUTF8($str)
		{
			if (empty ( $str ))
				return '';
			return iconv ( 'gb2312', 'utf-8', $str );
		}
		// 创建一个处理对象实例
		$objExcel = new PHPExcel ();
		
		// 创建文件格式写入对象实例, uncomment
		$objWriter = new PHPExcel_Writer_Excel2007 ( $objExcel );
		
		// 设置当前的sheet索引，用于后续的内容操作。
		// 一般只有在使用多个sheet的时候才需要显示调用。
		// 缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0
		$objExcel->setActiveSheetIndex ( 0 );
		$objActSheet = $objExcel->getActiveSheet ();
		
		// 设置当前活动sheet的名称
		$vendorname = VendorBiz::GetName ( $vendor_order_products ['product'] [0] ['vid'] );
		$objActSheet->setTitle ( $vendorname . ' ' . $vendor_order_products ['product'] [0] ['ordername'] );
		
		// 设置宽度，这个值和EXCEL里的不同，略小于EXCEL中的宽度
		$objActSheet->getColumnDimension ( 'A' )->setWidth ( 14 );
		$objActSheet->getColumnDimension ( 'B' )->setWidth ( 14 );
		$objActSheet->getColumnDimension ( 'C' )->setWidth ( 34 );
		$objActSheet->getColumnDimension ( 'D' )->setWidth ( 14 );
		$objActSheet->getColumnDimension ( 'E' )->setWidth ( 14 );
		$objActSheet->getColumnDimension ( 'F' )->setWidth ( 14 );
		// $objActSheet->getColumnDimension ( 'G' )->setWidth ( 14 );
		// $objActSheet->getColumnDimension ( 'H' )->setWidth ( 14 );
		// $objActSheet->getColumnDimension ( 'I' )->setWidth ( 18 );
		// $objActSheet->getColumnDimension ( 'J' )->setWidth ( 14 );
		
		// 数据开始的行数
		$rownum = 3;
		
		// 设置居中对齐
		$objActSheet->getStyle ( 'A1' )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objActSheet->getStyle ( 'A' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objActSheet->getStyle ( 'B' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objActSheet->getStyle ( 'C' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objActSheet->getStyle ( 'D' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objActSheet->getStyle ( 'E' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objActSheet->getStyle ( 'F' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		// $objActSheet->getStyle ( 'G' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		// $objActSheet->getStyle ( 'H' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		// $objActSheet->getStyle ( 'I' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		// $objActSheet->getStyle ( 'J' . $rownum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		
		// $objActSheet->getRowDimension ( 1 )->setRowHeight ( 90 ); // 设置行高
		// $objActSheet->getRowDimension ( 2 )->setRowHeight ( 50 );
		
		// 设置表头*********************************************
		// 合并单元格 10列
		$objActSheet->mergeCells ( 'A1:F1' );
		
		// 设置样式
		$objStyleA1 = $objActSheet->getStyle ( 'A1' );
		$objStyleA1->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objFontA1 = $objStyleA1->getFont ();
		$objFontA1->setName ( '微软雅黑' );
		$objFontA1->setSize ( 16 );
		$objFontA1->setBold ( true );
		
		// 设置单元格的值
		$objActSheet->setCellValue ( 'A1', $vendorname . '　' . $vendor_order_products ['product'] [0] ['ordername'] );
		
		// 发货方************************************************
		$objActSheet->mergeCells ( 'A2:C2' );
		$objActSheet->setCellValue ( 'A2', "发货方：" . $vendorname );
		$objActSheet->mergeCells ( 'D2:F2' );
		$objActSheet->setCellValue ( 'D2', "订单编号：" . $vendor_order_products ['vendor'] ['id'] );
		// 设置样式
		$objStyleA1 = $objActSheet->getStyle ( 'A2' );
		$objStyleA1->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_LEFT );
		$objFontA1 = $objStyleA1->getFont ();
		$objFontA1->setName ( '微软雅黑' );
		$objFontA1->setSize ( 12 );
		$objFontA1->setBold ( true );
		$objStyleA2 = $objActSheet->getStyle ( 'D2' );
		$objStyleA2->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_LEFT );
		$objFontA2 = $objStyleA2->getFont ();
		$objFontA2->setName ( '微软雅黑' );
		$objFontA2->setSize ( 12 );
		$objFontA2->setBold ( true );
		
		// 设置标题************************************************
		$objActSheet->setCellValue ( 'A' . $rownum, '商品编号' );
		$objActSheet->setCellValue ( 'B' . $rownum, '规格型号' );
		$objActSheet->setCellValue ( 'C' . $rownum, '商品名称' );
		$objActSheet->setCellValue ( 'D' . $rownum, 'SKU' );
		$objActSheet->setCellValue ( 'E' . $rownum, '销售单位' );
		$objActSheet->setCellValue ( 'F' . $rownum, '数量' );
		// $objActSheet->setCellValue ( 'H' . $rownum, '单价（元）' );
		// $objActSheet->setCellValue ( 'I' . $rownum, '修改后单价（元）' );
		// $objActSheet->setCellValue ( 'J' . $rownum, '小计（元）' );
		
		// 内容************************************************
		$amount = 0;
		$m = 0;
		for($n = 0; $n < count ( $vendor_order_products ['product'] ); $n ++)
		{
			$m = $n + ($rownum + 1);
			// 居中
			$objActSheet->getStyle ( 'A' . $m )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
			$objActSheet->getStyle ( 'B' . $m )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
			// $objActSheet->getStyle ( 'C' . $m )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
			$objActSheet->getStyle ( 'D' . $m )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
			$objActSheet->getStyle ( 'E' . $m )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
			$objActSheet->getStyle ( 'F' . $m )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
			
			$objActSheet->setCellValue ( 'A' . $m, $vendor_order_products ['product'] [$n] ['vpid'] );
			$objActSheet->setCellValue ( 'B' . $m, $vendor_order_products ['product'] [$n] ['vamodel'] );
			$objActSheet->setCellValue ( 'C' . $m, $vendor_order_products ['product'] [$n] ['name'] );
			$objActSheet->setCellValue ( 'D' . $m, $vendor_order_products ['product'] [$n] ['serial'] );
			$objActSheet->setCellValue ( 'E' . $m, $vendor_order_products ['product'] [$n] ['vaname'] );
			$objActSheet->setCellValue ( 'F' . $m, $vendor_order_products ['product'] [$n] ['number'] );
			// $objActSheet->setCellValue ( 'H' . $m, $vendor_order_products['product'] [$n] ['price_original'] );
			// $objActSheet->setCellValue ( 'I' . $m, $vendor_order_products['product'] [$n] ['price'] );
			// $objActSheet->setCellValue ( 'J' . $m, $vendor_order_products['product'] [$n] ['xj'] );
			$amount += $vendor_order_products ['product'] [$n] ['number'];
		}
		
		// 订单总计**************************************
		$objStyleA1 = $objActSheet->getStyle ( 'A' . ++ $m );
		$objStyleA1->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );
		$objFontA1 = $objStyleA1->getFont ();
		$objFontA1->setName ( '微软雅黑' );
		$objFontA1->setSize ( 12 );
		$objFontA1->setBold ( true );
		
		$objActSheet->mergeCells ( 'A' . $m . ':F' . $m );
		$objActSheet->setCellValue ( 'A' . $m, '总数：' . $amount );
		
		// 收货方***********************************
		$m ++;
		$objActSheet->mergeCells ( 'A' . $m . ':B' . $m );
		$objActSheet->setCellValue ( 'A' . $m, "收货方：" . $vendor_order_products ['vendor'] ['receiver'] );
		$objActSheet->mergeCells ( 'C' . $m . ':D' . $m );
		$objActSheet->setCellValue ( 'C' . $m, "地址：" . $vendor_order_products ['vendor'] ['detailedaddress'] . ' ' . $vendor_order_products ['vendor'] ['receiveaddress'] );
		$objActSheet->mergeCells ( 'E' . $m . ':F' . $m );
		$objActSheet->setCellValue ( 'E' . $m, "电话：" . $vendor_order_products ['vendor'] ['receivemobile'] );
		
		// 设置边框
		// $objActSheet->getStyle ( 'A1' )->getBorders ()->getTop ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_MEDIUM );
		// $objActSheet->getStyle ( 'A1' )->getBorders ()->getLeft ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		// $objActSheet->getStyle ( 'A1' )->getBorders ()->getRight ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		// $objActSheet->getStyle ( 'A1' )->getBorders ()->getBottom ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		// $objExcel->getActiveSheet ()->getStyle ( 'A2:J2' )->applyFromArray ( array ( 'borders' => array ( 'top' => array ( 'style' => PHPExcel_Style_Border::BORDER_MEDIUM ) ) ) );
		// 设置总表边框
		// $objExcel->getActiveSheet ()->getStyle ( 'A' . $rownum . ':G' . $m )->applyFromArray ( array ( 'borders' => array ( 'outline' => array ( 'style' => PHPExcel_Style_Border::BORDER_MEDIUM ) ) ) );
		// 设置单元格边框
		$objExcel->getActiveSheet ()->getStyle ( 'A' . $rownum . ':F' . $m )->getBorders ()->getAllBorders ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		
		// *************************************
		// // 保存文件到固定位置
		// $outputFileName = "C:\\Users\\Administrator\\Desktop\\aaaaa.xls";
		// $objWriter->save ( $outputFileName );
		
		// 客户端web浏览器(Excel2007)输出
		$fileName = iconv ( "utf-8", "gb2312", $vendorname . $vendor_order_products ['product'] [0] ['ordername'] . $vendor_order_products ['vendor'] ['id'].".xls" );
		header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header ( "Content-Disposition: attachment; filename=\"$fileName\"" );
		header ( 'Cache-Control: max-age=0' );
		$objWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'Excel2007' );
		$objWriter->save ( 'php://output' );
		
		// 导出成PDF
		// header ( 'Content-Type: application/pdf' );
		// header ( 'Content-Disposition: attachment;filename="' . $fileName . '.pdf"' );
		// header ( 'Cache-Control: max-age=0' );
		// $pdfWriter = PHPExcel_IOFactory::createWriter ( $objExcel, 'PDF' );
		// $pdfWriter->save ( 'php://output' );
		
		exit ();
	}
}

?>