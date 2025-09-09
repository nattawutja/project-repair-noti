<?php
ob_start();
// ตอบ OPTIONS preflight request
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 86400");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204); // No Content
  exit(0);
}

require 'vendor/autoload.php';
require_once 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$docNo = $_GET['tbDocNoSearch'] ?? '';
$dptName = $_GET['tbDptNameSearch'] ?? '';
$dptCode = $_GET['tbDptCodeSearch'] ?? '';
$SystemType = $_GET['tbSystemTypeSearch'] ?? '';
$Tool = $_GET['tbToolSearch'] ?? '';
$ToolNumber = $_GET['tbToolNumberSearch'] ?? '';
$Model = $_GET['tbModelSearch'] ?? '';
$AssetID = $_GET['tbAssetIDSearch'] ?? '';
$StatusWork = $_GET['tbStatusWorkSearch'] ?? '';
$DateNotiStart = $_GET['tbDateNotiStartSearch'] ?? '';
$DateNotiEnd = $_GET['tbDateNotiEndSearch'] ?? '';
$EmpName = $_GET['tbEmpNameSearch'] ?? '';


// สร้างไฟล์ใหม่
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->getStyle('A1:M1')->getFill()->setFillType(Fill::FILL_SOLID);//ในช่วงเซลล์ A1:M1 (คือแถวแรก ตั้งแต่คอลัมน์ A ถึง M) ให้กำหนด รูปแบบการเติมสีพื้นหลัง (Fill)
$sheet->getStyle('A1:M1')->getFill()->getStartColor()->setARGB('FFFFFF00');  // บรรทัดนี้จะตั้งค่าสีพื้นหลังของเซลล์ในช่วง A1:M1 ให้เป็นสี LightSteelBlue
// เขียนหัวตาราง
$sheet->setCellValue('A1', 'ลำดับ');
$sheet->setCellValue('B1', 'เลขที่เอกสาร');
$sheet->setCellValue('C1', 'รหัสแผนก');
$sheet->setCellValue('D1', 'แผนก');
$sheet->setCellValue('E1', 'ประเภท');
$sheet->setCellValue('F1', 'ชนิดอุปกรณ์');
$sheet->setCellValue('G1', 'หมายเลขเครื่อง');
$sheet->setCellValue('H1', 'รุ่น');
$sheet->setCellValue('I1', 'รหัสทรัพย์สิน');
$sheet->setCellValue('J1', 'รายละเอียด');
$sheet->setCellValue('K1', 'วันที่แจ้ง');
$sheet->setCellValue('L1', 'ผู้แจ้ง');
$sheet->setCellValue('M1', 'สถานะ');


$qryExport = 'select ROW_NUMBER() OVER (ORDER BY t1."RepairID") AS row_index,t1."RepairNo",t1."DptCode",t1."DptName",CASE WHEN t1."SystemType" = \'P\' THEN \'P/C\' else \'AS/400\' end as systemtype,t2."name_Device",t1."DeviceToolID",t1."Model",t1."ToolAssetID",t1."description",to_char(t1."RepairNotifyDate",\'DD/MM/YYYY\') as cvdate,t1."EmpName",case when t1."StatusWork" = 0 then \'รอ IT ตรวจสอบ\' else \'จบงาน\' end as statuswork from "rp_Repair_Notify" t1
left join "Master_Device_Type" t2 on t1."DeviceTypeID" = t2."id"
where 1=1 and t1."StatusDelete" = 0 ';

if (!empty($docNo)) {
  $qryExport .= ' AND t1."RepairNo"  Ilike \'%' . $docNo . '%\' ';
}

if (!empty($DateNotiStart) && !empty($DateNotiEnd)) {
  $qryExport .= ' AND t1."RepairNotifyDate"  between \'' . $DateNotiStart . '\' and \'' . $DateNotiEnd . '\' ';
}else if(!empty($DateNotiStart) && empty($DateNotiEnd)){
  $qryExport .= ' AND t1."RepairNotifyDate"  = \'' . $DateNotiStart . '\' ';
}


if (!empty($dptCode)) {
  $qryExport .= ' AND t1."DptCode"  Ilike \'%' . $dptCode . '%\' ';
}
if (!empty($dptName)) {
  $qryExport .= ' AND "DptName"  Ilike \'%' . $dptName . '%\' ';
}
if (!empty($EmpName)) {
  $qryExport .= ' AND t1."EmpName"  Ilike \'%' . $EmpName . '%\' ';
}
if (!empty($SystemType)) {
  $qryExport .= ' AND t1."SystemType" = \'' . $SystemType . '\' ';
}

if (!empty($Model)) {
  $qryExport .= ' AND t1."Model"  Ilike \'%' . $Model . '%\' ';
}
if (!empty($AssetID)) {
  $qryExport .= ' AND t1."ToolAssetID"  Ilike \'%' . $AssetID . '%\' ';
}

if (!empty($StatusWork)) {
  $qryExport .= ' AND t1."StatusWork"  = ' . $StatusWork . ' ';
}

if (!empty($Tool)) {
  $qryExport .= ' AND t1."DeviceTypeID" = ' . $Tool . ' ';
}
if (!empty($ToolNumber)) {
  $qryExport .= ' AND t1."DeviceToolID"  Ilike \'%' . $ToolNumber . '%\' ';
}

$res = pg_query($Con,$qryExport);

$dt = [];
if(pg_num_rows($res) > 0){
  while ($row = pg_fetch_array($res)) {
    $dt[] = $row;
  }
}

for ($i = 0; $i < count($dt); $i++) {
  $row = $i + 2; // เริ่มที่แถว 2
  $sheet->setCellValue('A' . $row, $dt[$i][0]);
  $sheet->setCellValue('B' . $row, $dt[$i][1]);
  $sheet->setCellValue('C' . $row, $dt[$i][2]);
  $sheet->setCellValue('D' . $row, $dt[$i][3]);
  $sheet->setCellValue('E' . $row, $dt[$i][4]);
  $sheet->setCellValue('F' . $row, $dt[$i][5]);
  $sheet->setCellValue('G' . $row, $dt[$i][6]);
  $sheet->setCellValue('H' . $row, $dt[$i][7]);
  $sheet->setCellValue('I' . $row, $dt[$i][8]);
  $sheet->setCellValue('J' . $row, $dt[$i][9]);
  $sheet->setCellValue('K' . $row, $dt[$i][10]);
  $sheet->setCellValue('L' . $row, $dt[$i][11]);
  $sheet->setCellValue('M' . $row, $dt[$i][12]);
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

?>
