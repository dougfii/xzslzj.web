<?php

class ExportMod extends BaseMod
{

    public function __construct()
    {
        set_time_limit(3600);
        ini_set('memory_limit', '2048M');

        parent::__construct();
    }

    public function index()
    {
    }

    public function ExcelProjectList()
    {
        $tid = $this->Req('tid', 0, 'int');

        $fname = $this->Req('fname', '', 'str');
        $fcompany = $this->Req('fcompany', '', 'str');
        $fdate0 = $this->Req('fdate0', '', 'str');
        $fdate1 = $this->Req('fdate1', '', 'str');
        $year = $this->Req('year', 0, 'int');

        $where = '';

        if ($year >= YEAR_START && $year <= Date::Year()) {
            $where .= ' AND EXTRACT(YEAR FROM time)=' . $year;
        }

        if (!empty ($fname)) $where .= ' AND LOWER(name) like \'%' . strtolower($fname) . '%\'';
        if (!empty($fcompany)) $where .= ' AND LOWER(company) like \'%' . strtolower($fcompany) . '%\'';
        if (!empty($fdate0) && empty($fdate1)) $where .= " AND DATE_TRUNC('day', time)='{$fdate0}'";
        if (empty($fdate0) && !empty($fdate1)) $where .= " AND DATE_TRUNC('day', time)='{$fdate1}'";
        if (!empty($fdate0) && !empty($fdate1)) $where .= " AND DATE_TRUNC('day', time)>='{$fdate0}' AND DATE_TRUNC('day', time)<='{$fdate1}'";

        list($count, $rs) = ProjectCls::Results($where, '', 1, 0);

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        $this->SetPageStandartA4Vertical($sheet);

        $sheet->setTitle('工程列表');

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(9);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);

        $i = 1;
        $sheet->mergeCells("A{$i}:J{$i}");
        $sheet->setCellValue("A{$i}", "工程列表");
        $sheet->getRowDimension($i)->setRowHeight(40);
        $this->SetPrintCaptionStyle($sheet, "A{$i}");

        $i++;
        $sheet->setCellValue("A{$i}", '序号');
        $sheet->setCellValue("B{$i}", '组织');
        $sheet->setCellValue("C{$i}", '立项');
        $sheet->setCellValue("D{$i}", '工程名称');
        $sheet->setCellValue("E{$i}", '申报单位');
        $sheet->setCellValue("F{$i}", '联系人');
        $sheet->setCellValue("G{$i}", '联系方式');
        $sheet->setCellValue("H{$i}", '邮箱');
        $sheet->setCellValue("I{$i}", '最后登录');
        $sheet->setCellValue("J{$i}", '注册时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:J{$i}");

        foreach ($rs as $v) {
            $i++;

            $sheet->setCellValue("A{$i}", $v ['id']);
            $sheet->setCellValue("B{$i}", $v ['group']);
            $sheet->setCellValue("C{$i}", $v['act'] ? '关闭' : '开放');
            $sheet->setCellValue("D{$i}", $v ['name']);
            $sheet->setCellValue("E{$i}", $v ['company']);
            $sheet->setCellValue("F{$i}", $v ['contacts']);
            $sheet->setCellValue("G{$i}", $v ['mobile']);
            $sheet->setCellValue("H{$i}", $v ['email']);
            $sheet->setCellValue("I{$i}", $v ['last']);
            $sheet->setCellValue("J{$i}", $v ['time']);
        }

        $sheet->getStyle("A1:J{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "ProjectList.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelPrintContractView()
    {
        $id = UUID::Check(Url::Req('id', '', 'str'));

        $rs = ContractCls::Instance()->Item($id);
        $rp = ContractProductCls::getProductList($id);
        if (empty ($rs) && !empty ($rp)) {
            echo 'export no data!';
            return;
        }

        $customer_address = CustomerCls::Instance()->GetAddress($rs ['contactid']);
        $customer_phone = CustomerCls::Instance()->GetPhone($rs ['customerid']);
        $customer_fax = CustomerCls::Instance()->GetFax($rs ['contactid']);

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        $this->SetPageStandartA4Vertical($sheet);

        $sheet->setTitle('广通购销合同' . $rs ['code']);

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(9);
        $sheet->getColumnDimension('D')->setWidth(9);
        $sheet->getColumnDimension('E')->setWidth(9);
        $sheet->getColumnDimension('F')->setWidth(9);

        $i = 1;

        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", CompanyCls::FULL_NAME . "\n购 销 合 同");
        $sheet->getRowDimension($i)->setRowHeight(60);
        $this->SetPrintCaptionStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "签订地点：  " . CompanyCls::SINGING_PLACE);
        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "合同编号：" . $rs ['code']);
        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("供方：", CompanyCls::FULL_NAME);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("需方：", $rs ['customer']);
        $sheet->getCell("C{$i}")->setValue($rich);
        $sheet->getRowDimension($i)->setRowHeight(20);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("地址：", CompanyCls::ADDRESS);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        if (!empty ($customer_address)) {
            $rich = $this->CreateUnderlineText("地址：", $customer_address);
            $sheet->getCell("C{$i}")->setValue($rich);
        } else {
            $sheet->setCellValue("C{$i}", "地址：____________________");
        }
        $sheet->getRowDimension($i)->setRowHeight(20);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("电话：", CompanyCls::TELEPHONE);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        if (!empty ($customer_phone)) {
            $rich = $this->CreateUnderlineText("电话：", $customer_phone);
            $sheet->getCell("C{$i}")->setValue($rich);
        } else {
            $sheet->setCellValue("C{$i}", "电话：____________________");
        }
        $sheet->getRowDimension($i)->setRowHeight(20);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("传真：", CompanyCls::FAX);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        if (!empty ($customer_fax)) {
            $rich = $this->CreateUnderlineText("传真：", $customer_fax);
            $sheet->getCell("C{$i}")->setValue($rich);
        } else {
            $sheet->setCellValue("C{$i}", "传真：____________________");
        }
        $sheet->getRowDimension($i)->setRowHeight(20);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "          为保护供需双方的合法权益，根据《中华人民共和国合同法》及有关法律规定，供需双方经友好协商，一致同意按下列条款签定本合同：");
        $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($i)->setRowHeight(40);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "一、产品明细");

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品名称');
        $sheet->setCellValue("B{$i}", '产品规格');
        $sheet->setCellValue("C{$i}", '单位');
        $sheet->setCellValue("D{$i}", '数量');
        $sheet->setCellValue("E{$i}", '单价');
        $sheet->setCellValue("F{$i}", '总价');

        $this->SetPrintTableTitleStyle($sheet, "A{$i}:F{$i}");

        $alltotalprice = 0;
        foreach ($rp as $v) {
            $i++;
            $totalprice = $v ['number'] * $v ['price'] * $v ['currencyrate'];
            $this->SetPrintCellTextValue($sheet, "A{$i}", $v ['product']);
            $this->SetPrintCellTextValue($sheet, "B{$i}", $v ['productrule']);
            $this->SetPrintCellCenterValue($sheet, "C{$i}", $v ['productunit']);
            $this->SetPrintCellNumberValue($sheet, "D{$i}", Format::PriceString($v ['number'], 2));
            $this->SetPrintCellNumberValue($sheet, "E{$i}", Format::PriceString($v ['price'] * $v ['currencyrate'], 2));
            $this->SetPrintCellNumberValue($sheet, "F{$i}", Format::PriceString($totalprice, 2));
            $alltotalprice += $totalprice;
        }

        $i++;
        $sheet->mergeCells("A{$i}:D{$i}");
        $this->SetPrintCellTextValue($sheet, "A{$i}", "运输费用");
        $sheet->mergeCells("E{$i}:F{$i}");
        $this->SetPrintCellNumberValue($sheet, "E{$i}", Format::PriceString($rs ['carriage'] * $rs ['currencyrate'], 2));
        $alltotalprice += $rs ['carriage'] * $rs ['currencyrate'];

        $i++;
        $sheet->mergeCells("A{$i}:D{$i}");
        $this->SetPrintCellTextValue($sheet, "A{$i}", "其他费用");
        $sheet->mergeCells("E{$i}:F{$i}");
        $this->SetPrintCellNumberValue($sheet, "E{$i}", Format::PriceString($rs ['expense'] * $rs ['currencyrate'], 2));
        $alltotalprice += $rs ['expense'] * $rs ['currencyrate'];

        $alltotalprice = Format::PriceString($alltotalprice, 2);
        $alltotalpricermb = RMB::Number2RMB($alltotalprice);

        $i++;
        $sheet->mergeCells("A{$i}:D{$i}");
        $this->SetPrintCellTextValue($sheet, "A{$i}", "合计（人民币）：" . $alltotalpricermb);
        $sheet->mergeCells("E{$i}:F{$i}");
        $this->SetPrintCellTextRightValue($sheet, "E{$i}", $alltotalprice . "元");

        $this->SetPrintTableBorderStyle($sheet, "A11:F{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "二、交货和付款");
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("（1）付款方式：", $rs ['payment']);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("（2）发货日期：", $rs ['odate']);
        $sheet->getCell("C{$i}")->setValue($rich);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("（3）运输方式：", $rs ['traffic']);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("（4）交货地点：", $rs ['receiveinfo']);
        $sheet->getCell("A{$i}")->setValue($rich);
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("（5）发票寄送：", $rs ['billinfo']);
        $sheet->getCell("A{$i}")->setValue($rich);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "三、质量和验收");
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "        （１）供方承诺提供的货物是全新的，质量符合国家或国际有关技术标准，供方在货物的质保期内提供售后服务。\n        （２）需方在收到货物后5个工作日内，如对货物有异议，在妥善保管货物的同时，应以传真方式通知供方；需方未及时提出异议的，视为货物验收合格。\n        （３）需方因使用、保管不当等造成产品质量下降的，不得提出异议。");
        $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($i)->setRowHeight(75);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "四、违约责任");
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "        （１）供方逾期交货的，应按合同总金额每日千分之五计算，向需方支付逾期交货的违约金。需方逾期付款的，应按合同总金额每日千分之五计算，向供方支付逾期付款的违约金。\n        （２）供方不能按约定数量交货的，按不能交货部分货款的30%支付违约金。需方在货物无质量问题的情况下，要求退货的，应征得供方的同意，并按退回货物货款的30％支付违约金，同时承担因退货产生的相关费用。");
        $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($i)->setRowHeight(75);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "五、争议解决");
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "        （１）合同双方协商解决。\n        （２）向合同签定地人民法院起诉。");
        $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($i)->setRowHeight(30);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "六、不可抗力");
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "        任何一方由于不可抗力原因不能履行合同时，应在不可抗力事件结束后5个工作日内向对方通报，以减轻可能给对方造成的损失，在取得有关机构的不可抗力证明后，允许延期履行、部分履行或者不履行合同，并根据情况可部分或全部免予承担违约责任。");
        $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($i)->setRowHeight(45);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "七、其他条款");
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "        （１）本合同一式两份,经双方签字盖章后生效。在合同履行期内，供需双方均不得随意变更或解除合同，未尽事宜双方另行协商注释，以合同附件形式办理，其与合同具有同等法律效力。\n        （２）双方来往函件，按照合同规定的地址或传真号码以传真方式送达对方。如一方地址、电话、传真号码有变更，应在变更后的5个工作日内书面通知对方，否则，应承担相应责任。");
        $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($i)->setRowHeight(60);

        if (!empty ($rs ['additional'])) {
            $i++;
            $sheet->mergeCells("A{$i}:F{$i}");
            $sheet->setCellValue("A{$i}", "");

            $i++;
            $sheet->mergeCells("A{$i}:F{$i}");
            $sheet->setCellValue("A{$i}", "八、补充条款");
            $i++;
            $sheet->mergeCells("A{$i}:F{$i}");
            $rich = $this->CreateUnderlineText('', $rs ['additional']);
            $sheet->getCell("A{$i}")->setValue($rich);
            $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        }

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("供          方：", CompanyCls::FULL_NAME);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("需          方：", $rs ['customer']);
        $sheet->getCell("C{$i}")->setValue($rich);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", "签字盖章：____________________");
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "签字盖章：____________________");
        $sheet->getRowDimension($i)->setRowHeight(30);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("日          期：", Date::DateString());
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "日          期：____________________");
        $sheet->getRowDimension($i)->setRowHeight(30);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("开   户   行：", CompanyCls::BANK_ABC_NAME);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "开   户   行：____________________");
        $sheet->getRowDimension($i)->setRowHeight(30);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("帐          号：", CompanyCls::BANK_ABC_ACCOUNT);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "帐          号：____________________");
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->SetPrintHeaderFooter($sheet, $rs ['code']);

        $filename = "Purchase_" . $rs ['code'] . ".xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelPrintPurchaseView()
    {
        $id = UUID::Check(Url::Req('id', '', 'str'));

        $rs = PurchaseCls::Instance()->Item($id);
        $rp = PurchaseProductCls::getProductList($id);
        if (empty ($rs) && !empty ($rp)) {
            echo 'export no data!';
            return;
        }

        $contact_phone = CustomerContactCls::Instance()->GetPhone($rs ['contactid']);
        $contact_fax = CustomerContactCls::Instance()->GetFax($rs ['contactid']);
        $contact_mobile = CustomerContactCls::Instance()->GetMobile($rs ['contactid']);
        $contact_phone = CompanyCls::PhoneCombineString($contact_phone, $contact_mobile);
        $tax = $rs ['tax'] ? CompanyCls::TAX_VAT : CompanyCls::TAX_NOT;

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        $this->SetPageStandartA4Vertical($sheet);

        $sheet->setTitle('广通采购合同' . $rs ['code']);

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(9);
        $sheet->getColumnDimension('D')->setWidth(9);
        $sheet->getColumnDimension('E')->setWidth(9);
        $sheet->getColumnDimension('F')->setWidth(9);

        $i = 1;

        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", CompanyCls::FULL_NAME . "\n采 购 合 同");
        $sheet->getRowDimension($i)->setRowHeight(60);
        $this->SetPrintCaptionStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "签订地点：" . CompanyCls::SINGING_PLACE);
        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "订 单 号：" . $rs ['code']);
        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", "TO：" . $rs ['customer']);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "DATE：" . Date::DateString());
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", '　　' . $rs ['contact']);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "SHIP TO：" . CompanyCls::FULL_NAME);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", "TEL：" . $contact_phone);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "TEL：" . CompanyCls::TELEPHONE);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", "FAX：" . $contact_fax);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "FAX：" . CompanyCls::FAX);

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品名称');
        $sheet->setCellValue("B{$i}", '产品规格');
        $sheet->setCellValue("C{$i}", '单位');
        $sheet->setCellValue("D{$i}", '数量');
        $sheet->setCellValue("E{$i}", '单价');
        $sheet->setCellValue("F{$i}", '总价');

        $this->SetPrintTableTitleStyle($sheet, "A{$i}:F{$i}");

        $alltotalprice = 0;
        foreach ($rp as $v) {
            $i++;
            $totalprice = $v ['number'] * $v ['price'] * $v ['currencyrate'];
            $this->SetPrintCellTextValue($sheet, "A{$i}", $v ['product']);
            $this->SetPrintCellTextValue($sheet, "B{$i}", $v ['productrule']);
            $this->SetPrintCellCenterValue($sheet, "C{$i}", $v ['productunit']);
            $this->SetPrintCellNumberValue($sheet, "D{$i}", Format::PriceString($v ['number'], 2));
            $this->SetPrintCellNumberValue($sheet, "E{$i}", Format::PriceString($v ['price'] * $v ['currencyrate'], 2));
            $this->SetPrintCellNumberValue($sheet, "F{$i}", Format::PriceString($totalprice, 2));
            $alltotalprice += $totalprice;
        }

        $i++;
        $sheet->mergeCells("A{$i}:D{$i}");
        $this->SetPrintCellTextValue($sheet, "A{$i}", "运输费用");
        $sheet->mergeCells("E{$i}:F{$i}");
        $this->SetPrintCellNumberValue($sheet, "E{$i}", Format::PriceString($rs ['carriage'] * $rs ['currencyrate'], 2));
        $alltotalprice += $rs ['carriage'] * $rs ['currencyrate'];

        $i++;
        $sheet->mergeCells("A{$i}:D{$i}");
        $this->SetPrintCellTextValue($sheet, "A{$i}", "其他费用");
        $sheet->mergeCells("E{$i}:F{$i}");
        $this->SetPrintCellNumberValue($sheet, "E{$i}", Format::PriceString($rs ['expense'] * $rs ['currencyrate'], 2));
        $alltotalprice += $rs ['expense'] * $rs ['currencyrate'];

        $alltotalprice = Format::PriceString($alltotalprice, 2);
        $alltotalpricermb = RMB::Number2RMB($alltotalprice);

        $i++;
        $sheet->mergeCells("A{$i}:D{$i}");
        $this->SetPrintCellTextValue($sheet, "A{$i}", "合计（人民币）：" . $alltotalpricermb);
        $sheet->mergeCells("E{$i}:F{$i}");
        $this->SetPrintCellTextRightValue($sheet, "E{$i}", $alltotalprice . "元");

        $this->SetPrintTableBorderStyle($sheet, "A9:F{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "");

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $sheet->setCellValue("A{$i}", "一、约定条款：");
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("1、付款方式：", $rs ['payment']);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("2、到货日期：", $rs ['odate']);
        $sheet->getCell("C{$i}")->setValue($rich);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $rich = $this->CreateUnderlineText("3、运费支付：", $rs ['carriagepayment']);
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->mergeCells("C{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("4、是否含税：", $tax);
        $sheet->getCell("C{$i}")->setValue($rich);
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("5、收货联系：", $rs ['receiveinfo']);
        $sheet->getCell("A{$i}")->setValue($rich);
        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("6、发票寄送：", $rs ['billinfo']);
        $sheet->getCell("A{$i}")->setValue($rich);

        if (!empty ($rs ['additional'])) {
            $i++;
            $sheet->mergeCells("A{$i}:F{$i}");
            $sheet->setCellValue("A{$i}", $rs ['additional']);
            $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
            // $sheet->getRowDimension ( $i )->setRowHeight ( 60 );
        }

        $i++;
        $sheet->mergeCells("A{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("二、质量承诺：需方发现产品质量问题，须及时通知供方，按供方样品共同核实，并按供方产品使用说明书和产品质量保证书规定执行。提出异议期限为三十日。\n三、违约责任：按我国《合同法》及相关法律、法规执行。\n1、供方未按期交标物的，每日按逾期金额的千分之", "十", "支付违约金。\n2、因供方不能按照合同期限提供符合质量要求的产品，给需方造成的损失，由供方赔偿。\n四、解决合同纠纷的方式：\n（1）合同双方协商解决。\n（2）向签约当地经济合同仲裁机关申请仲裁。\n（3）向合同签订地人民法院起诉。\n五、本合同壹式贰份，供需双方各执行具有壹份，双方经办人签字、单位盖章后即刻生效，本合同的传真复印件与本合同原件同等法律效力。\n六、请签字盖章后回传，如不回传视为默认，传真：" . CompanyCls::FAX . "。");
        $sheet->getCell("A{$i}")->setValue($rich);
        $sheet->getStyle("A{$i}")->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($i)->setRowHeight(182);

        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", "                供货方");
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "                定货方");
        $sheet->getRowDimension($i)->setRowHeight(30);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", $rs ['customer']);
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", CompanyCls::FULL_NAME);
        $sheet->getRowDimension($i)->setRowHeight(30);
        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", "签字：__________ 盖章");
        $sheet->mergeCells("C{$i}:F{$i}");
        $sheet->setCellValue("C{$i}", "签字：__________ 盖章");
        $sheet->getRowDimension($i)->setRowHeight(30);

        $auditors = '';
        $ra = ApproveSequenceCls::GetLastSuccessAuditorList(ApproveObjectCls::PURCHASE, $id);
        if (!empty ($ra)) {
            $rx = array();
            foreach ($ra as $k => $v) {
                $rx [] = $v ['auditor'];
            }
            $auditors = implode(' , ', $rx);
        }

        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", "");
        $sheet->mergeCells("C{$i}:F{$i}");
        $rich = $this->CreateUnderlineText("复核：", $auditors);
        $sheet->getCell("C{$i}")->setValue($rich);

        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->SetPrintHeaderFooter($sheet, $rs ['code']);

        $filename = "Purchase_" . $rs ['code'] . ".xlsx";

        $this->excelOutput($writer, $filename);
        PurchaseCls::setPrint($id, 1);
    }

    public function ExcelCalendarReport()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::CALENDAR_CALENDAR_REPORT)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findyear = Url::Req('findyear', -1, 'int');
        $findmonth = Url::Req('findmonth', -1, 'int');
        $findavaliabledays = Url::Req('findavaliabledays', -1, 'int');
        $findtype = UUID::Check(Url::Req('findtype', '', 'str'));
        $finddepartmentid = UUID::Check(Url::Req('finddepartmentid', '', 'str'));
        $findcreatorid = UUID::Check(Url::Req('findcreatorid', '', 'str'));

        $where = '';

        if (!empty ($findtype)) {
            $where .= ' AND calendartypeid=\'' . $findtype . '\'::UUID';
        }

        // 得到用户列表
        $userlist = array();
        if ($this->IsAdmin()) {
            $depts = DepartmentCls::Instance()->GetRootSubIDList();
            foreach ($depts as $v) {
                $userlist = array_merge($userlist, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
            }
        } else {
            $userlist [] = $this->UID();
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $userlist = array_merge($userlist, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }
        }

        if (!empty ($finddepartmentid)) {
            $filteruser = array();
            $keys = DepartmentCls::Instance()->GetChildren($finddepartmentid);
            $keys = UserCls::Instance()->getIDListByDepartmentIDList($keys);
            foreach ($userlist as $user) {
                foreach ($keys as $key) {
                    if ($user == $key) $filteruser [] = $user;
                }
            }
            $userlist = $filteruser;
        }
        if (!empty ($findcreatorid)) {
            $filteruser = array();
            foreach ($userlist as $user) {
                if ($user == $findcreatorid) $filteruser [] = $user;
            }
            $userlist = $filteruser;
        }

        $time = Date::ToTime($findyear . '-' . $findmonth . '-01');
        $year = Date::Year($time);
        $month = Date::Month($time, false);
        $days = Date::MonthDays($time);
        $yearmonth = $year . '-' . Date::Month($time);

        // 创建用户数据
        $rs = array();
        $rr = CalendarCls::queryUserMonthRecordNumberList($yearmonth, $where);

        // 创建用户日期二维数据结构
        foreach ($userlist as $v) {
            if (!array_key_exists($v, $rs)) {
                $rs [$v] = array('name' => UserCls::Instance()->GetUName($v), 'data' => array_fill(1, $days + 1, 0));
                $rs [$v] ['data'] ['total'] = 0;
                $rs [$v] ['data'] ['avaliable'] = 0;
            }
        }

        // 填充用户数据
        foreach ($rr as $v) {
            if (array_key_exists($v ['creatorid'], $rs)) {
                $rs [$v ['creatorid']] ['data'] [$v ['month']] = $v ['count'];
                // 记录总数及大 于零的总数
                $rs [$v ['creatorid']] ['data'] ['total'] += $v ['count'];
                if ($v ['count'] > 0 && $v ['month'] > 0) $rs [$v ['creatorid']] ['data'] ['avaliable'] += 1;
            }
        }

        // 创建表头结构
        $rt = array();
        $avaliabledays = $days;
        for ($i = 1; $i <= $days; $i++) {
            $ctime = Date::ToTime($yearmonth . '-' . $i);
            $cweekday = Date::DayOfWeek($ctime);
            $rt [$i] = array('weekday' => $cweekday, 'name' => Date::$CHINESE_WEEKDAY [$cweekday]);
            if ($cweekday == 6 || $cweekday == 7) $avaliabledays--;
        }

        $avaliabledays = ($findavaliabledays > 0) ? $findavaliabledays : $avaliabledays;

        if (empty ($rs) || empty ($rt)) {
            echo 'export no data!';
            return;
        }

        // 重新处理成Excel表结构
        $rx = array();
        //
        $ri1 = array_fill(1, 5, '');
        $ri2 = array($yearmonth, '少做', '应做', '做天', '总条');
        for ($i = 1; $i <= $days; $i++) {
            $ri1 [] = $rt [$i] ['name'];
            $ri2 [] = $i;
        }
        $rx [] = $ri1;
        $rx [] = $ri2;
        //
        foreach ($rs as $k => $v) {
            $ri = array($v ['name'], $avaliabledays - $v ['data'] ['avaliable'], $avaliabledays, $v ['data'] ['avaliable'], $v ['data'] ['total']);
            for ($i = 1; $i <= $days; $i++) {
                $ri [] = $v ['data'] [$i];
            }
            $rx [] = $ri;
        }

        //
        //
        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通日程月报');

        $i = 1;

        $sheet->mergeCells("A{$i}:Y{$i}");
        $sheet->setCellValue("A{$i}", '广通日程月报');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:Y{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;

        $sheet->fromArray($rx, NULL, "A{$i}");

        $filename = "CalendarReport.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelCustomerList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::CUSTOMER_CUSTOMER)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = Url::Req('findname', '', 'str');

        $where = 'WHERE del=false';

        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList(ResourceCls::CUSTOMER_CUSTOMER));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR team LIKE \'%' . $this->UID() . '%\')';
        }
        if (!empty ($findname)) {
            $where .= ' AND (name like \'%' . $findname . '%\' OR code like \'%' . $findname . '%\' OR address like \'%' . $findname . '%\' OR phone like \'%' . $findname . '%\' OR fax like \'%' . $findname . '%\' OR postal like \'%' . $findname . '%\' OR homepage like \'%' . $findname . '%\' OR memo like \'%' . $findname . '%\') ';
        }
        $order = 'ORDER BY time DESC';

        $rs = CustomerCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通客户列表');

        $i = 1;

        $sheet->mergeCells("A{$i}:Y{$i}");
        $sheet->setCellValue("A{$i}", '广通客户列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:Y{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '客户ID');
        $sheet->setCellValue("B{$i}", '客户名称');
        $sheet->setCellValue("C{$i}", '客户代码');
        $sheet->setCellValue("D{$i}", '客户类型ID');
        $sheet->setCellValue("E{$i}", '客户类型');
        $sheet->setCellValue("F{$i}", '电话');
        $sheet->setCellValue("G{$i}", '传真');
        $sheet->setCellValue("H{$i}", '地址');
        $sheet->setCellValue("I{$i}", '邮编');
        $sheet->setCellValue("J{$i}", '网站');
        $sheet->setCellValue("K{$i}", '备注');
        $sheet->setCellValue("L{$i}", '创建者ID');
        $sheet->setCellValue("M{$i}", '创建者');
        $sheet->setCellValue("N{$i}", '实付平衡');
        $sheet->setCellValue("O{$i}", '应收总额');
        $sheet->setCellValue("P{$i}", '应付总额');
        $sheet->setCellValue("Q{$i}", '实收总额');
        $sheet->setCellValue("R{$i}", '实付总额');
        $sheet->setCellValue("S{$i}", '初始实收');
        $sheet->setCellValue("T{$i}", '初始实付');
        $sheet->setCellValue("U{$i}", '逾期天数');
        $sheet->setCellValue("V{$i}", '逾期金额');
        $sheet->setCellValue("W{$i}", '过期天数');
        $sheet->setCellValue("X{$i}", '最后收款');
        $sheet->setCellValue("Y{$i}", '最后付款');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:Y{$i}");

        foreach ($rs as $v) {
            $i++;

            $sheet->setCellValue("A{$i}", $v ['id']);
            $sheet->setCellValue("B{$i}", $v ['name']);
            $sheet->setCellValue("C{$i}", $v ['code']);
            $sheet->setCellValue("D{$i}", $v ['customertypeid']);
            $sheet->setCellValue("E{$i}", $v ['customertype']);
            $sheet->setCellValue("F{$i}", $v ['phone']);
            $sheet->setCellValue("G{$i}", $v ['fax']);
            $sheet->setCellValue("H{$i}", $v ['address']);
            $sheet->setCellValue("I{$i}", $v ['postal']);
            $sheet->setCellValue("J{$i}", $v ['homepage']);
            $sheet->setCellValue("K{$i}", $v ['memo']);
            $sheet->setCellValue("L{$i}", $v ['creatorid']);
            $sheet->setCellValue("M{$i}", $v ['creator']);
            $sheet->setCellValue("N{$i}", $v ['availablemoney']);
            $sheet->setCellValue("O{$i}", $v ['inventoryimport']);
            $sheet->setCellValue("P{$i}", $v ['inventoryexport']);
            $sheet->setCellValue("Q{$i}", $v ['importmoney']);
            $sheet->setCellValue("R{$i}", $v ['exportmoney']);
            $sheet->setCellValue("S{$i}", $v ['initimport']);
            $sheet->setCellValue("T{$i}", $v ['initexport']);
            $sheet->setCellValue("U{$i}", $v ['limitday']);
            $sheet->setCellValue("V{$i}", $v ['limitmoney']);
            $sheet->setCellValue("W{$i}", $v ['expireday']);
            $sheet->setCellValue("X{$i}", $v ['importlast']);
            $sheet->setCellValue("Y{$i}", $v ['exportlast']);
        }

        $sheet->getStyle("A3:Y{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "CustomerList.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryTrackList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_TRACK)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = Url::Req('findname', '', 'str');
        $finddepartmentid = UUID::Check(Url::Req('finddepartmentid', '', 'str'));
        $findrepositoryid = UUID::Check(Url::Req('findrepositoryid', '', 'str'));
        $findfromnumber = trim(Url::Req('findfromnumber', '', 'str'));
        $findtonumber = trim(Url::Req('findtonumber', '', 'str'));
        $findfromprice = trim(Url::Req('findfromprice', '', 'str'));
        $findtoprice = trim(Url::Req('findtoprice', '', 'str'));
        $findfromtotal = trim(Url::Req('findfromtotal', '', 'str'));
        $findtototal = trim(Url::Req('findtototal', '', 'str'));

        $findcatalogid = UUID::Check(Url::Req('findcatalogid', '', 'str'));
        $findproductstatusid = Url::Req('findproductstatusid', 0, 'int');

        $where = $this->FindInventoryInitTrackWhere('', $finddepartmentid, $findcatalogid, $findname, $findrepositoryid, $findfromnumber, $findtonumber, $findfromprice, $findtoprice, $findfromtotal, $findtototal, $findproductstatusid);
        $order = 'ORDER BY productid, time DESC';

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_TRACK);

        $rs = InventoryTrackCls::queryList($where, $order, 0, 0);
        $localavailable = $localcost = $localtotalcost = 0;

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通库存量列表');

        $i = 1;

        $sheet->mergeCells("A{$i}:AD{$i}");
        $sheet->setCellValue("A{$i}", '广通库存量列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:AD{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '产品ID');
        $sheet->setCellValue("B{$i}", '产品代码');
        $sheet->setCellValue("C{$i}", '产品名称');
        $sheet->setCellValue("D{$i}", '分类ID');
        $sheet->setCellValue("E{$i}", '四级分类名称');
        $sheet->setCellValue("F{$i}", '三级分类名称');
        $sheet->setCellValue("G{$i}", '二级分类名称');
        $sheet->setCellValue("H{$i}", '一级分类名称');
        $sheet->setCellValue("I{$i}", '规格');
        $sheet->setCellValue("J{$i}", '单位');
        $sheet->setCellValue("K{$i}", '仓库ID');
        $sheet->setCellValue("L{$i}", '仓库名称');
        $sheet->setCellValue("M{$i}", '常备库存');
        if ($showprice) {
            $sheet->setCellValue("N{$i}", '库存量');
            $sheet->setCellValue("O{$i}", '成本');
            $sheet->setCellValue("P{$i}", '库存总价');
        } else {
            $sheet->mergeCells("M{$i}:O{$i}");
            $sheet->setCellValue("P{$i}", '库存量');
        }
        $sheet->setCellValue("Q{$i}", '入库量');
        $sheet->setCellValue("R{$i}", '出库量');
        $sheet->setCellValue("S{$i}", '借出量');
        $sheet->setCellValue("T{$i}", '归还量');
        $sheet->setCellValue("U{$i}", '退货量');
        $sheet->setCellValue("V{$i}", '退回量');
        $sheet->setCellValue("W{$i}", '报废量');
        $sheet->setCellValue("X{$i}", '预警阀值');
        $sheet->setCellValue("Y{$i}", '过期天数');
        $sheet->setCellValue("Z{$i}", '供货编号');
        $sheet->setCellValue("AA{$i}", '库位ID');
        $sheet->setCellValue("AB{$i}", '库位');
        $sheet->setCellValue("AC{$i}", '初始时间');
        $sheet->setCellValue("AD{$i}", '最后时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:AD{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['productid']);
            $sheet->setCellValue("B{$i}", $v ['productsku']);
            $sheet->setCellValue("C{$i}", $v ['product']);
            $sheet->setCellValue("D{$i}", $v ['productid']);
            $sheet->setCellValue("E{$i}", $v ['productcatalogid']);
            $pid = ProductCatalogCls::Instance()->GetPID($v ['productcatalogid']);
            $pname = ProductCatalogCls::Instance()->GetName($pid);
            $sheet->setCellValue("F{$i}", $pname);
            $pid = ProductCatalogCls::Instance()->GetPID($pid);
            $pname = ProductCatalogCls::Instance()->GetName($pid);
            $sheet->setCellValue("G{$i}", $pname);
            $pid = ProductCatalogCls::Instance()->GetPID($pid);
            $pname = ProductCatalogCls::Instance()->GetName($pid);
            $sheet->setCellValue("H{$i}", $pname);
            $sheet->setCellValue("I{$i}", $v ['productrule']);
            $sheet->setCellValue("J{$i}", $v ['productunit']);
            $sheet->setCellValue("K{$i}", $v ['repositoryid']);
            $sheet->setCellValue("L{$i}", $v ['repository']);
            $sheet->setCellValue("M{$i}", $v ['always']);
            if ($showprice) {
                $sheet->setCellValue("N{$i}", $v ['available']);
                $sheet->setCellValue("O{$i}", $v ['cost']);
                $sheet->setCellValue("P{$i}", $v ['totalcost']);
            } else {
                $sheet->mergeCells("M{$i}:O{$i}");
                $sheet->setCellValue("P{$i}", $v ['available']);
            }
            $sheet->setCellValue("Q{$i}", $v ['pin']);
            $sheet->setCellValue("R{$i}", $v ['pout']);
            $sheet->setCellValue("S{$i}", $v ['borrow']);
            $sheet->setCellValue("T{$i}", $v ['revert']);
            $sheet->setCellValue("U{$i}", $v ['return']);
            $sheet->setCellValue("V{$i}", $v ['sendback']);
            $sheet->setCellValue("W{$i}", $v ['castoff']);
            $sheet->setCellValue("X{$i}", $v ['expirenumber']);
            $sheet->setCellValue("Y{$i}", $v ['expireday']);
            $sheet->setCellValue("Z{$i}", $v ['productserial']);
            $sheet->setCellValue("AA{$i}", $v ['shelfid']);
            $sheet->setCellValue("AB{$i}", $v ['shelf']);
            $sheet->setCellValue("AC{$i}", Format::DateString($v ['time']));
            $sheet->setCellValue("AD{$i}", Format::DateString($v ['last']));

            $localavailable += $v ['available'];
            $localcost += $v ['cost'];
            $localtotalcost += $v ['totalcost'];
        }

        $sheet->getStyle("A3:AD{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        if ($showprice) {
            $i++;
            $sheet->setCellValue("A{$i}", '总计数量：');
            $sheet->setCellValue("B{$i}", Format::PriceString($localavailable));
            $i++;
            $sheet->setCellValue("A{$i}", '总计成本：');
            $sheet->setCellValue("B{$i}", Format::PriceString($localcost));
            $i++;
            $sheet->setCellValue("A{$i}", '总计总价：');
            $sheet->setCellValue("B{$i}", Format::PriceString($localtotalcost));
        }

        $filename = "InventoryTrackList.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryInList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_IN)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_IN);

        $findname = trim(Url::Req('findname', '', 'str'));
        $findrepositoryid = UUID::Check(Url::Req('findrepositoryid', '', 'str'));
        $findstateid = Url::Req('findstateid', -1, 'int');
        $findcustomerid = UUID::Check(Url::Req('findcustomerid', '', 'str'));
        $findcustomer2 = trim(Url::Req('findcustomer2', '', 'str'));
        $findfromdate = Date::Format(Url::Req('findfromdate', '', 'str'), 'd', '');
        $findtodate = Date::Format(Url::Req('findtodate', '', 'str'), 'd', '');
        $findfromtotal = trim(Url::Req('findfromtotal', '', 'str'));
        $findtototal = trim(Url::Req('findtototal', '', 'str'));
        $findowners = trim(Url::Req('findowners', '', 'str'));
        $findcreators = trim(Url::Req('findcreators', '', 'str'));
        $findcurrencyid = UUID::Check(Url::Req('findcurrencyid', '', 'str'));
        $findtax = Url::Req('findtax', 0, 'int');
        $findopenup = Url::Req('findopenup', 0, 'int');
        $findfinishedstateid = Url::Req('findfinishedstateid', -1, 'int');

        $where = $this->FindInventoryWhere(InventoryCls::INVENTORY_TYPE_IN, ResourceCls::INVENTORY_IN, $findname, $findcurrencyid, $findrepositoryid, $findstateid, $findcustomerid, $findcustomer2, $findfromdate, $findtodate, $findfromtotal, $findtototal, $findowners, $findcreators, $findtax, $findopenup, $findfinishedstateid);

        /*
		 * $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_IN . ' AND del=false'; if (! $this->IsAdmin ()) { $perm = array ( $this->UID () ); $lead = UserCls::Instance ()->getLead ( $this->UID () ); if ($lead) { $dept = UserCls::Instance ()->getDepartmentID ( $this->UID () ); $depts = DepartmentCls::Instance ()->GetChildren ( $dept ); foreach ( $depts as $v ) { $perm = array_merge ( $perm, array_keys ( UserCls::Instance ()->getUsersByDepartmentID ( $v ) ) ); } } $perm = array_merge ( $perm, AclCls::Instance ( $this->UID () )->getRoleResourceViewUserList ( ResourceCls::INVENTORY_IN ) ); $perm = "'" . implode ( "','", $perm ) . "'"; $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) '; }
		 */

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通入库单列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:Z{$i}");
        $sheet->setCellValue("A{$i}", '广通入库单列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:Z{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:H{$i}");
            $sheet->setCellValue("I{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("J{$i}", '入库单号');
        $sheet->setCellValue("K{$i}", '负责人');
        $sheet->setCellValue("L{$i}", '供货客户');
        $sheet->setCellValue("M{$i}", '入库日期');
        $sheet->setCellValue("N{$i}", '仓库');
        $sheet->setCellValue("O{$i}", '采购单');
        $sheet->setCellValue("P{$i}", '主入库单');
        $sheet->setCellValue("Q{$i}", '合同');
        $sheet->setCellValue("R{$i}", '运输方式');
        if ($showprice) {
            $sheet->setCellValue("S{$i}", '运输费用');
            $sheet->setCellValue("T{$i}", '其他费用');
            $sheet->setCellValue("U{$i}", '产品总价');
            $sheet->setCellValue("V{$i}", '合计');
            $sheet->setCellValue("W{$i}", '备注');
        } else {
            $sheet->mergeCells("R{$i}:V{$i}");
            $sheet->setCellValue("W{$i}", '备注');
        }
        $sheet->setCellValue("X{$i}", '状态');
        $sheet->setCellValue("Y{$i}", '创建者');
        $sheet->setCellValue("Z{$i}", '创建时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:Z{$i}");

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                } else {
                    $sheet->mergeCells("F{$i}:H{$i}");
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("J{$i}", $v ['code']);
                $sheet->setCellValue("K{$i}", $v ['owner']);
                $sheet->setCellValue("L{$i}", $v ['customer']);
                $sheet->setCellValue("M{$i}", $v ['odate']);
                $sheet->setCellValue("N{$i}", $v ['repository']);
                $sheet->setCellValue("O{$i}", $v ['purchase']);
                $sheet->setCellValue("P{$i}", $v ['inventory']);
                $sheet->setCellValue("Q{$i}", $v ['contract']);
                $sheet->setCellValue("R{$i}", $v ['traffic']);
                if ($showprice) {
                    $sheet->setCellValue("S{$i}", $v ['carriage']);
                    $sheet->setCellValue("T{$i}", $v ['expense']);
                    $sheet->setCellValue("U{$i}", $v ['totalprice']);
                    $sheet->setCellValue("V{$i}", $v ['total']);
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("R{$i}:V{$i}");
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                }
                $sheet->setCellValue("X{$i}", $v ['state']);
                $sheet->setCellValue("Y{$i}", $v ['creator']);
                $sheet->setCellValue("Z{$i}", $v ['time']);
            }
        }

        $sheet->getStyle("A3:Z{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryIn.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryOutList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_OUT)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_OUT);

        $findname = trim(Url::Req('findname', '', 'str'));
        $findrepositoryid = UUID::Check(Url::Req('findrepositoryid', '', 'str'));
        $findstateid = Url::Req('findstateid', -1, 'int');
        $findcustomerid = UUID::Check(Url::Req('findcustomerid', '', 'str'));
        $findcustomer2 = trim(Url::Req('findcustomer2', '', 'str'));
        $findfromdate = Date::Format(Url::Req('findfromdate', '', 'str'), 'd', '');
        $findtodate = Date::Format(Url::Req('findtodate', '', 'str'), 'd', '');
        $findfromtotal = trim(Url::Req('findfromtotal', '', 'str'));
        $findtototal = trim(Url::Req('findtototal', '', 'str'));
        $findowners = trim(Url::Req('findowners', '', 'str'));
        $findcreators = trim(Url::Req('findcreators', '', 'str'));
        $findpaymentid = UUID::Check(Url::Req('findpaymentid', '', 'str'));
        $findcurrencyid = UUID::Check(Url::Req('findcurrencyid', '', 'str'));
        $findtax = Url::Req('findtax', 0, 'int');
        $findopenup = Url::Req('findopenup', 0, 'int');
        $findfinishedstateid = Url::Req('findfinishedstateid', -1, 'int');

        $where = $this->FindInventoryWhere(InventoryCls::INVENTORY_TYPE_OUT, ResourceCls::INVENTORY_OUT, $findname, $findcurrencyid, $findrepositoryid, $findstateid, $findcustomerid, $findcustomer2, $findfromdate, $findtodate, $findfromtotal, $findtototal, $findowners, $findcreators, $findtax, $findopenup, $findfinishedstateid, $findpaymentid);

        /*
		 * $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_OUT . ' AND del=false'; if (! $this->IsAdmin ()) { $perm = array ( $this->UID () ); $lead = UserCls::Instance ()->getLead ( $this->UID () ); if ($lead) { $dept = UserCls::Instance ()->getDepartmentID ( $this->UID () ); $depts = DepartmentCls::Instance ()->GetChildren ( $dept ); foreach ( $depts as $v ) { $perm = array_merge ( $perm, array_keys ( UserCls::Instance ()->getUsersByDepartmentID ( $v ) ) ); } } $perm = array_merge ( $perm, AclCls::Instance ( $this->UID () )->getRoleResourceViewUserList ( ResourceCls::INVENTORY_OUT ) ); $perm = "'" . implode ( "','", $perm ) . "'"; $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) '; }
		 */

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通出库单列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:AG{$i}");
        $sheet->setCellValue("A{$i}", '广通出库单列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:AG{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '成本');
            $sheet->setCellValue("J{$i}", '成本合计');
            $sheet->setCellValue("K{$i}", '利润');
            $sheet->setCellValue("L{$i}", '利润合计');
            $sheet->setCellValue("M{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:L{$i}");
            $sheet->setCellValue("M{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("N{$i}", '出库单号');
        $sheet->setCellValue("O{$i}", '负责人');
        $sheet->setCellValue("P{$i}", '销售客户');
        $sheet->setCellValue("Q{$i}", '出库日期');
        $sheet->setCellValue("R{$i}", '仓库');
        $sheet->setCellValue("S{$i}", '付款方式');
        $sheet->setCellValue("T{$i}", '主出库单');
        $sheet->setCellValue("U{$i}", '合同');
        $sheet->setCellValue("V{$i}", '运输方式');
        if ($showprice) {
            $sheet->setCellValue("W{$i}", '运输费用');
            $sheet->setCellValue("X{$i}", '其他费用');
            $sheet->setCellValue("Y{$i}", '产品总价');
            $sheet->setCellValue("Z{$i}", '成本总计');
            $sheet->setCellValue("AA{$i}", '利润总计');
            $sheet->setCellValue("AB{$i}", '总计');
            $sheet->setCellValue("AC{$i}", '备注');
        } else {
            $sheet->mergeCells("V{$i}:AB{$i}");
            $sheet->setCellValue("AC{$i}", '备注');
        }
        $sheet->setCellValue("AD{$i}", '状态');
        $sheet->setCellValue("AE{$i}", '创建者');
        $sheet->setCellValue("AF{$i}", '创建时间');
        $sheet->setCellValue("AG{$i}", '新开拓');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:AG{$i}");

        $alltotalcost = $alltotalprofit = $alltotal = $alltotalprice = 0;

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            $j = $i;
            $j++;

            $totalcost = $totalprofit = 0;

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['cost']);
                    $sheet->setCellValue("J{$i}", $n ['totalcost']);
                    $sheet->setCellValue("K{$i}", $n ['profit']);
                    $sheet->setCellValue("L{$i}", $n ['totalprofit']);
                    $sheet->setCellValue("M{$i}", $n ['memo']);

                    $totalcost += $n ['totalcost'];
                    $totalprofit += $n ['totalprofit'];
                    $alltotalcost += $n ['totalcost'];
                    $alltotalprofit += $n ['totalprofit'];
                } else {
                    $sheet->mergeCells("F{$i}:L{$i}");
                    $sheet->setCellValue("M{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("N{$i}", $v ['code']);
                $sheet->setCellValue("O{$i}", $v ['owner']);
                $sheet->setCellValue("P{$i}", $v ['customer']);
                $sheet->setCellValue("Q{$i}", $v ['odate']);
                $sheet->setCellValue("R{$i}", $v ['repository']);
                $sheet->setCellValue("S{$i}", $v ['payment']);
                $sheet->setCellValue("T{$i}", $v ['inventory']);
                $sheet->setCellValue("U{$i}", $v ['contract']);
                $sheet->setCellValue("V{$i}", $v ['traffic']);
                if ($showprice) {
                    $sheet->setCellValue("W{$i}", $v ['carriage']);
                    $sheet->setCellValue("X{$i}", $v ['expense']);
                    $sheet->setCellValue("Y{$i}", $v ['totalprice']);
                    $sheet->setCellValue("Z{$i}", 0); // $totalcost
                    $sheet->setCellValue("AA{$i}", 0); // $totalprofit
                    $sheet->setCellValue("AB{$i}", $v ['total']);
                    $sheet->setCellValue("AC{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("V{$i}:AB{$i}");
                    $sheet->setCellValue("AC{$i}", $v ['memo']);
                }
                $sheet->setCellValue("AD{$i}", $v ['state']);
                $sheet->setCellValue("AE{$i}", $v ['creator']);
                $sheet->setCellValue("AF{$i}", $v ['time']);
                $sheet->setCellValue("AG{$i}", $v ['openup']);
            }

            $alltotalprice += $v ['totalprice'];
            $alltotal += $v ['total'];
            for ($x = $j; $x <= $i; $x++) {
                $sheet->setCellValue("Z{$x}", $totalcost);
                $sheet->setCellValue("AA{$x}", $totalprofit);
            }
        }

        if ($showprice) {
            $i++;
            $sheet->setCellValue("A{$i}", '产品共计：');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $alltotalprice);
            $sheet->mergeCells("E{$i}:AG{$i}");

            $i++;
            $sheet->setCellValue("A{$i}", '成本共计：');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $alltotalcost);
            $sheet->mergeCells("E{$i}:AG{$i}");

            $i++;
            $sheet->setCellValue("A{$i}", '利润共计：');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $alltotalprofit);
            $sheet->mergeCells("E{$i}:AF{$i}");

            $i++;
            $sheet->setCellValue("A{$i}", '共计：');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $alltotal);
            $sheet->mergeCells("E{$i}:AG{$i}");
        }

        $sheet->getStyle("A3:AG{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryOut.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryOutView()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_OUT)) {
            UtilMod::MessageBoxUnableView();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_OUT);

        $id = UUID::Check(Url::Req('id', '', 'str'));

        $rs = InventoryCls::Instance()->Item($id);

        if (empty ($rs)) {
            echo 'export error!';
            return;
        }

        $rp = InventoryProductCls::getProductList($id); // 产品列表
        $rc = CustomerCls::Instance()->Item($rs ['customerid']); // 客户信息
        $ro = CustomerContactCls::Instance()->Item($rs ['contactid']); // 联系人信息

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle($rs ['code']);

        $i = 1;

        $sheet->mergeCells("A{$i}:I{$i}");
        $sheet->setCellValue("A{$i}", '广通出库单 ' . $rs ['code']);
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:B{$i}");
        $sheet->setCellValue("A{$i}", '状态：' . $rs ['state']);
        $sheet->mergeCells("C{$i}:G{$i}");
        $sheet->setCellValue("C{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("H{$i}:I{$i}");
        $sheet->setCellValue("H{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("C{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '出库单号');
        $sheet->mergeCells("B{$i}:D{$i}");
        $sheet->setCellValue("B{$i}", $rs ['code']);
        $sheet->setCellValue("E{$i}", '负责人');
        $sheet->mergeCells("F{$i}:I{$i}");
        $sheet->setCellValue("F{$i}", $rs ['owner']);
        $i++;
        $sheet->setCellValue("A{$i}", '销售客户');
        $sheet->mergeCells("B{$i}:D{$i}");
        $customer = $rs ['customer'];
        if (!empty ($rs ['contactid'])) $customer .= ' (' . $rs ['contact'] . ')';
        $sheet->setCellValue("B{$i}", $customer);
        $sheet->setCellValue("E{$i}", '主出库单');
        $sheet->mergeCells("F{$i}:I{$i}");
        $sheet->setCellValue("F{$i}", $rs ['inventory']);
        $i++;
        $sheet->setCellValue("A{$i}", '出库日期');
        $sheet->mergeCells("B{$i}:D{$i}");
        $sheet->setCellValue("B{$i}", $rs ['odate']);
        $sheet->setCellValue("E{$i}", '合同');
        $sheet->mergeCells("F{$i}:I{$i}");
        $sheet->setCellValue("F{$i}", $rs ['contract']);
        $i++;
        $sheet->setCellValue("A{$i}", '运输方式');
        $sheet->mergeCells("B{$i}:I{$i}");
        $sheet->setCellValue("B{$i}", $rs ['traffic']);

        if ($showprice) {
            $i++;
            $sheet->setCellValue("A{$i}", '运输费用');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $rs ['carriage']);
            $sheet->setCellValue("E{$i}", '其他费用');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $rs ['expense']);
            $i++;
            $sheet->setCellValue("A{$i}", '产品总价');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $rs ['totalprice']);
            $sheet->setCellValue("E{$i}", '合计');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $rs ['total']);
        }

        $i++;
        $sheet->setCellValue("A{$i}", '备注');
        $sheet->mergeCells("B{$i}:I{$i}");
        $sheet->setCellValue("B{$i}", $rs ['memo']);
        $i++;
        $sheet->setCellValue("A{$i}", '创建者');
        $sheet->mergeCells("B{$i}:D{$i}");
        $sheet->setCellValue("B{$i}", $rs ['creator']);
        $sheet->setCellValue("E{$i}", '创建时间');
        $sheet->mergeCells("F{$i}:I{$i}");
        $sheet->setCellValue("F{$i}", $rs ['time']);

        if (!empty ($rc)) {
            $i++;
            $sheet->mergeCells("A{$i}:I{$i}");
            $sheet->setCellValue("A{$i}", '客户信息');

            $this->excelSetSubTitleStyle($sheet, "A{$i}");

            $i++;
            $sheet->setCellValue("A{$i}", '客户名称');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $rc ['name']);
            $sheet->setCellValue("E{$i}", '客户代码');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $rc ['code']);
            $i++;
            $sheet->setCellValue("A{$i}", '电话');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $rc ['phone']);
            $sheet->setCellValue("E{$i}", '地址');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $rc ['address']);
            $i++;
            $sheet->setCellValue("A{$i}", '传真');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $rc ['fax']);
            $sheet->setCellValue("E{$i}", '邮编');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $rc ['postal']);
        }

        if (!empty ($ro)) {
            $i++;
            $sheet->mergeCells("A{$i}:I{$i}");
            $sheet->setCellValue("A{$i}", '联系人信息');

            $this->excelSetSubTitleStyle($sheet, "A{$i}");

            $i++;
            $sheet->setCellValue("A{$i}", '姓名');
            $sheet->mergeCells("B{$i}:I{$i}");
            $sheet->setCellValue("B{$i}", $ro ['name']);
            $i++;
            $sheet->setCellValue("A{$i}", '手机');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $ro ['mobile']);
            $sheet->setCellValue("E{$i}", '邮箱');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $ro ['email']);
            $i++;
            $sheet->setCellValue("A{$i}", '电话');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $ro ['phone']);
            $sheet->setCellValue("E{$i}", '地址');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $ro ['address']);
            $i++;
            $sheet->setCellValue("A{$i}", '传真');
            $sheet->mergeCells("B{$i}:D{$i}");
            $sheet->setCellValue("B{$i}", $ro ['fax']);
            $sheet->setCellValue("E{$i}", '邮编');
            $sheet->mergeCells("F{$i}:I{$i}");
            $sheet->setCellValue("F{$i}", $ro ['postal']);
        }

        if (!empty ($rp)) {
            $i++;
            $sheet->mergeCells("A{$i}:I{$i}");
            $sheet->setCellValue("A{$i}", '产品清单');

            $this->excelSetSubTitleStyle($sheet, "A{$i}");

            $i++;
            $sheet->setCellValue("A{$i}", '产品代码');
            $sheet->setCellValue("B{$i}", '产品名称');
            $sheet->setCellValue("C{$i}", '规格');
            $sheet->setCellValue("D{$i}", '单位');
            $sheet->setCellValue("E{$i}", '仓库');
            $sheet->setCellValue("F{$i}", '数量');
            if ($showprice) {
                $sheet->setCellValue("G{$i}", '单价');
                $sheet->setCellValue("H{$i}", '合计');
                $sheet->setCellValue("I{$i}", '备注');
            } else {
                $sheet->mergeCells("G{$i}:I{$i}");
                $sheet->setCellValue("G{$i}", '备注');
            }

            $this->excelSetSubTitleStyle($sheet, "A{$i}:I{$i}", 'FFF6F6F6');

            foreach ($rp as $p) {
                $i++;
                $sheet->setCellValue("A{$i}", ProductCls::Instance()->GetCode($p ['productid']));
                $sheet->setCellValue("B{$i}", ProductCls::Instance()->GetName($p ['productid']));
                $sheet->setCellValue("C{$i}", ProductCls::Instance()->GetRule($p ['productid']));
                $sheet->setCellValue("D{$i}", ProductCls::Instance()->GetUnit($p ['productid']));
                $sheet->setCellValue("E{$i}", RepositoryCls::Instance()->GetName($p ['repositoryid']));
                $sheet->setCellValue("F{$i}", $p ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $p ['price']);
                    $sheet->setCellValue("H{$i}", $p ['number'] * $p ['price']);
                    $sheet->setCellValue("I{$i}", $p ['memo']);
                } else {
                    $sheet->mergeCells("G{$i}:I{$i}");
                    $sheet->setCellValue("G{$i}", $p ['memo']);
                }
            }
        }

        $sheet->getStyle("A3:G{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = $rs ['code'] . ".xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryBorrowList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_BORROW)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_BORROW);

        $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_BORROW . ' AND del=false';

        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList(ResourceCls::INVENTORY_BORROW));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) ';
        }

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通借出列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:V{$i}");
        $sheet->setCellValue("A{$i}", '广通借出列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:J{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("K{$i}:V{$i}");
        $sheet->setCellValue("K{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("K{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:H{$i}");
            $sheet->setCellValue("I{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("J{$i}", '借出单号');
        $sheet->setCellValue("K{$i}", '借出者');
        $sheet->setCellValue("L{$i}", '借出客户');
        $sheet->setCellValue("M{$i}", '借出日期');
        $sheet->setCellValue("N{$i}", '仓库');
        $sheet->setCellValue("O{$i}", '主借出单');
        if ($showprice) {
            $sheet->setCellValue("P{$i}", '其他费用');
            $sheet->setCellValue("Q{$i}", '产品总价');
            $sheet->setCellValue("R{$i}", '合计');
            $sheet->setCellValue("S{$i}", '备注');
        } else {
            $sheet->mergeCells("O{$i}:R{$i}");
            $sheet->setCellValue("S{$i}", '备注');
        }
        $sheet->setCellValue("T{$i}", '状态');
        $sheet->setCellValue("U{$i}", '创建者');
        $sheet->setCellValue("V{$i}", '创建时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:V{$i}");

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                } else {
                    $sheet->mergeCells("F{$i}:H{$i}");
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("J{$i}", $v ['code']);
                $sheet->setCellValue("K{$i}", $v ['owner']);
                $sheet->setCellValue("L{$i}", $v ['customer']);
                $sheet->setCellValue("M{$i}", $v ['odate']);
                $sheet->setCellValue("N{$i}", $v ['repository']);
                $sheet->setCellValue("O{$i}", $v ['inventory']);
                if ($showprice) {
                    $sheet->setCellValue("P{$i}", $v ['expense']);
                    $sheet->setCellValue("Q{$i}", $v ['totalprice']);
                    $sheet->setCellValue("R{$i}", $v ['total']);
                    $sheet->setCellValue("S{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("O{$i}:R{$i}");
                    $sheet->setCellValue("S{$i}", $v ['memo']);
                }
                $sheet->setCellValue("T{$i}", $v ['state']);
                $sheet->setCellValue("U{$i}", $v ['creator']);
                $sheet->setCellValue("V{$i}", $v ['time']);
            }
        }

        $sheet->getStyle("A3:V{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryBorrow.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryRevertList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_REVERT)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_REVERT);

        $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_REVERT . ' AND del=false';

        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList(ResourceCls::INVENTORY_REVERT));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) ';
        }

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通归还列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:V{$i}");
        $sheet->setCellValue("A{$i}", '广通归还列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:J{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("K{$i}:V{$i}");
        $sheet->setCellValue("K{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("K{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:H{$i}");
            $sheet->setCellValue("I{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("J{$i}", '归还单号');
        $sheet->setCellValue("K{$i}", '归还者');
        $sheet->setCellValue("L{$i}", '归还客户');
        $sheet->setCellValue("M{$i}", '归还日期');
        $sheet->setCellValue("N{$i}", '仓库');
        $sheet->setCellValue("O{$i}", '借出单号');
        if ($showprice) {
            $sheet->setCellValue("P{$i}", '其他费用');
            $sheet->setCellValue("Q{$i}", '产品总价');
            $sheet->setCellValue("R{$i}", '合计');
            $sheet->setCellValue("S{$i}", '备注');
        } else {
            $sheet->mergeCells("O{$i}:R{$i}");
            $sheet->setCellValue("S{$i}", '备注');
        }
        $sheet->setCellValue("T{$i}", '状态');
        $sheet->setCellValue("U{$i}", '创建者');
        $sheet->setCellValue("V{$i}", '创建时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:V{$i}");

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                } else {
                    $sheet->mergeCells("F{$i}:H{$i}");
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("J{$i}", $v ['code']);
                $sheet->setCellValue("K{$i}", $v ['owner']);
                $sheet->setCellValue("L{$i}", $v ['customer']);
                $sheet->setCellValue("M{$i}", $v ['odate']);
                $sheet->setCellValue("N{$i}", $v ['repository']);
                $sheet->setCellValue("O{$i}", $v ['inventory']);
                if ($showprice) {
                    $sheet->setCellValue("P{$i}", $v ['expense']);
                    $sheet->setCellValue("Q{$i}", $v ['totalprice']);
                    $sheet->setCellValue("R{$i}", $v ['total']);
                    $sheet->setCellValue("S{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("O{$i}:R{$i}");
                    $sheet->setCellValue("S{$i}", $v ['memo']);
                }
                $sheet->setCellValue("T{$i}", $v ['state']);
                $sheet->setCellValue("U{$i}", $v ['creator']);
                $sheet->setCellValue("V{$i}", $v ['time']);
            }
        }

        $sheet->getStyle("A3:V{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryRevert.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryReturnList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_RETURN)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_RETURN);

        $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_RETURN . ' AND del=false';

        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList(ResourceCls::INVENTORY_RETURN));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) ';
        }

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通客户退货列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:AA{$i}");
        $sheet->setCellValue("A{$i}", '广通客户退货列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:AA{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:H{$i}");
            $sheet->setCellValue("I{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("J{$i}", '退货单号');
        $sheet->setCellValue("K{$i}", '负责人');
        $sheet->setCellValue("L{$i}", '退货客户');
        $sheet->setCellValue("M{$i}", '退货日期');
        $sheet->setCellValue("N{$i}", '仓库');
        $sheet->setCellValue("O{$i}", '采购单');
        $sheet->setCellValue("P{$i}", '出库单号');
        $sheet->setCellValue("Q{$i}", '合同');
        $sheet->setCellValue("R{$i}", '运输方式');
        if ($showprice) {
            $sheet->setCellValue("S{$i}", '运输费用');
            $sheet->setCellValue("T{$i}", '其他费用');
            $sheet->setCellValue("U{$i}", '产品总价');
            $sheet->setCellValue("V{$i}", '合计');
            $sheet->setCellValue("W{$i}", '备注');
        } else {
            $sheet->mergeCells("R{$i}:V{$i}");
            $sheet->setCellValue("W{$i}", '备注');
        }
        $sheet->setCellValue("X{$i}", '状态');
        $sheet->setCellValue("Y{$i}", '创建者');
        $sheet->setCellValue("Z{$i}", '创建时间');
        $sheet->setCellValue("AA{$i}", '新开拓');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:AA{$i}");

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                } else {
                    $sheet->mergeCells("F{$i}:H{$i}");
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("J{$i}", $v ['code']);
                $sheet->setCellValue("K{$i}", $v ['owner']);
                $sheet->setCellValue("L{$i}", $v ['customer']);
                $sheet->setCellValue("M{$i}", $v ['odate']);
                $sheet->setCellValue("N{$i}", $v ['repository']);
                $sheet->setCellValue("O{$i}", $v ['purchase']);
                $sheet->setCellValue("P{$i}", $v ['inventory']);
                $sheet->setCellValue("Q{$i}", $v ['contract']);
                $sheet->setCellValue("R{$i}", $v ['traffic']);
                if ($showprice) {
                    $sheet->setCellValue("S{$i}", $v ['carriage']);
                    $sheet->setCellValue("T{$i}", $v ['expense']);
                    $sheet->setCellValue("U{$i}", $v ['totalprice']);
                    $sheet->setCellValue("V{$i}", $v ['total']);
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("R{$i}:V{$i}");
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                }
                $sheet->setCellValue("X{$i}", $v ['state']);
                $sheet->setCellValue("Y{$i}", $v ['creator']);
                $sheet->setCellValue("Z{$i}", $v ['time']);
                $sheet->setCellValue("AA{$i}", $v ['openup']);
            }
        }

        $sheet->getStyle("A3:AA{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryReturn.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventorySendbackList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_SENDBACK)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_SENDBACK);

        $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_SENDBACK . ' AND del=false';

        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList(ResourceCls::INVENTORY_SENDBACK));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) ';
        }

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通退回供应商列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:Z{$i}");
        $sheet->setCellValue("A{$i}", '广通退回供应商列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:Z{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:H{$i}");
            $sheet->setCellValue("I{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("J{$i}", '退回单号');
        $sheet->setCellValue("K{$i}", '负责人');
        $sheet->setCellValue("L{$i}", '退回供应商');
        $sheet->setCellValue("M{$i}", '退回日期');
        $sheet->setCellValue("N{$i}", '仓库');
        $sheet->setCellValue("O{$i}", '采购单');
        $sheet->setCellValue("P{$i}", '入库单号');
        $sheet->setCellValue("Q{$i}", '合同');
        $sheet->setCellValue("R{$i}", '运输方式');
        if ($showprice) {
            $sheet->setCellValue("S{$i}", '运输费用');
            $sheet->setCellValue("T{$i}", '其他费用');
            $sheet->setCellValue("U{$i}", '产品总价');
            $sheet->setCellValue("V{$i}", '合计');
            $sheet->setCellValue("W{$i}", '备注');
        } else {
            $sheet->mergeCells("R{$i}:V{$i}");
            $sheet->setCellValue("W{$i}", '备注');
        }
        $sheet->setCellValue("X{$i}", '状态');
        $sheet->setCellValue("Y{$i}", '创建者');
        $sheet->setCellValue("Z{$i}", '创建时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:Z{$i}");

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                } else {
                    $sheet->mergeCells("F{$i}:H{$i}");
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("J{$i}", $v ['code']);
                $sheet->setCellValue("K{$i}", $v ['owner']);
                $sheet->setCellValue("L{$i}", $v ['customer']);
                $sheet->setCellValue("M{$i}", $v ['odate']);
                $sheet->setCellValue("N{$i}", $v ['repository']);
                $sheet->setCellValue("O{$i}", $v ['purchase']);
                $sheet->setCellValue("P{$i}", $v ['inventory']);
                $sheet->setCellValue("Q{$i}", $v ['contract']);
                $sheet->setCellValue("R{$i}", $v ['traffic']);
                if ($showprice) {
                    $sheet->setCellValue("S{$i}", $v ['carriage']);
                    $sheet->setCellValue("T{$i}", $v ['expense']);
                    $sheet->setCellValue("U{$i}", $v ['totalprice']);
                    $sheet->setCellValue("V{$i}", $v ['total']);
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("R{$i}:V{$i}");
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                }
                $sheet->setCellValue("X{$i}", $v ['state']);
                $sheet->setCellValue("Y{$i}", $v ['creator']);
                $sheet->setCellValue("Z{$i}", $v ['time']);
            }
        }

        $sheet->getStyle("A3:Z{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventorySendback.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryCastoffList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_CASTOFF)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_CASTOFF);

        $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_CASTOFF . ' AND del=false';

        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList(ResourceCls::INVENTORY_CASTOFF));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) ';
        }

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通报废列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:Z{$i}");
        $sheet->setCellValue("A{$i}", '广通报废列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:Z{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:H{$i}");
            $sheet->setCellValue("I{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("J{$i}", '报废单号');
        $sheet->setCellValue("K{$i}", '负责人');
        $sheet->setCellValue("L{$i}", '报废客户');
        $sheet->setCellValue("M{$i}", '报废日期');
        $sheet->setCellValue("N{$i}", '仓库');
        $sheet->setCellValue("O{$i}", '采购单');
        $sheet->setCellValue("P{$i}", '入库单号');
        $sheet->setCellValue("Q{$i}", '合同');
        $sheet->setCellValue("R{$i}", '运输方式');
        if ($showprice) {
            $sheet->setCellValue("S{$i}", '运输费用');
            $sheet->setCellValue("T{$i}", '其他费用');
            $sheet->setCellValue("U{$i}", '产品总价');
            $sheet->setCellValue("V{$i}", '合计');
            $sheet->setCellValue("W{$i}", '备注');
        } else {
            $sheet->mergeCells("R{$i}:V{$i}");
            $sheet->setCellValue("W{$i}", '备注');
        }
        $sheet->setCellValue("X{$i}", '状态');
        $sheet->setCellValue("Y{$i}", '创建者');
        $sheet->setCellValue("Z{$i}", '创建时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:Z{$i}");

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                } else {
                    $sheet->mergeCells("F{$i}:H{$i}");
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("J{$i}", $v ['code']);
                $sheet->setCellValue("K{$i}", $v ['owner']);
                $sheet->setCellValue("L{$i}", $v ['customer']);
                $sheet->setCellValue("M{$i}", $v ['odate']);
                $sheet->setCellValue("N{$i}", $v ['repository']);
                $sheet->setCellValue("O{$i}", $v ['purchase']);
                $sheet->setCellValue("P{$i}", $v ['inventory']);
                $sheet->setCellValue("Q{$i}", $v ['contract']);
                $sheet->setCellValue("R{$i}", $v ['traffic']);
                if ($showprice) {
                    $sheet->setCellValue("S{$i}", $v ['carriage']);
                    $sheet->setCellValue("T{$i}", $v ['expense']);
                    $sheet->setCellValue("U{$i}", $v ['totalprice']);
                    $sheet->setCellValue("V{$i}", $v ['total']);
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("R{$i}:V{$i}");
                    $sheet->setCellValue("W{$i}", $v ['memo']);
                }
                $sheet->setCellValue("X{$i}", $v ['state']);
                $sheet->setCellValue("Y{$i}", $v ['creator']);
                $sheet->setCellValue("Z{$i}", $v ['time']);
            }
        }

        $sheet->getStyle("A3:Z{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryCastoff.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryMoveList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_MOVE)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_MOVE);

        $where = 'WHERE type=' . InventoryCls::INVENTORY_TYPE_MOVE . ' AND del=false';

        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList(ResourceCls::INVENTORY_MOVE));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) ';
        }

        $order = 'ORDER BY time DESC';

        $rs = InventoryCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通移库列表');

        $i = $j = 1;

        $sheet->mergeCells("A{$i}:AA{$i}");
        $sheet->setCellValue("A{$i}", '广通移库列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:AA{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        // product list
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '合计');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("F{$i}:H{$i}");
            $sheet->setCellValue("I{$i}", '备注');
        }
        // inventory
        $sheet->setCellValue("J{$i}", '移库单号');
        $sheet->setCellValue("K{$i}", '申请者');
        $sheet->setCellValue("L{$i}", '移出仓库');
        $sheet->setCellValue("M{$i}", '移入仓库');
        $sheet->setCellValue("N{$i}", '移库日期');
        $sheet->setCellValue("O{$i}", '主移库单');
        $sheet->setCellValue("P{$i}", '移库客户');
        $sheet->setCellValue("Q{$i}", '采购单');
        $sheet->setCellValue("R{$i}", '合同');
        $sheet->setCellValue("S{$i}", '运输方式');
        if ($showprice) {
            $sheet->setCellValue("T{$i}", '运输费用');
            $sheet->setCellValue("U{$i}", '其他费用');
            $sheet->setCellValue("V{$i}", '移出总价');
            $sheet->setCellValue("W{$i}", '移入总价');
            $sheet->setCellValue("X{$i}", '备注');
        } else {
            $sheet->mergeCells("S{$i}:W{$i}");
            $sheet->setCellValue("X{$i}", '备注');
        }
        $sheet->setCellValue("Y{$i}", '状态');
        $sheet->setCellValue("Z{$i}", '创建者');
        $sheet->setCellValue("AA{$i}", '创建时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:AA{$i}");

        foreach ($rs as $v) {
            // product list
            $rx = InventoryProductCls::getProductList($v ['id']);

            foreach ($rx as $n) {
                $i++;
                $sheet->setCellValue("A{$i}", $n ['productsku']);
                $sheet->setCellValue("B{$i}", $n ['product']);
                $sheet->setCellValue("C{$i}", $n ['productrule']);
                $sheet->setCellValue("D{$i}", $n ['productunit']);
                $sheet->setCellValue("E{$i}", $n ['repository']);
                $sheet->setCellValue("F{$i}", $n ['number']);
                if ($showprice) {
                    $sheet->setCellValue("G{$i}", $n ['price']);
                    $sheet->setCellValue("H{$i}", $n ['total']);
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                } else {
                    $sheet->mergeCells("F{$i}:H{$i}");
                    $sheet->setCellValue("I{$i}", $n ['memo']);
                }

                // inventory
                $sheet->setCellValue("J{$i}", $v ['code']);
                $sheet->setCellValue("K{$i}", $v ['owner']);
                $sheet->setCellValue("L{$i}", $v ['repository']);
                $sheet->setCellValue("M{$i}", $v ['dstrepository']);
                $sheet->setCellValue("N{$i}", $v ['odate']);
                $sheet->setCellValue("O{$i}", $v ['inventory']);
                $sheet->setCellValue("P{$i}", $v ['customer']);
                $sheet->setCellValue("Q{$i}", $v ['purchase']);
                $sheet->setCellValue("R{$i}", $v ['contract']);
                $sheet->setCellValue("S{$i}", $v ['traffic']);
                if ($showprice) {
                    $sheet->setCellValue("T{$i}", $v ['carriage']);
                    $sheet->setCellValue("U{$i}", $v ['expense']);
                    $sheet->setCellValue("V{$i}", $v ['totalprice']);
                    $sheet->setCellValue("W{$i}", $v ['dsttotalprice']);
                    $sheet->setCellValue("X{$i}", $v ['memo']);
                } else {
                    $sheet->mergeCells("S{$i}:W{$i}");
                    $sheet->setCellValue("X{$i}", $v ['memo']);
                }
                $sheet->setCellValue("Y{$i}", $v ['state']);
                $sheet->setCellValue("Z{$i}", $v ['creator']);
                $sheet->setCellValue("AA{$i}", $v ['time']);
            }
        }

        $sheet->getStyle("A3:AA{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryMove.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryInitList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_INIT)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $showprice = PermissionCls::Instance($this->UID())->isCanPrice(ResourceCls::INVENTORY_INIT);

        $where = 'WHERE del=false';

        $order = 'ORDER BY productid, time DESC';

        $inventoryid = InventoryCls::getInitInventoryID();

        if (!empty ($inventoryid)) {
            $where .= ' AND inventoryid=\'' . $inventoryid . '\'::UUID';

            $rs = InventoryProductCls::queryList($where, $order, 0, 0);
        } else {
            $rs = array();
        }

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通初始化列表');

        $i = 1;

        $sheet->mergeCells("A{$i}:K{$i}");
        $sheet->setCellValue("A{$i}", '广通初始化列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:I{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("J{$i}:K{$i}");
        $sheet->setCellValue("J{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("J{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '单位');
        $sheet->setCellValue("D{$i}", '规格');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '数量');
        if ($showprice) {
            $sheet->setCellValue("G{$i}", '单价');
            $sheet->setCellValue("H{$i}", '总价');
            $sheet->setCellValue("I{$i}", '备注');
        } else {
            $sheet->mergeCells("G{$i}:I{$i}");
            $sheet->setCellValue("G{$i}", '备注');
        }
        $sheet->setCellValue("J{$i}", '创建者');
        $sheet->setCellValue("K{$i}", '创建时间');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:K{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['productsku']);
            $sheet->setCellValue("B{$i}", $v ['product']);
            $sheet->setCellValue("C{$i}", $v ['productunit']);
            $sheet->setCellValue("D{$i}", $v ['productrule']);
            $sheet->setCellValue("E{$i}", $v ['repository']);
            $sheet->setCellValue("F{$i}", $v ['number']);
            if ($showprice) {
                $sheet->setCellValue("G{$i}", $v ['price']);
                $sheet->setCellValue("H{$i}", $v ['total']);
                $sheet->setCellValue("I{$i}", $v ['memo']);
            } else {
                $sheet->mergeCells("G{$i}:I{$i}");
                $sheet->setCellValue("G{$i}", $v ['memo']);
            }
            $sheet->setCellValue("J{$i}", $v ['creator']);
            $sheet->setCellValue("K{$i}", $v ['time']);
        }

        $sheet->getStyle("A3:K{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryInit.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryExpireDayList()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_TRACK)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = trim(Url::Req('findname', '', 'str'));
        $findrepositoryid = UUID::Check(Url::Req('findrepositoryid', '', 'str'));
        $findexpiredayfrom = trim(Url::Req('findexpiredayfrom', '', 'str'));
        $findexpiredayto = trim(Url::Req('findexpiredayto', '', 'str'));
        $findoverdayfrom = trim(Url::Req('findoverdayfrom', '', 'str'));
        $findoverdayto = trim(Url::Req('findoverdayto', '', 'str'));
        $findavaliablefrom = trim(Url::Req('findavaliablefrom', '', 'str'));
        $findavaliableto = trim(Url::Req('findavaliableto', '', 'str'));

        $avaliable = "(pin-pout-borrow+revert-castoff+return-sendback+movein-moveout+init-lock)";
        $where = 'WHERE del=false';

        if (!empty ($findname)) {
            $keys = ProductCls::FindLike($findname);
            $keys = array_merge($keys, ProductCls::FindLike($findname, 'code'));
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND productid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findrepositoryid)) {
            $where .= ' AND repositoryid=\'' . $findrepositoryid . '\'::UUID';
        }
        if (!Util::IsPrice($findexpiredayfrom)) $findexpiredayfrom = '';
        if (!Util::IsPrice($findexpiredayto)) $findexpiredayto = '';
        if (empty ($findexpiredayfrom) && !empty ($findexpiredayto)) {
            $findexpiredayfrom = $findexpiredayto;
            $findexpiredayto = '';
        }
        if (!empty ($findexpiredayfrom) && !empty ($findexpiredayto)) {
            if ($findexpiredayto < $findexpiredayfrom) list ($findexpiredayfrom, $findexpiredayto) = array($findexpiredayto, $findexpiredayfrom);
            $where .= " AND expireday>='" . $findexpiredayfrom . "' AND expireday<='" . $findexpiredayto . "'";
        } elseif (!empty ($findexpiredayfrom)) {
            $where .= " AND expireday='" . $findexpiredayfrom . "'";
        }
        if (!Util::IsPrice($findoverdayfrom)) $findoverdayfrom = '';
        if (!Util::IsPrice($findoverdayto)) $findoverdayto = '';
        if (empty ($findoverdayfrom) && !empty ($findoverdayto)) {
            $findoverdayfrom = $findoverdayto;
            $findoverdayto = '';
        }
        if (!empty ($findoverdayfrom) && !empty ($findoverdayto)) {
            if ($findoverdayto < $findoverdayfrom) list ($findoverdayfrom, $findoverdayto) = array($findoverdayto, $findoverdayfrom);
            $where .= " AND DATE_PART('day', (CURRENT_TIMESTAMP-last))-expireday>='" . $findoverdayfrom . "' AND DATE_PART('day', (CURRENT_TIMESTAMP-last))-expireday<='" . $findoverdayto . "'";
        } elseif (!empty ($findoverdayfrom)) {
            $where .= " AND DATE_PART('day', (CURRENT_TIMESTAMP-last))-expireday='" . $findoverdayfrom . "'";
        }
        if (!Util::IsPrice($findavaliablefrom)) $findavaliablefrom = '';
        if (!Util::IsPrice($findavaliableto)) $findavaliableto = '';
        if (empty ($findavaliablefrom) && !empty ($findavaliableto)) {
            $findavaliablefrom = $findavaliableto;
            $findavaliableto = '';
        }
        if (!empty ($findavaliablefrom) && !empty ($findavaliableto)) {
            if ($findavaliableto < $findavaliablefrom) list ($findavaliablefrom, $findavaliableto) = array($findavaliableto, $findavaliablefrom);
            $where .= " AND " . $avaliable . ">='" . $findavaliablefrom . "' AND " . $avaliable . "<='" . $findavaliableto . "'";
        } elseif (!empty ($findavaliablefrom)) {
            $where .= " AND " . $avaliable . "='" . $findavaliablefrom . "'";
        }
        // reality
        $where .= " AND " . $avaliable . "<>0 AND expireday>0 AND DATE_PART('day', (CURRENT_TIMESTAMP-last))>expireday";
        $order = 'ORDER BY productid, time DESC';

        $showcost = PermissionCls::Instance($this->UID())->isCanCost(ResourceCls::INVENTORY_TRACK);

        $rs = InventoryTrackCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('库存量过期');

        $i = 1;

        $sheet->mergeCells("A{$i}:J{$i}");
        $sheet->setCellValue("A{$i}", '库存量过期');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:E{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("F{$i}:J{$i}");
        $sheet->setCellValue("F{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '产品代码');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '规格');
        $sheet->setCellValue("D{$i}", '单位');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '库存量');
        $sheet->setCellValue("G{$i}", '过期天数');
        $sheet->setCellValue("H{$i}", '最后时间');
        $sheet->setCellValue("I{$i}", '初始时间');
        $sheet->setCellValue("J{$i}", '过期预警');
        if ($showcost) {
            $sheet->setCellValue("K{$i}", '成本');
            $sheet->setCellValue("L{$i}", '库存总价');
        }

        $this->excelSetSubTitleStyle($sheet, "A{$i}:J{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['productsku']);
            $sheet->setCellValue("B{$i}", $v ['product']);
            $sheet->setCellValue("C{$i}", $v ['productrule']);
            $sheet->setCellValue("D{$i}", $v ['productunit']);
            $sheet->setCellValue("E{$i}", $v ['repository']);
            $sheet->setCellValue("F{$i}", Format::NumberString($v ['available']));
            $sheet->setCellValue("G{$i}", Date::DiffDay(Date::Datetime(), $v ['last']) - $v ['expireday']);
            $sheet->setCellValue("H{$i}", Format::DateString($v ['last']));
            $sheet->setCellValue("I{$i}", Format::DateString($v ['time']));
            $sheet->setCellValue("J{$i}", $v ['expireday']);
            if ($showcost) {
                $sheet->setCellValue("K{$i}", Format::PriceString($v ['cost']));
                $sheet->setCellValue("L{$i}", Format::PriceString($v ['totalcost']));
            }
        }

        $sheet->getStyle("A3:J{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "InventoryExpireDayList.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryReportProductProfitFind()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_REPORT_SUMMARY)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $where = '';
        $order = 'ORDER BY i.approvelast ASC';

        $findcatalogid = UUID::Check(Url::Req('findcatalogid', '', 'str'));
        $findcustomerid = UUID::Check(Url::Req('findcustomerid', '', 'str'));
        $findcustomername = trim(Url::Req('findcustomername', '', 'str'));
        $finddepartmentid = UUID::Check(Url::Req('finddepartmentid', '', 'str'));
        $findownerid = UUID::Check(Url::Req('findownerid', '', 'str'));
        $findproductid = UUID::Check(Url::Req('findproductid', '', 'str'));
        $findrepositoryid = UUID::Check(Url::Req('findrepositoryid', '', 'str'));
        $findfromdate = Date::Format(Url::Req('findfromdate', '', 'str'), 'd', '');
        $findtodate = Date::Format(Url::Req('findtodate', '', 'str'), 'd', '');
        $findcurrencyid = UUID::Check(Url::Req('findcurrencyid', '', 'str'));
        $findowner2 = trim(Url::Req('findowner2', '', 'str'));
        $findtax = Url::Req('findtax', 0, 'int');

        if (!empty ($findrepositoryid)) {
            $where .= ' AND p.repositoryid=\'' . $findrepositoryid . '\'::UUID';
        }
        if (!empty ($findproductid)) {
            $where .= ' AND p.productid=\'' . $findproductid . '\'::UUID';
        }
        if (!empty ($findcatalogid)) {
            $keys = ProductCatalogCls::Instance()->GetChildren($findcatalogid);
            $keys = ProductCls::GetIDListByCatalogIDList($keys);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND p.productid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($finddepartmentid)) {
            $keys = DepartmentCls::Instance()->GetChildren($finddepartmentid);
            $keys = UserCls::Instance()->getIDListByDepartmentIDList($keys);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND i.ownerid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findownerid)) {
            $where .= ' AND i.ownerid=\'' . $findownerid . '\'::UUID';
        }
        if (!empty ($findcustomerid)) {
            $where .= ' AND i.customerid=\'' . $findcustomerid . '\'::UUID';
        }
        if (!empty ($findcustomername)) {
            $keys = CustomerCls::FindLike($findcustomername);

            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND i.customerid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findfromdate) && !empty ($findtodate)) {
            $where .= " AND TO_CHAR(i.approvelast ,'YYYY-MM-DD')>='" . $findfromdate . "' AND TO_CHAR(i.approvelast, 'YYYY-MM-DD')<='" . $findtodate . "'";
            $num = $lastnumber = InventoryProductCls::getProductInventoryNumber($findproductid, $findrepositoryid, " AND TO_CHAR(i.approvelast ,'YYYY-MM-DD')<'" . $findfromdate . "'");
        } elseif (!empty ($findfromdate)) {
            $where .= " AND TO_CHAR(i.approvelast, 'YYYY-MM-DD')>='" . $findfromdate . "'";
            $num = $lastnumber = InventoryProductCls::getProductInventoryNumber($findproductid, $findrepositoryid, " AND TO_CHAR(i.approvelast ,'YYYY-MM-DD')<'" . $findfromdate . "'");
        } elseif (!empty ($findtodate)) {
            $where .= " AND TO_CHAR(i.approvelast, 'YYYY-MM-DD')<='" . $findtodate . "'";
        }
        if (!empty ($findcurrencyid)) {
            $where .= " AND i.currencyid='" . $findcurrencyid . "'";
        }
        if (!empty ($findowner2)) {
            $keys = UserCls::FindLike($findowner2);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND i.ownerid IN (\'' . $keys . '\') ';
            }
        }
        if ($findtax > 0) {
            if ($findtax == 1) {
                $where .= ' AND i.tax=true';
            } else {
                $where .= ' AND i.tax=false';
            }
        }

        $rs = InventoryProductCls::queryProfitFlowList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $localcurrency = CurrencyCls::Instance()->getLocalCurrencyName();
        $localtotalprice = $localtotalcost = $localtotalprofit = 0;

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通产品利润');

        $i = 1;

        $sheet->mergeCells("A{$i}:U{$i}");
        $sheet->setCellValue("A{$i}", '广通产品利润');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:U{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '审批日期');
        $sheet->setCellValue("B{$i}", '录入日期');
        $sheet->setCellValue("C{$i}", '出库单号');
        $sheet->setCellValue("D{$i}", '产品');
        $sheet->setCellValue("E{$i}", '仓库');
        $sheet->setCellValue("F{$i}", '客户');
        $sheet->setCellValue("G{$i}", '负责人');
        $sheet->setCellValue("H{$i}", '数量');
        $sheet->setCellValue("I{$i}", '单价');
        $sheet->setCellValue("J{$i}", '金额');
        $sheet->setCellValue("K{$i}", '成本');
        $sheet->setCellValue("L{$i}", '利润');
        $sheet->setCellValue("M{$i}", '结算');
        $sheet->setCellValue("N{$i}", '汇率');
        $sheet->setCellValue("O{$i}", '含税');
        $sheet->setCellValue("P{$i}", '特批');
        $sheet->setCellValue("Q{$i}", "金额({$localcurrency})");
        $sheet->setCellValue("R{$i}", "成本({$localcurrency})");
        $sheet->setCellValue("S{$i}", "利润({$localcurrency})");
        $sheet->setCellValue("T{$i}", '产品分类');
        $sheet->setCellValue("U{$i}", '产品一级分类');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:U{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['approvelast']);
            $sheet->setCellValue("B{$i}", $v ['time']);
            $sheet->setCellValue("C{$i}", $v ['code']);
            $sheet->setCellValue("D{$i}", $v ['product']);
            $sheet->setCellValue("E{$i}", $v ['repository']);
            $sheet->setCellValue("F{$i}", $v ['customer']);
            $sheet->setCellValue("G{$i}", $v ['owner']);
            $sheet->setCellValue("H{$i}", Format::NumberString($v ['number']));
            $sheet->setCellValue("I{$i}", Format::PriceString($v ['price']));
            $sheet->setCellValue("J{$i}", Format::PriceString($v ['total']));
            $sheet->setCellValue("K{$i}", Format::PriceString($v ['totalcost']));
            $sheet->setCellValue("L{$i}", Format::PriceString($v ['totalprofit']));
            $sheet->setCellValue("M{$i}", $v ['currency']);
            $sheet->setCellValue("N{$i}", Format::PriceString($v ['currencyrate']));
            $sheet->setCellValue("O{$i}", Format::BoolString($v ['tax']));
            $sheet->setCellValue("P{$i}", Format::BoolString($v ['special']));
            $sheet->setCellValue("Q{$i}", Format::PriceString($v ['total'] * $v ['currencyrate']));
            $sheet->setCellValue("R{$i}", Format::PriceString($v ['totalcost'] * $v ['currencyrate']));
            $sheet->setCellValue("S{$i}", Format::PriceString($v ['totalprofit'] * $v ['currencyrate']));
            $sheet->setCellValue("T{$i}", $v ['productcatalog']);
            $sheet->setCellValue("U{$i}", ProductCatalogCls::Instance()->GetLevel1Name($v ['productcatalogid']));

            $localtotalprice += ($v ['total'] * $v ['currencyrate']);
            $localtotalcost += ($v ['totalcost'] * $v ['currencyrate']);
            $localtotalprofit += ($v ['totalprofit'] * $v ['currencyrate']);
        }

        $sheet->getStyle("A3:U{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $i++;
        $sheet->setCellValue("A{$i}", '总计金额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalprice));
        $sheet->setCellValue("C{$i}", $localcurrency);
        $i++;
        $sheet->setCellValue("A{$i}", '总计成本：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalcost));
        $sheet->setCellValue("C{$i}", $localcurrency);
        $i++;
        $sheet->setCellValue("A{$i}", '总计利润：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalprofit));
        $sheet->setCellValue("C{$i}", $localcurrency);

        $filename = "InventoryReportInventoryProfitFind.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryReportInventoryProfitFind()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_REPORT_SUMMARY)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findtype = Url::Req('findtype', 0, 'int');
        $findcode = trim(Url::Req('findcode', '', 'str'));
        $findcustomerid = UUID::Check(Url::Req('findcustomerid', '', 'str'));
        $findcustomername = trim(Url::Req('findcustomername', '', 'str'));
        $finddepartmentid = UUID::Check(Url::Req('finddepartmentid', '', 'str'));
        $findownerid = UUID::Check(Url::Req('findownerid', '', 'str'));
        $findrepositoryid = UUID::Check(Url::Req('findrepositoryid', '', 'str'));
        $findfromdate = Date::Format(Url::Req('findfromdate', '', 'str'), 'd', '');
        $findtodate = Date::Format(Url::Req('findtodate', '', 'str'), 'd', '');
        $findcurrencyid = UUID::Check(Url::Req('findcurrencyid', '', 'str'));
        $findowner2 = trim(Url::Req('findowner2', '', 'str'));
        $findtax = Url::Req('findtax', 0, 'int');

        $where = '';
        $order = 'ORDER BY i.approvelast ASC';

        if (!empty ($findrepositoryid)) {
            $where .= ' AND i.repositoryid=\'' . $findrepositoryid . '\'::UUID';
        }
        if (!empty ($findtype)) {
            $where .= ' AND i.type=' . $findtype;
        }
        if (!empty ($findcode)) {
            $where .= ' AND i.code LIKE \'%' . strtoupper($findcode) . '%\' ';
        }
        if (!empty ($finddepartmentid)) {
            $keys = DepartmentCls::Instance()->GetChildren($finddepartmentid);
            $keys = UserCls::Instance()->getIDListByDepartmentIDList($keys);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND i.ownerid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findownerid)) {
            $where .= ' AND i.ownerid=\'' . $findownerid . '\'::UUID';
        }
        if (!empty ($findcustomerid)) {
            $where .= ' AND i.customerid=\'' . $findcustomerid . '\'::UUID';
        }
        if (!empty ($findcustomername)) {
            $keys = CustomerCls::FindLike($findcustomername);

            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND i.customerid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findfromdate) && !empty ($findtodate)) {
            $where .= " AND TO_CHAR(i.approvelast ,'YYYY-MM-DD')>='" . $findfromdate . "' AND TO_CHAR(i.approvelast, 'YYYY-MM-DD')<='" . $findtodate . "'";
        } elseif (!empty ($findfromdate)) {
            $where .= " AND TO_CHAR(i.approvelast, 'YYYY-MM-DD')>='" . $findfromdate . "'";
        } elseif (!empty ($findtodate)) {
            $where .= " AND TO_CHAR(i.approvelast, 'YYYY-MM-DD')<='" . $findtodate . "'";
        }
        if (!empty ($findcurrencyid)) {
            $where .= " AND i.currencyid='" . $findcurrencyid . "'";
        }
        if (!empty ($findowner2)) {
            $keys = UserCls::FindLike($findowner2);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND i.ownerid IN (\'' . $keys . '\') ';
            }
        }
        if ($findtax > 0) {
            if ($findtax == 1) {
                $where .= ' AND i.tax=true';
            } else {
                $where .= ' AND i.tax=false';
            }
        }

        $rs = InventoryCls::queryProfitFlowList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $localcurrency = CurrencyCls::Instance()->getLocalCurrencyName();
        $localalltotalprice = $localtotalprice = $localtotalcarriage = $localtotalexpense = $localtotalcost = $localtotalprofit = 0;

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通单据利润');

        $i = 1;

        $sheet->mergeCells("A{$i}:W{$i}");
        $sheet->setCellValue("A{$i}", '广通单据利润');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:M{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("N{$i}:W{$i}");
        $sheet->setCellValue("N{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("N{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;

        $sheet->setCellValue("A{$i}", '类型');
        $sheet->setCellValue("B{$i}", '库存单号');
        $sheet->setCellValue("C{$i}", '审批日期');
        $sheet->setCellValue("D{$i}", '仓库');
        $sheet->setCellValue("E{$i}", '客户');
        $sheet->setCellValue("F{$i}", '负责人');
        $sheet->setCellValue("G{$i}", '单据总价');
        $sheet->setCellValue("H{$i}", '产品总价');
        $sheet->setCellValue("I{$i}", '运费');
        $sheet->setCellValue("J{$i}", '其他费用');
        $sheet->setCellValue("K{$i}", '产品成本');
        $sheet->setCellValue("L{$i}", '产品利润');
        $sheet->setCellValue("M{$i}", '结算');
        $sheet->setCellValue("N{$i}", '汇率');
        $sheet->setCellValue("O{$i}", '含税');
        $sheet->setCellValue("P{$i}", '特批');
        $sheet->setCellValue("Q{$i}", "单据总价({$localcurrency})");
        $sheet->setCellValue("R{$i}", "产品总价({$localcurrency})");
        $sheet->setCellValue("S{$i}", "运费({$localcurrency})");
        $sheet->setCellValue("T{$i}", "其他费用({$localcurrency})");
        $sheet->setCellValue("U{$i}", "产品成本({$localcurrency})");
        $sheet->setCellValue("V{$i}", "产品利润({$localcurrency})");
        $sheet->setCellValue("W{$i}", "新开拓");

        $this->excelSetSubTitleStyle($sheet, "A{$i}:V{$i}");

        foreach ($rs as $v) {
            $i++;

            $sheet->setCellValue("A{$i}", $v ['typename']);
            $sheet->setCellValue("B{$i}", $v ['code']);
            $sheet->setCellValue("C{$i}", Format::DateString($v ['approvelast']));
            $sheet->setCellValue("D{$i}", $v ['repository']);
            $sheet->setCellValue("E{$i}", $v ['customer']);
            $sheet->setCellValue("F{$i}", $v ['owner']);
            $sheet->setCellValue("G{$i}", Format::PriceString($v ['alltotalprice']));
            $sheet->setCellValue("H{$i}", Format::PriceString($v ['totalprice']));
            $sheet->setCellValue("I{$i}", Format::PriceString($v ['carriage']));
            $sheet->setCellValue("J{$i}", Format::PriceString($v ['expense']));
            $sheet->setCellValue("K{$i}", Format::PriceString($v ['ptotalcost']));
            $sheet->setCellValue("L{$i}", Format::PriceString($v ['ptotalprofit']));
            $sheet->setCellValue("M{$i}", $v ['currency']);
            $sheet->setCellValue("N{$i}", Format::PriceString($v ['currencyrate']));
            $sheet->setCellValue("O{$i}", Format::BoolString($v ['tax']));
            $sheet->setCellValue("P{$i}", Format::BoolString($v ['special']));
            $sheet->setCellValue("Q{$i}", Format::PriceString($v ['currencyalltotalprice']));
            $sheet->setCellValue("R{$i}", Format::PriceString($v ['currencytotalprice']));
            $sheet->setCellValue("S{$i}", Format::PriceString($v ['currencycarriage']));
            $sheet->setCellValue("T{$i}", Format::PriceString($v ['currencyexpense']));
            $sheet->setCellValue("U{$i}", Format::PriceString($v ['currencyptotalcost']));
            $sheet->setCellValue("V{$i}", Format::PriceString($v ['currencyptotalprofit']));
            $sheet->setCellValue("W{$i}", Format::BoolString($v ['openup']));

            $localalltotalprice += $v ['currencyalltotalprice'];
            $localtotalprice += $v ['currencytotalprice'];
            $localtotalcarriage += abs($v ['currencycarriage']);
            $localtotalexpense += abs($v ['currencyexpense']);
            $localtotalcost += $v ['currencyptotalcost'];
            $localtotalprofit += $v ['currencyptotalprofit'];
        }

        $sheet->getStyle("A3:W{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $i++;
        $sheet->setCellValue("A{$i}", '总计总价：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localalltotalprice));
        $sheet->setCellValue("C{$i}", $localcurrency);
        $i++;
        $sheet->setCellValue("A{$i}", '总计产品：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalprice));
        $sheet->setCellValue("C{$i}", $localcurrency);
        $i++;
        $sheet->setCellValue("A{$i}", '总计成本：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalcost));
        $sheet->setCellValue("C{$i}", $localcurrency);
        $i++;
        $sheet->setCellValue("A{$i}", '总计利润：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalprofit));
        $sheet->setCellValue("C{$i}", $localcurrency);
        $i++;
        $sheet->setCellValue("A{$i}", '总计运费：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalcarriage));
        $sheet->setCellValue("C{$i}", $localcurrency);
        $i++;
        $sheet->setCellValue("A{$i}", '总计其他：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalexpense));
        $sheet->setCellValue("C{$i}", $localcurrency);

        $filename = "InventoryReportInventoryProfitFind.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelInventoryReportHistoryFind()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_REPORT_SUMMARY)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = Url::Req('findname', '', 'str');
        $findrepositoryid = UUID::Check(Url::Req('findrepositoryid', '', 'str'));
        $findfromnumber = trim(Url::Req('findfromnumber', '', 'str'));
        $findtonumber = trim(Url::Req('findtonumber', '', 'str'));
        $findfromprice = trim(Url::Req('findfromprice', '', 'str'));
        $findtoprice = trim(Url::Req('findtoprice', '', 'str'));
        $findfromtotal = trim(Url::Req('findfromtotal', '', 'str'));
        $findtototal = trim(Url::Req('findtototal', '', 'str'));
        $finddate = Date::Format(Url::Req('finddate', '', 'str'), 'd', '');
        $findcatalogid = UUID::Check(Url::Req('findcatalogid', '', 'str'));
        $finddepartmentid = UUID::Check(Url::Req('finddepartmentid', '', 'str'));

        $orderkey = Url::Req('orderkey', '', 'str');
        $orderval = Url::Req('orderval', 0, 'int');

        $date = '';
        $where = 'WHERE 1=1';

        if (!empty ($finddate)) {
            $date .= "WHERE DATE_TRUNC('day', time)<='" . $finddate . "'";
        }

        if (!empty ($findname)) {
            $keys = ProductCls::FindLike($findname);
            $keys = array_merge($keys, ProductCls::FindLike($findname, 'code'));
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND productid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($finddepartmentid)) {
            $keys = RepositoryCls::Instance()->GetReposioryIDListByDepartmentID($finddepartmentid);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND repositoryid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findrepositoryid)) {
            $where .= ' AND repositoryid=\'' . $findrepositoryid . '\'::UUID';
        }
        if (!empty ($findcatalogid)) {
            $keys = ProductCatalogCls::Instance()->GetChildren($findcatalogid);
            $keys = ProductCls::GetIDListByCatalogIDList($keys);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND productid IN (\'' . $keys . '\') ';
            }
        }
        if (!Util::IsPrice($findfromnumber)) $findfromnumber = '';
        if (!Util::IsPrice($findtonumber)) $findtonumber = '';
        if (empty ($findfromnumber) && !empty ($findtonumber)) {
            $findfromnumber = $findtonumber;
            $findtonumber = '';
        }
        if (!empty ($findfromnumber) && !empty ($findtonumber)) {
            if ($findtonumber < $findfromnumber) list ($findfromnumber, $findtonumber) = array($findtonumber, $findfromnumber);
            $where .= ' AND available>=' . $findfromnumber . ' AND available<=' . $findtonumber;
        } elseif (!empty ($findfromnumber)) {
            $where .= ' AND available=' . $findfromnumber;
        }
        if (!Util::IsPrice($findfromprice)) $findfromprice = '';
        if (!Util::IsPrice($findtoprice)) $findtoprice = '';
        if (empty ($findfromprice) && !empty ($findtoprice)) {
            $findfromprice = $findtoprice;
            $findtoprice = '';
        }
        if (!empty ($findfromprice) && !empty ($findtoprice)) {
            if ($findtoprice < $findfromprice) list ($findfromprice, $findtoprice) = array($findtoprice, $findfromprice);
            $where .= ' AND cost>=' . $findfromprice . ' AND cost<=' . $findtoprice;
        } elseif (!empty ($findfromprice)) {
            $where .= ' AND cost=' . $findfromprice;
        }
        if (!Util::IsPrice($findfromtotal)) $findfromtotal = '';
        if (!Util::IsPrice($findtototal)) $findtototal = '';
        if (empty ($findfromtotal) && !empty ($findtototal)) {
            $findfromtotal = $findtototal;
            $findtototal = '';
        }
        if (!empty ($findfromtotal) && !empty ($findtototal)) {
            if ($findtototal < $findfromtotal) list ($findfromtotal, $findtototal) = array($findtototal, $findfromtotal);
            $where .= ' AND totalcost>=' . $findfromtotal . ' AND totalcost<=' . $findtototal;
        } elseif (!empty ($findfromtotal)) {
            $where .= ' AND totalcost=' . $findfromtotal;
        }

        $localavailable = $localcost = $localtotalcost = 0;
        $rs = InventoryHistoryCls::queryList($where, $date, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通库存量历史查询');

        $i = 1;

        $sheet->mergeCells("A{$i}:N{$i}");
        $sheet->setCellValue("A{$i}", '广通库存量历史查询');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:G{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("H{$i}:N{$i}");
        $sheet->setCellValue("H{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '历史ID');
        $sheet->setCellValue("B{$i}", '产品ID');
        $sheet->setCellValue("C{$i}", '产品代码');
        $sheet->setCellValue("D{$i}", '产品名称');
        $sheet->setCellValue("E{$i}", '分类ID');
        $sheet->setCellValue("F{$i}", '分类名称');
        $sheet->setCellValue("G{$i}", '规格');
        $sheet->setCellValue("H{$i}", '单位');
        $sheet->setCellValue("I{$i}", '仓库ID');
        $sheet->setCellValue("J{$i}", '仓库名称');
        $sheet->setCellValue("K{$i}", '库存量');
        $sheet->setCellValue("L{$i}", '成本');
        $sheet->setCellValue("M{$i}", '库存总价');
        $sheet->setCellValue("N{$i}", '产品一级分类');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:N{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['id']);
            $sheet->setCellValue("B{$i}", $v ['productid']);
            $sheet->setCellValue("C{$i}", $v ['productsku']);
            $sheet->setCellValue("D{$i}", $v ['product']);
            $sheet->setCellValue("E{$i}", $v ['productcatalogid']);
            $sheet->setCellValue("F{$i}", $v ['productcatalog']);
            $sheet->setCellValue("G{$i}", $v ['productrule']);
            $sheet->setCellValue("H{$i}", $v ['productunit']);
            $sheet->setCellValue("I{$i}", $v ['repositoryid']);
            $sheet->setCellValue("J{$i}", $v ['repository']);
            $sheet->setCellValue("K{$i}", $v ['available']);
            $sheet->setCellValue("L{$i}", $v ['cost']);
            $sheet->setCellValue("M{$i}", $v ['totalcost']);
            $sheet->setCellValue("N{$i}", ProductCatalogCls::Instance()->GetLevel1Name($v ['productcatalogid']));

            $localavailable += $v ['available'];
            $localcost += $v ['cost'];
            $localtotalcost += $v ['totalcost'];
        }

        $sheet->getStyle("A3:N{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $i++;
        $sheet->setCellValue("A{$i}", '总计数量：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localavailable));
        $i++;
        $sheet->setCellValue("A{$i}", '总计成本：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localcost));
        $i++;
        $sheet->setCellValue("A{$i}", '总计总价：');
        $sheet->setCellValue("B{$i}", Format::PriceString($localtotalcost));

        $filename = "InventoryReportHistoryFind.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelFinanceCustomerAccountReport()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::FINANCE_FINANCE_REPORT)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = trim(Url::Req('findname', '', 'str'));
        $finddepartmentid = UUID::Check(Url::Req('finddepartmentid', '', 'str'));
        $findcreatorid = UUID::Check(Url::Req('findcreatorid', '', 'str'));
        $findcreator2 = trim(Url::Req('findcreator2', '', 'str'));

        $orderkey = Url::Req('orderkey', '', 'str');
        $orderval = Url::Req('orderval', 0, 'int');

        $where = 'WHERE del=false AND (inventoryimportlast IS NOT NULL OR inventoryexportlast IS NOT NULL OR financeimportlast IS NOT NULL OR financeexportlast IS NOT NULL OR initimportdate IS NOT NULL OR initexportdate IS NOT NULL)';
        $order = 'ORDER BY time DESC';

        if (!empty ($findname)) {
            $where .= ' AND (name like \'%' . $findname . '%\' OR code like \'%' . $findname . '%\')';
        }
        if (!empty ($finddepartmentid)) {
            $keys = DepartmentCls::Instance()->GetChildren($finddepartmentid);
            $keys = UserCls::Instance()->getIDListByDepartmentIDList($keys);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND creatorid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findcreatorid)) {
            $where .= ' AND creatorid=\'' . $findcreatorid . '\'::UUID';
        }
        if (!empty ($findcreator2)) {
            $keys = UserCls::FindLike($findcreator2);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND creatorid IN (\'' . $keys . '\') ';
            }
        }

        $totalalldebt = $totaldebtimport = $totaldebtexport = $totalinventoryimport = $totalinventoryexport = $totalfinanceimport = $totalfinanceexport = 0;
        $rs = CustomerCls::queryList($where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通应收应付查询');

        $i = 1;

        $sheet->mergeCells("A{$i}:K{$i}");
        $sheet->setCellValue("A{$i}", '广通应收应付查询');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:G{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("H{$i}:K{$i}");
        $sheet->setCellValue("H{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '客户ID');
        $sheet->setCellValue("B{$i}", '客户代码');
        $sheet->setCellValue("C{$i}", '客户名称');
        $sheet->setCellValue("D{$i}", '客户类型');
        $sheet->setCellValue("E{$i}", '负责人');
        $sheet->setCellValue("F{$i}", '应收欠款');
        $sheet->setCellValue("G{$i}", '应收总额');
        $sheet->setCellValue("H{$i}", '已收总额');
        $sheet->setCellValue("I{$i}", '应付欠款');
        $sheet->setCellValue("J{$i}", '应付总额');
        $sheet->setCellValue("K{$i}", '已付总额');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:K{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['id']);
            $sheet->setCellValue("B{$i}", $v ['code']);
            $sheet->setCellValue("C{$i}", $v ['name']);
            $sheet->setCellValue("D{$i}", $v ['customertype']);
            $sheet->setCellValue("E{$i}", $v ['creator']);
            $sheet->setCellValue("F{$i}", $v ['debtimport']);
            $sheet->setCellValue("G{$i}", $v ['inventoryimport']);
            $sheet->setCellValue("H{$i}", $v ['financeimport']);
            $sheet->setCellValue("I{$i}", $v ['debtexport']);
            $sheet->setCellValue("J{$i}", $v ['inventoryexport']);
            $sheet->setCellValue("K{$i}", $v ['financeexport']);

            $totalalldebt += $v ['alldebt'];
            $totaldebtimport += $v ['debtimport'];
            $totaldebtexport += $v ['debtexport'];
            $totalinventoryimport += $v ['inventoryimport'];
            $totalinventoryexport += $v ['inventoryexport'];
            $totalfinanceimport += $v ['financeimport'];
            $totalfinanceexport += $v ['financeexport'];
        }

        $sheet->getStyle("A3:K{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $i++;
        $sheet->setCellValue("A{$i}", '总计应收欠款：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totaldebtimport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计应收总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalinventoryimport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计已收总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalfinanceimport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计应付欠款：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totaldebtexport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计应付总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalinventoryexport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计已付总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalfinanceexport));

        $filename = "FinanceCustomerAccountReport.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelContactsReport()
    {
        $where = 'WHERE del=false AND act=true';
        $order = 'ORDER BY time DESC';

        $rs = UserProfileCls::Items($where, $order, 0, 0);

        // $rs = array_multisort($rs,SORT_ASC,SORT_REGULAR);
        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通通讯录');

        $i = 1;

        $sheet->mergeCells("A{$i}:J{$i}");
        $sheet->setCellValue("A{$i}", '广通通讯录');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:G{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("H{$i}:J{$i}");
        $sheet->setCellValue("H{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '序号');
        $sheet->setCellValue("B{$i}", '部门');
        $sheet->setCellValue("C{$i}", '姓名');
        $sheet->setCellValue("D{$i}", '职务');
        $sheet->setCellValue("E{$i}", '分机号');
        $sheet->setCellValue("F{$i}", '公司集团号');
        $sheet->setCellValue("G{$i}", '集团短号');
        $sheet->setCellValue("H{$i}", '手机号码');
        $sheet->setCellValue("I{$i}", '邮箱');
        $sheet->setCellValue("J{$i}", '备注');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:J{$i}");
        $j = 0;
        foreach ($rs as $v) {
            $i++;
            $j++;
            $sheet->setCellValue("A{$i}", $j);
            $sheet->setCellValue("B{$i}", $v ['department']);
            $sheet->setCellValue("C{$i}", $v ['name']);
            $sheet->setCellValue("D{$i}", $v ['place']);
            $sheet->setCellValue("E{$i}", $v ['extphone']);
            $sheet->setCellValue("F{$i}", $v ['companyphone']);
            $sheet->setCellValue("G{$i}", $v ['cornetcode']);
            $sheet->setCellValue("H{$i}", $v ['mobile']);
            $sheet->setCellValue("I{$i}", $v ['email']);
            $sheet->setCellValue("J{$i}", $v ['memo']);
        }

        $sheet->getStyle("A3:J{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "ContactsReport.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelFinanceCustomerAccountHistoryReport()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::INVENTORY_REPORT_SUMMARY)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = trim(Url::Req('findname', '', 'str'));
        $finddepartmentid = UUID::Check(Url::Req('finddepartmentid', '', 'str'));
        $findcreatorid = UUID::Check(Url::Req('findcreatorid', '', 'str'));
        $findcreator2 = trim(Url::Req('findcreator2', '', 'str'));
        $finddate = Date::Format(Url::Req('finddate', '', 'str'), 'd', '');

        $orderkey = Url::Req('orderkey', '', 'str');
        $orderval = Url::Req('orderval', 0, 'int');

        $date = '';
        $where = 'WHERE 1=1';

        if (!empty ($finddate)) {
            $date .= "WHERE DATE_TRUNC('day', time)<='" . $finddate . "'";
        }

        if (!empty ($findname)) {
            $keys = CustomerCls::FindLike($findname);
            $keys = array_merge($keys, CustomerCls::FindLike($findname, 'code'));
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND customerid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($finddepartmentid)) {
            $keys = DepartmentCls::Instance()->GetChildren($finddepartmentid);
            $keys = UserCls::Instance()->getIDListByDepartmentIDList($keys);
            $keys2 = array();
            foreach ($keys as $v) {
                $keys2 = array_merge($keys2, CustomerCls::findValue($v, 'creatorid'));
            }
            if (!empty ($keys2)) {
                $keys2 = implode("','", $keys2);
                $where .= ' AND customerid IN (\'' . $keys2 . '\') ';
            }
        }
        if (!empty ($findcreatorid)) {
            $keys = CustomerCls::findValue($findcreatorid, 'creatorid');
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND customerid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($findcreator2)) {
            $keys = UserCls::FindLike($findcreator2);
            $keys2 = array();
            foreach ($keys as $v) {
                $keys2 = array_merge($keys2, CustomerCls::findValue($v, 'creatorid'));
            }
            if (!empty ($keys2)) {
                $keys2 = implode("','", $keys2);
                $where .= ' AND customerid IN (\'' . $keys2 . '\') ';
            }
        }

        $totalalldebt = $totaldebtimport = $totaldebtexport = $totalinventoryimport = $totalinventoryexport = $totalfinanceimport = $totalfinanceexport = 0;
        $rs = FinanceHistoryCls::queryList($where, $date, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通应收应付历史查询');

        $i = 1;

        $sheet->mergeCells("A{$i}:K{$i}");
        $sheet->setCellValue("A{$i}", '广通应收应付历史查询');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:G{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("H{$i}:K{$i}");
        $sheet->setCellValue("H{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '客户ID');
        $sheet->setCellValue("B{$i}", '客户代码');
        $sheet->setCellValue("C{$i}", '客户名称');
        $sheet->setCellValue("D{$i}", '客户类型');
        $sheet->setCellValue("E{$i}", '负责人');
        $sheet->setCellValue("F{$i}", '应收欠款');
        $sheet->setCellValue("G{$i}", '应收总额');
        $sheet->setCellValue("H{$i}", '已收总额');
        $sheet->setCellValue("I{$i}", '应付欠款');
        $sheet->setCellValue("J{$i}", '应付总额');
        $sheet->setCellValue("K{$i}", '已付总额');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:K{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['customerid']);
            $sheet->setCellValue("B{$i}", $v ['customercode']);
            $sheet->setCellValue("C{$i}", $v ['customer']);
            $sheet->setCellValue("D{$i}", $v ['customertype']);
            $sheet->setCellValue("E{$i}", $v ['customercreator']);
            $sheet->setCellValue("F{$i}", $v ['debtimport']);
            $sheet->setCellValue("G{$i}", $v ['inventoryimport']);
            $sheet->setCellValue("H{$i}", $v ['financeimport']);
            $sheet->setCellValue("I{$i}", $v ['debtexport']);
            $sheet->setCellValue("J{$i}", $v ['inventoryexport']);
            $sheet->setCellValue("K{$i}", $v ['financeexport']);

            $totalalldebt += $v ['alldebt'];
            $totaldebtimport += $v ['debtimport'];
            $totaldebtexport += $v ['debtexport'];
            $totalinventoryimport += $v ['inventoryimport'];
            $totalinventoryexport += $v ['inventoryexport'];
            $totalfinanceimport += $v ['financeimport'];
            $totalfinanceexport += $v ['financeexport'];
        }

        $sheet->getStyle("A3:K{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $i++;
        $sheet->setCellValue("A{$i}", '总计应收欠款：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totaldebtimport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计应收总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalinventoryimport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计已收总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalfinanceimport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计应付欠款：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totaldebtexport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计应付总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalinventoryexport));
        $i++;
        $sheet->setCellValue("A{$i}", '总计已付总额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalfinanceexport));

        $filename = "FinanceCustomerAccountHistoryReport.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelFinanceCustomerAccountFlowReport()
    {
        if (!PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::FINANCE_FINANCE_REPORT)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findcustomerid = UUID::Check(Url::Req('findcustomerid', '', 'str'));

        $where = 'WHERE del=false';
        $order = 'ORDER BY time ASC, kid ASC';

        $view = View::Factory('FinanceCustomerAccountFlowReport');

        $view->showexport = PermissionCls::Instance($this->UID())->isCanExport(ResourceCls::FINANCE_FINANCE_REPORT);

        $rs = false;
        if (!empty ($findcustomerid)) {
            $where .= " AND customerid='" . $findcustomerid . "'::UUID";

            $rs = FinanceHistoryCls::queryFlowList($where, $order, 0, 0);

            foreach ($rs as $k => $v) {
                $rs [$k] ['typename'] = FinanceHistoryCls::getTypeName($v ['type']);
                $rs [$k] ['billtypename'] = '';
                $rs [$k] ['billcode'] = '';
                $rs [$k] ['billtarget'] = '';
                if ($v ['type'] == 1) {
                    $rs [$k] ['billtypename'] = InventoryCls::getTypeName($v ['billtype']);
                    $rs [$k] ['billcode'] = InventoryCls::Instance()->GetCode($v ['billid']);
                    if ($v ['billtype'] == 2) {
                        $rs [$k] ['billtarget'] = '应收';
                    } elseif ($v ['billtype'] == 6) {
                        $rs [$k] ['billtarget'] = '应收';
                        $rs [$k] ['billmoney'] = 0 - $rs [$k] ['billmoney'];
                    } elseif ($v ['billtype'] == 1) {
                        $rs [$k] ['billtarget'] = '应付';
                    } elseif ($v ['billtype'] == 7) {
                        $rs [$k] ['billtarget'] = '应付';
                        $rs [$k] ['billmoney'] = 0 - $rs [$k] ['billmoney'];
                    }
                } elseif ($v ['type'] == 2) {
                    $rs [$k] ['billtypename'] = FinanceCls::getTypeName($v ['billtype']);
                    $rs [$k] ['billcode'] = FinanceCls::Instance()->GetCode($v ['billid']);
                    if ($v ['billtype'] == 1) {
                        $rs [$k] ['billtarget'] = '已收';
                    } elseif ($v ['billtype'] == 2) {
                        $rs [$k] ['billtarget'] = '已付';
                    }
                }
            }
        }

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('客户应收应付流水查询');

        $i = 1;

        $sheet->mergeCells("A{$i}:L{$i}");
        $sheet->setCellValue("A{$i}", '客户应收应付流水查询');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:G{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("H{$i}:L{$i}");
        $sheet->setCellValue("H{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '审批日期');
        $sheet->setCellValue("B{$i}", '');
        $sheet->setCellValue("C{$i}", '');
        $sheet->setCellValue("D{$i}", '单据号');
        $sheet->setCellValue("E{$i}", '单据价值');
        $sheet->setCellValue("F{$i}", '收付目标');
        $sheet->setCellValue("G{$i}", '应收欠款');
        $sheet->setCellValue("H{$i}", '应收总额');
        $sheet->setCellValue("I{$i}", '已收总额');
        $sheet->setCellValue("J{$i}", '应付欠款');
        $sheet->setCellValue("K{$i}", '应付总额');
        $sheet->setCellValue("L{$i}", '已付总额');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:L{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['time']);
            $sheet->setCellValue("B{$i}", $v ['typename']);
            $sheet->setCellValue("C{$i}", $v ['billtypename']);
            $sheet->setCellValue("D{$i}", $v ['billcode']);
            $sheet->setCellValue("E{$i}", $v ['billmoney']);
            $sheet->setCellValue("F{$i}", $v ['billtarget']);
            $sheet->setCellValue("G{$i}", $v ['debtimport']);
            $sheet->setCellValue("H{$i}", $v ['inventoryimport']);
            $sheet->setCellValue("I{$i}", $v ['financeimport']);
            $sheet->setCellValue("J{$i}", $v ['debtexport']);
            $sheet->setCellValue("K{$i}", $v ['inventoryexport']);
            $sheet->setCellValue("L{$i}", $v ['financeexport']);
        }

        $sheet->getStyle("A3:L{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "FinanceCustomerAccountFlowReport.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelProductList()
    {
        $where = '';
        $order = 'ORDER BY time DESC';

        $rs = ProductCls::Items($where, $order, 0, 0);
        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通产品列表');

        $i = 1;

        $sheet->mergeCells("A{$i}:W{$i}");
        $sheet->setCellValue("A{$i}", '广通产品列表');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:I{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("J{$i}:W{$i}");
        $sheet->setCellValue("J{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("J{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '产品ID');
        $sheet->setCellValue("B{$i}", '产品名称');
        $sheet->setCellValue("C{$i}", '库存号');
        $sheet->setCellValue("D{$i}", '供应商序号');
        $sheet->setCellValue("E{$i}", '分类ID');
        $sheet->setCellValue("F{$i}", '分类名称');
        $sheet->setCellValue("G{$i}", '品牌ID');
        $sheet->setCellValue("H{$i}", '品牌名称');
        $sheet->setCellValue("I{$i}", '单位ID');
        $sheet->setCellValue("J{$i}", '单位名称');
        $sheet->setCellValue("K{$i}", '含图');
        $sheet->setCellValue("L{$i}", '促销');
        $sheet->setCellValue("M{$i}", '推荐');
        $sheet->setCellValue("N{$i}", '网售');
        $sheet->setCellValue("O{$i}", '上架');
        $sheet->setCellValue("P{$i}", '浏览');
        $sheet->setCellValue("Q{$i}", '规格');
        $sheet->setCellValue("R{$i}", '价格');
        $sheet->setCellValue("S{$i}", '创建者');
        $sheet->setCellValue("T{$i}", '创建时间');
        $sheet->setCellValue("U{$i}", '更新者');
        $sheet->setCellValue("V{$i}", '更新时间');
        $sheet->setCellValue("W{$i}", '删除');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:W{$i}");

        foreach ($rs as $v) {
            $i++;
            $sheet->setCellValue("A{$i}", $v ['id']);
            $sheet->setCellValue("B{$i}", $v ['name']);
            $sheet->setCellValue("C{$i}", $v ['code']);
            $sheet->setCellValue("D{$i}", $v ['serial']);
            $sheet->setCellValue("E{$i}", $v ['catalogid']);
            $sheet->setCellValue("F{$i}", $v ['catalog']);
            $sheet->setCellValue("G{$i}", $v ['brandid']);
            $sheet->setCellValue("H{$i}", $v ['brand']);
            $sheet->setCellValue("I{$i}", $v ['unitid']);
            $sheet->setCellValue("J{$i}", $v ['unit']);
            $sheet->setCellValue("K{$i}", Format::BoolString(Common::HasPhoto($v ['pic'])));
            $sheet->setCellValue("L{$i}", Format::BoolString($v ['promotion']));
            $sheet->setCellValue("M{$i}", Format::BoolString($v ['commend']));
            $sheet->setCellValue("N{$i}", Format::BoolString($v ['shop']));
            $sheet->setCellValue("O{$i}", Format::BoolString($v ['act']));
            $sheet->setCellValue("P{$i}", $v ['views']);
            $sheet->setCellValue("Q{$i}", $v ['rule']);
            $sheet->setCellValue("R{$i}", Format::PriceString($v ['price']));
            $sheet->setCellValue("S{$i}", $v ['creator']);
            $sheet->setCellValue("T{$i}", $v ['time']);
            $sheet->setCellValue("U{$i}", $v ['updater']);
            $sheet->setCellValue("V{$i}", $v ['updaterlast']);
            $sheet->setCellValue("W{$i}", Format::BoolString($v ['del']));
        }

        $sheet->getStyle("A3:W{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "ProductList.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelCatalogList()
    {
        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通产品分类结构');

        $i = 1;

        $sheet->mergeCells("A{$i}:I{$i}");
        $sheet->setCellValue("A{$i}", '广通产品分类结构');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:E{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("F{$i}:I{$i}");
        $sheet->setCellValue("F{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '一级分类ID');
        $sheet->setCellValue("B{$i}", '一级分类名称');
        $sheet->setCellValue("C{$i}", '二级分类ID');
        $sheet->setCellValue("D{$i}", '二级分类名称');
        $sheet->setCellValue("E{$i}", '三级分类ID');
        $sheet->setCellValue("F{$i}", '三级分类名称');
        $sheet->setCellValue("G{$i}", '四级分类ID');
        $sheet->setCellValue("H{$i}", '四级分类名称');
        $sheet->setCellValue("I{$i}", '网售');
        $this->excelSetSubTitleStyle($sheet, "A{$i}:I{$i}");

        $rs1 = ProductCatalogCls::Instance()->GetChildrenList(null);
        foreach ($rs1 as $v1) {
            $i++;
            $sheet->setCellValue("A{$i}", $v1 ['id']);
            $sheet->setCellValue("B{$i}", $v1 ['name']);
            $sheet->setCellValue("I{$i}", $v1 ['shop']);

            $rs2 = ProductCatalogCls::Instance()->GetChildrenList($v1 ['id']);
            foreach ($rs2 as $v2) {
                $i++;
                $sheet->setCellValue("A{$i}", $v1 ['id']);
                $sheet->setCellValue("B{$i}", $v1 ['name']);
                $sheet->setCellValue("C{$i}", $v2 ['id']);
                $sheet->setCellValue("D{$i}", $v2 ['name']);
                $sheet->setCellValue("I{$i}", $v2 ['shop']);

                $rs3 = ProductCatalogCls::Instance()->GetChildrenList($v2 ['id']);
                foreach ($rs3 as $v3) {
                    $i++;
                    $sheet->setCellValue("A{$i}", $v1 ['id']);
                    $sheet->setCellValue("B{$i}", $v1 ['name']);
                    $sheet->setCellValue("C{$i}", $v2 ['id']);
                    $sheet->setCellValue("D{$i}", $v2 ['name']);
                    $sheet->setCellValue("E{$i}", $v3 ['id']);
                    $sheet->setCellValue("F{$i}", $v3 ['name']);
                    $sheet->setCellValue("I{$i}", $v3 ['shop']);

                    $rs4 = ProductCatalogCls::Instance()->GetChildrenList($v3 ['id']);
                    foreach ($rs4 as $v4) {
                        $i++;
                        $sheet->setCellValue("A{$i}", $v1 ['id']);
                        $sheet->setCellValue("B{$i}", $v1 ['name']);
                        $sheet->setCellValue("C{$i}", $v2 ['id']);
                        $sheet->setCellValue("D{$i}", $v2 ['name']);
                        $sheet->setCellValue("E{$i}", $v3 ['id']);
                        $sheet->setCellValue("F{$i}", $v3 ['name']);
                        $sheet->setCellValue("G{$i}", $v4 ['id']);
                        $sheet->setCellValue("H{$i}", $v4 ['name']);
                        $sheet->setCellValue("I{$i}", $v4 ['shop']);
                    }
                }
            }
        }

        $sheet->getStyle("A3:I{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "CatalogList.xlsx";

        $this->excelOutput($writer, $filename);
    }

    public function ExcelSaleReportProductTop()
    {
        if (!PermissionCls::Instance($this->UID())->isCanView(ResourceCls::SALE_REPORT_SUMMARY)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = Url::Req('findname', '', 'str');

        $orderkey = Url::Req('orderkey', '', 'str');
        $orderval = Url::Req('orderval', 0, 'int');

        $where = $this->GetPrivilegeUserSql('ownerid', ResourceCls::SALE_REPORT_SUMMARY);

        $order = '';
        $ordertips = '';

        if (empty ($orderkey) || $orderkey != 'number') {
            $order = 'ORDER BY totalprice DESC';
            $ordertips = '销售额';
        } else {
            $order = 'ORDER BY number DESC';
            $ordertips = '销售量';
        }

        $rs = InventoryCls::GetProductTopItems(1, $where, $order, 0, 0);

        if (empty ($rs)) {
            echo 'export no data!';
            return;
        }

        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通销售产品排行');

        $i = 1;

        $sheet->mergeCells("A{$i}:E{$i}");
        $sheet->setCellValue("A{$i}", '广通销售产品排行');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:C{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("D{$i}:E{$i}");
        $sheet->setCellValue("D{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '按' . $ordertips . '排名');
        $sheet->setCellValue("B{$i}", '产品代码');
        $sheet->setCellValue("C{$i}", '产品名称');
        $sheet->setCellValue("D{$i}", '销售额');
        $sheet->setCellValue("E{$i}", '销售量');

        $this->excelSetSubTitleStyle($sheet, "A{$i}:E{$i}");

        $pos = $totalprice = $totalnumber = 0;

        foreach ($rs as $v) {
            $i++;
            $pos++;

            $sheet->setCellValue("A{$i}", $pos);
            $sheet->setCellValue("B{$i}", ProductCls::Instance()->GetCode($v ['productid']));
            $sheet->setCellValue("C{$i}", ProductCls::Instance()->GetName($v ['productid']));
            $sheet->setCellValue("D{$i}", Format::PriceString($v ['totalprice']));
            $sheet->setCellValue("E{$i}", Format::NumberString($v ['number']));

            $totalprice += $v ['totalprice'];
            $totalnumber += $v ['number'];
        }

        $sheet->getStyle("A3:E{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $i++;
        $sheet->setCellValue("A{$i}", '总计销售额：');
        $sheet->setCellValue("B{$i}", Format::PriceString($totalprice));
        $i++;
        $sheet->setCellValue("A{$i}", '总计销售量：');
        $sheet->setCellValue("B{$i}", Format::NumberString($totalnumber));

        $filename = "SaleReportProductTop.xlsx";

        $this->excelOutput($writer, $filename);
    }

    private function excelSetCaptionStyle($sheet, $cell)
    {
        // $sheet->getStyle ( $cell )->getFont ()->setName ( '黑体' );
        $sheet->getStyle($cell)->getFont()->setSize(18);
        // $sheet->getStyle ( $cell )->getFont ()->setBold ( true );
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    private function excelSetTitleStyle($sheet, $cell)
    {
        // $sheet->getStyle ( $cell )->getFont ()->setName ( '黑体' );
        $sheet->getStyle($cell)->getFont()->setSize(18);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    private function excelSetSubTitleStyle($sheet, $cell, $argb = 'FFEEEEEE')
    {
        $sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB($argb);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    // 暂时作废
    private function excelSetInventoryWidth($sheet)
    {
        $sheet->getColumnDimension("A")->setWidth(12);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(8);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(15);
        $sheet->getColumnDimension("G")->setWidth(15);
        $sheet->getColumnDimension("H")->setWidth(15);
        $sheet->getColumnDimension("I")->setWidth(15);
    }

    private function excelOutput($writer, $filename)
    {
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        // header ( "Content-Type: application/octet-stream" );
        // //IE7,IE8会提示安全框,改为下面文本输出欺骗浏览器
        // header ( "Content-Type: text/plain" );
        header("Content-Type: application/msexcel");
        header('Content-Disposition: inline;filename="' . $filename . '"'); // attachment
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $writer->save('php://output');
    }

    private function FindInventoryWhere($type, $resource, $findname, $findcurrencyid, $findrepositoryid, $findstateid, $findcustomerid, $findcustomer2, $findfromdate, $findtodate, $findfromtotal, $findtototal, $findowners, $findcreators, $findtax, $findopenup, $findfinishedstateid, $findpaymentid = false, $finddstrepositoryid = false)
    {
        $repos = '';
        $dstrepos = '';
        $keys = RepositoryCls::Instance()->getIDListByAdminID($this->UID());
        if (!empty ($keys)) {
            $keys = implode("','", $keys);
            $repos .= ' repositoryid IN (\'' . $keys . '\') OR ';

            if ($finddstrepositoryid !== false) $repos .= ' dstrepositoryid IN (\'' . $keys . '\') OR ';
        }
        $repos = '';
        $where = 'WHERE ' . $repos . ' (type=' . $type . ' AND del=false';

        if (!empty ($findname)) {
            $where .= ' AND (code like \'%' . strtoupper($findname) . '%\' ';

            $keys = ProductCls::FindLike($findname);
            $keys = array_merge($keys, ProductCls::FindLike($findname, 'code'));

            if (!empty ($keys)) {
                $keys = InventoryProductCls::findInventoryIDListByProductIDList($keys);

                if (!empty ($keys)) {
                    $keys = implode("','", $keys);
                    $where .= ' OR id IN (\'' . $keys . '\') ';
                }
            }

            $where .= ')';
        }
        if (!empty ($findrepositoryid)) {
            $where .= ' AND repositoryid=\'' . $findrepositoryid . '\'::UUID';
        }
        if ($findstateid >= 0) {
            $where .= ' AND stateid=' . $findstateid;
        }
        if (!empty ($findcustomerid)) {
            $where .= " AND customerid='{$findcustomerid}'";
        }
        if (!empty ($findcustomer2)) {
            $keys = CustomerCls::FindLike($findcustomer2);

            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND customerid IN (\'' . $keys . '\') ';
            }
        }
        if (empty ($findfromdate) && !empty ($findtodate)) {
            $findfromdate = $findtodate;
            $findtodate = '';
        }
        if (!empty ($findfromdate) && !empty ($findtodate)) {
            $where .= " AND approvelast IS NOT NULL AND DATE_TRUNC('day', approvelast)>='" . $findfromdate . "' AND DATE_TRUNC('day', approvelast)<='" . $findtodate . "'";
        } elseif (!empty ($findfromdate)) {
            $where .= " AND approvelast IS NOT NULL AND DATE_TRUNC('day', approvelast)='" . $findfromdate . "'";
        }
        if (!Util::IsPrice($findfromtotal)) $findfromtotal = '';
        if (!Util::IsPrice($findtototal)) $findtototal = '';
        if (empty ($findfromtotal) && !empty ($findtototal)) {
            $findfromtotal = $findtototal;
            $findtototal = '';
        }
        if (!empty ($findfromtotal) && !empty ($findtototal)) {
            if ($findtototal < $findfromtotal) list ($findfromtotal, $findtototal) = array($findtototal, $findfromtotal);
            $where .= " AND (totalprice+carriage+expense)>='" . $findfromtotal . "' AND (totalprice+carriage+expense)<='" . $findtototal . "'";
        } elseif (!empty ($findfromtotal)) {
            $where .= " AND (totalprice+carriage+expense)='" . $findfromtotal . "'";
        }
        $findownerids = UserCls::Instance()->getIDListByUNamesString($findowners);
        if (!empty ($findownerids)) {
            $findownerids = "'" . implode("','", $findownerids) . "'";
            $where .= ' AND ownerid IN (' . $findownerids . ') ';
        }
        $findcreatorids = UserCls::Instance()->getIDListByUNamesString($findcreators);
        if (!empty ($findcreatorids)) {
            $findcreatorids = "'" . implode("','", $findcreatorids) . "'";
            $where .= ' AND creatorid IN (' . $findcreatorids . ') ';
        }
        if (!empty ($findpaymentid)) {
            $where .= ' AND paymentid=\'' . $findpaymentid . '\'::UUID';
        }
        if (!empty ($finddstrepositoryid)) {
            $where .= ' AND dstrepositoryid=\'' . $finddstrepositoryid . '\'::UUID';
        }
        if (!empty ($findcurrencyid)) {
            $where .= ' AND currencyid=\'' . $findcurrencyid . '\'::UUID';
        }
        if ($findtax > 0) {
            if ($findtax == 1) {
                $where .= ' AND tax=true';
            } else {
                $where .= ' AND tax=false';
            }
        }
        if ($findopenup > 0) {
            if ($findopenup == 1) {
                $where .= ' AND openup=true';
            } else {
                $where .= ' AND openup=false';
            }
        }
        if ($findfinishedstateid >= 0) {
            $where .= ' AND finishedstateid=' . $findfinishedstateid;
        }
        if (!$this->IsAdmin()) {
            $perm = array($this->UID());
            $lead = UserCls::Instance()->IsLead($this->UID());
            if ($lead) {
                $dept = UserCls::Instance()->getDepartmentID($this->UID());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v) {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->UID())->getRoleResourceViewUserList($resource));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR ownerid IN (' . $perm . ')) ';
        }

        $where .= ')';

        return $where;
    }

    private function FindInventoryInitTrackWhere($type, $finddepartmentid, $findcatalogid, $findname, $findrepositoryid, $findfromnumber, $findtonumber, $findfromprice, $findtoprice, $findfromtotal, $findtototal, $findproductstatusid)
    {
        $where = 'WHERE del=false';

        if (!empty ($findname)) {
            $keys = ProductCls::FindLike($findname);
            $keys = array_merge($keys, ProductCls::FindLike($findname, 'code'));
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND productid IN (\'' . $keys . '\') ';
            }
        }
        if (!empty ($finddepartmentid)) {
            $keys = RepositoryCls::Instance()->GetReposioryIDListByDepartmentID($finddepartmentid);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND repositoryid IN (\'' . $keys . '\') ';
            }
        }
        if ($findproductstatusid > 0) {
            $keys = ProductCls::GetIDListByStatusID($findproductstatusid);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND productid IN (\'' . $keys . '\') ';
            } else {
                $where .= ' AND 1=2';
            }
        }
        if (!empty ($findrepositoryid)) {
            $where .= ' AND repositoryid=\'' . $findrepositoryid . '\'::UUID';
        }
        if (!empty ($findcatalogid)) {
            $keys = ProductCatalogCls::Instance()->GetChildren($findcatalogid);
            $keys = ProductCls::GetIDListByCatalogIDList($keys);
            if (!empty ($keys)) {
                $keys = implode("','", $keys);
                $where .= ' AND productid IN (\'' . $keys . '\') ';
            }
        }
        if (!Util::IsPrice($findfromnumber)) $findfromnumber = '';
        if (!Util::IsPrice($findtonumber)) $findtonumber = '';
        if (empty ($findfromnumber) && !empty ($findtonumber)) {
            $findfromnumber = $findtonumber;
            $findtonumber = '';
        }
        if (!empty ($findfromnumber) && !empty ($findtonumber)) {
            if ($findtonumber < $findfromnumber) list ($findfromnumber, $findtonumber) = array($findtonumber, $findfromnumber);
            if ($type === InventoryCls::INVENTORY_TYPE_INIT) {
                $where .= ' AND number>=' . $findfromnumber . ' AND number<=' . $findtonumber;
            } else {
                $where .= ' AND available>=' . $findfromnumber . ' AND available<=' . $findtonumber;
            }
        } elseif (!empty ($findfromnumber)) {
            if ($type === InventoryCls::INVENTORY_TYPE_INIT) {
                $where .= ' AND number=' . $findfromnumber;
            } else {
                $where .= ' AND available=' . $findfromnumber;
            }
        }
        if (!Util::IsPrice($findfromprice)) $findfromprice = '';
        if (!Util::IsPrice($findtoprice)) $findtoprice = '';
        if (empty ($findfromprice) && !empty ($findtoprice)) {
            $findfromprice = $findtoprice;
            $findtoprice = '';
        }
        if (!empty ($findfromprice) && !empty ($findtoprice)) {
            if ($findtoprice < $findfromprice) list ($findfromprice, $findtoprice) = array($findtoprice, $findfromprice);
            if ($type === InventoryCls::INVENTORY_TYPE_INIT) {
                $where .= ' AND (number*price)>=' . $findfromprice . ' AND (number*price)<=' . $findtoprice;
            } else {
                $where .= ' AND cost>=' . $findfromprice . ' AND cost<=' . $findtoprice;
            }
        } elseif (!empty ($findfromprice)) {
            if ($type === InventoryCls::INVENTORY_TYPE_INIT) {
                $where .= ' AND (number*price)=' . $findfromprice;
            } else {
                $where .= ' AND cost=' . $findfromprice;
            }
        }
        if (!Util::IsPrice($findfromtotal)) $findfromtotal = '';
        if (!Util::IsPrice($findtototal)) $findtototal = '';
        if (empty ($findfromtotal) && !empty ($findtototal)) {
            $findfromtotal = $findtototal;
            $findtototal = '';
        }
        if (!empty ($findfromtotal) && !empty ($findtototal)) {
            if ($findtototal < $findfromtotal) list ($findfromtotal, $findtototal) = array($findtototal, $findfromtotal);
            if ($type === InventoryCls::INVENTORY_TYPE_INIT) {
                $where .= ' AND (number*price)>=' . $findfromtotal . ' AND (number*price)<=' . $findtototal;
            } else {
                $where .= ' AND totalcost>=' . $findfromtotal . ' AND totalcost<=' . $findtototal;
            }
        } elseif (!empty ($findfromtotal)) {
            if ($type === InventoryCls::INVENTORY_TYPE_INIT) {
                $where .= ' AND (number*price)=' . $findfromtotal;
            } else {
                $where .= ' AND totalcost=' . $findfromtotal;
            }
        }

        return $where;
    }

    // 临时，导出来目录树
    public function CatalogTreeList()
    {
        $where = "WHERE del=false";
        $rs = DB::db()->FetchAll("SELECT DISTINCT pid FROM t_product_catalog $where");
        $rd = array();
        if (is_array($rs) && !empty ($rs)) {
            $pid = array();
            foreach ($rs as $value) {
                if (UUID::Check($value ['pid'])) $pid [] = $value ['pid'];
            }
            $pidlist = implode("','", $pid);

            $ro = DB::db()->FetchAll("
					SELECT id FROM t_product_catalog WHERE id NOT IN ('{$pidlist}')
					AND del=false
					");

            if ($ro && count($ro) > 0) {
                foreach ($ro as $v) {
                    $rd [$v ['id']] = ProductCatalogCls::Instance()->GetParentList($v ['id']);
                }
            }
        }
        $excel = new PHPExcel ();

        $writer = new PHPExcel_Writer_Excel2007 ($excel);

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('广通产品分类结构');

        $i = 1;

        $sheet->mergeCells("A{$i}:H{$i}");
        $sheet->setCellValue("A{$i}", '广通产品分类结构');
        $sheet->getRowDimension($i)->setRowHeight(30);

        $this->excelSetTitleStyle($sheet, "A{$i}");

        $i++;
        $sheet->mergeCells("A{$i}:E{$i}");
        $sheet->setCellValue("A{$i}", '打印时间：' . Date::Datetime());
        $sheet->mergeCells("F{$i}:H{$i}");
        $sheet->setCellValue("F{$i}", '打印者：' . UserCls::Instance()->GetUName($this->UID()));

        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $i++;
        $sheet->setCellValue("A{$i}", '一级分类ID');
        $sheet->setCellValue("B{$i}", '一级分类名称');
        $sheet->setCellValue("C{$i}", '二级分类ID');
        $sheet->setCellValue("D{$i}", '二级分类名称');
        $sheet->setCellValue("E{$i}", '三级分类ID');
        $sheet->setCellValue("F{$i}", '三级分类名称');
        $sheet->setCellValue("G{$i}", '四级分类ID');
        $sheet->setCellValue("H{$i}", '四级分类名称');
        $this->excelSetSubTitleStyle($sheet, "A{$i}:H{$i}");

        foreach ($rd as $v1) {
            $i++;
            $sheet->setCellValue("A{$i}", isset ($v1 [0]) ? $v1 [0] : '');
            $sheet->setCellValue("B{$i}", isset ($v1 [0]) ? ProductCatalogCls::Instance()->GetName($v1 [0]) : '');
            $sheet->setCellValue("C{$i}", isset ($v1 [1]) ? $v1 [1] : '');
            $sheet->setCellValue("D{$i}", isset ($v1 [1]) ? ProductCatalogCls::Instance()->GetName($v1 [1]) : '');
            $sheet->setCellValue("E{$i}", isset ($v1 [2]) ? $v1 [2] : '');
            $sheet->setCellValue("F{$i}", isset ($v1 [2]) ? ProductCatalogCls::Instance()->GetName($v1 [2]) : '');
            $sheet->setCellValue("G{$i}", isset ($v1 [3]) ? $v1 [3] : '');
            $sheet->setCellValue("H{$i}", isset ($v1 [3]) ? ProductCatalogCls::Instance()->GetName($v1 [3]) : '');
        }

        $sheet->getStyle("A3:H{$i}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $filename = "CatalogList.xlsx";

        $this->excelOutput($writer, $filename);
    }

    private function SetPageStandartA4Vertical(&$sheet)
    {
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4); // A4纸大小
    }

    private function SetPrintHeader(&$sheet)
    {
        $header = new PHPExcel_Worksheet_HeaderFooterDrawing ();
        $header->setName('GreenTown Logo');
        $header->setPath('../../web/app/img/print-header-logo.gif');
        $header->setHeight(52);
        $sheet->getHeaderFooter()->addImage($header, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

        $sheet->getHeaderFooter()->setOddHeader("&L&G&R" . CompanyCls::FULL_NAME . "\n" . CompanyCls::ADDRESS . "\n电话：" . CompanyCls::TELEPHONE . " 传真：" . CompanyCls::FAX);

        $sheet->getPageMargins()->setTop(1);
    }

    private function SetPrintFooter(&$sheet, $title = '')
    {
        $header = new PHPExcel_Worksheet_HeaderFooterDrawing ();
        $sheet->getHeaderFooter()->setOddFooter("&C" . $title . "    &P / &N");
    }

    private function SetPrintHeaderFooter(&$sheet, $title = '')
    {
        $this->SetPrintHeader($sheet);
        $this->SetPrintFooter($sheet, $title);
    }

    private function SetPrintCaptionStyle(&$sheet, $cell)
    {
        $sheet->getStyle($cell)->getFont()->setSize(18);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
    }

    private function SetPrintTableTitleStyle(&$sheet, $cell, $argb = 'FFEEEEEE')
    {
        $sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB($argb);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }

    private function SetPrintTableBorderStyle(&$sheet, $cell)
    {
        $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }

    private function SetPrintFormatNumber(&$sheet, $cell)
    {
        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#0.00');
    }

    private function SetPrintCellTextValue(&$sheet, $cell, $val)
    {
        $sheet->setCellValue($cell, $val);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
    }

    private function SetPrintCellTextRightValue(&$sheet, $cell, $val)
    {
        $sheet->setCellValue($cell, $val);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
    }

    private function SetPrintCellCenterValue(&$sheet, $cell, $val)
    {
        $sheet->setCellValue($cell, $val);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    private function SetPrintCellNumberValue(&$sheet, $cell, $val)
    {
        $sheet->setCellValue($cell, $val);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle($cell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->SetPrintFormatNumber($sheet, $cell);
    }

    private function CreateUnderlineText($s1, $s2, $s3 = '', $s4 = '', $s5 = '')
    {
        $txt = new PHPExcel_RichText ();
        $txt->createText($s1);
        $underline = $txt->createTextRun($s2);
        $underline->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
        if (!empty ($s3)) {
            $txt->createText($s3);
        }
        if (!empty ($s4)) {
            $underline = $txt->createTextRun($s4);
            $underline->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
        }
        if (!empty ($s5)) {
            $txt->createText($s5);
        }

        return $txt;
    }
}

?>