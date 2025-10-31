<?php
ob_start();
// ตอบ OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");
  header("Access-Control-Max-Age: 86400");
  exit(0);
}

header("Access-Control-Allow-Origin: *");

$repairID = intval($_GET['id']); // แปลงเป็นเลขจำนวนเต็ม

require 'vendor/autoload.php';
require_once 'db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$qry = 'SELECT t1."SystemType",t1."DeviceTypeID",t1."OtherTool",t1."RepairNo",to_char(t1."RepairNotifyDate",\'DD/MM/YYYY\') as cvdate,t1."DptCode",t1."EmpName",t3."name",
CASE WHEN t1."SystemType" = \'P\' THEN \'P/C\' else \'AS/400\' end as systemtype,t2."name_Device",t1."DeviceToolID",t1."Model",t1."ToolAssetID",t1."description" from "rp_Repair_Notify" t1 
left join "Master_Device_Type" t2 on t1."DeviceTypeID" = t2."id"
left join "Division" t3 on t1."DviCode" = t3."code"
where t1."StatusDelete" = 0 and t1."RepairID" = ' . $repairID;

$res = pg_query($Con,$qry);

if(pg_num_rows($res) > 0){
  $dt = pg_fetch_assoc($res);
} 

$explodeDate = explode("/", $dt["cvdate"]);
$date = $explodeDate[0];
$month = $explodeDate[1];
$year = $explodeDate[2];

$check_pc = ($dt["SystemType"] == "P") ? 'X' : '';
$check_400 = ($dt["SystemType"] == "A") ? 'X' : '';
$check_computer = ($dt["DeviceTypeID"] == 1) ? 'X' : '';
$check_Printer = ($dt["DeviceTypeID"] == 2) ? 'X' : '';
$check_mouse = ($dt["DeviceTypeID"] == 3) ? 'X' : '';
$check_monitor = ($dt["DeviceTypeID"] == 4) ? 'X' : '';
$check_keyboard = ($dt["DeviceTypeID"] == 5) ? 'X' : '';
$check_network = ($dt["DeviceTypeID"] == 6) ? 'X' : '';
$check_other = ($dt["DeviceTypeID"] == 7) ? 'X' : '';
$check_otherTool = ($dt["OtherTool"] != "") ? $dt["OtherTool"] : '______________________________________________________';


$html = '
<html>
  <head>
    <style>
    @font-face {
      font-family: "Sarabun";
      font-style: normal;
      font-weight: 400;
      src: url("fonts/Sarabun-Regular.ttf") format("truetype");
    }
    @font-face {
      font-family: "Sarabun";
      font-style: normal;
      font-weight: 700;
      src: url("fonts/Sarabun-Bold.ttf") format("truetype");
    }
    @page {
      size: A4 portrait;
    }

    body {
      font-family: "Sarabun", sans-serif;
      font-size: 11px;
    }
    .checkbox-label {
      margin-right: 15px;
      display: inline-flex;
      align-items: center;
      white-space: nowrap;
    }
    .checkbox {
      margin-right: 5px;
      width: 12px;
      height: 12px;
      border: 1px solid black;
      display: inline-block;
      text-align: center;
      line-height: 12px; 
      font-weight: bold;
    }

    </style>
  </head>
  <body>
    

    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 5px;">
      <div style="font-size: 14px; font-weight: bold; text-align: left;">
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;บริษัท โรงงานผลิตภัณฑ์อาหารไทย จำกัด
      </div>
    </div>
    
    <div style="display: inline-flex; align-items: center; justify-content: space-between;">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="text-align: end; font-size: 18px; font-weight: bold;">ใบแจ้งซ่อมเครื่องและอุปกรณ์คอมพิวเตอร์ </span>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="text-align: end; font-size: 12px; font-weight: bold;">เลขที่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size: 12px; font-weight: bold; text-decoration:underline">' . $dt["RepairNo"] . '</span> </span>
    </div>


    <div style="font-weight:bold; display: flex; justify-content: space-between; align-items: center; font-size: 10px; margin-top: 10px; width: 100%;">

      <div style="display: inline-flex; align-items: center;">
        <span style="font-weight:bold; font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ชื่อฝ่าย / แผนก &nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span style="font-weight:bold; font-size:12px; text-decoration: underline; min-width: 100px;">' . $dt["name"] . '</span>
      </div>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <div style="font-weight:bold; font-size:12px; display: inline-flex; align-items: right;">
        <label>วันที่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <label style="text-decoration:underline;"> ' . $date . '  </label>
        <label>/</label>
        <label style="text-decoration:underline;"> ' . $month . '  </label>
        <label>/</label>
        <label style="text-decoration:underline;"> ' . $year . '  </label>
      </div>

    </div>


    <div style="font-size:11px; font-weight:bold;  display: flex; justify-content: space-between; align-items: center; font-size: 10px; margin-top: 5px; width: 100%;">

      <div style="font-weight:bold; font-size:12px; display: inline-flex; align-items: center;">
        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ชื่อ  &nbsp;&nbsp;</span>
        <span style="text-decoration: underline; min-width: 100px;">' . $dt["EmpName"] . '</span>
      </div>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <div style="font-weight:bold; font-size:12px; display: inline-flex; align-items: right;">
        <span>รหัสแผนก </span>
        <span style="text-decoration: underline; min-width: 60px;">' . $dt["DptCode"] . '</span>
      </div>

    </div>


    <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px;">
      <div style="flex: 1; display: inline-flex; font-size:12px; font-weight:bold;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ประเภท&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     
      </div>
      <div style="flex: 1; display: inline-flex; ">
        <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_pc . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">P/C</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
      </div>
      <div style="flex: 1; display: inline-flex;">
        <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;">' . $check_400 . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">AS/400</span></span> 
      </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 12px;">
      <div style="flex: 1; display: inline-flex; font-size:11px; font-weight:bold;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ชนิดอุปกรณ์&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
      <div style="flex: 1; display: inline-flex;">
        <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_computer . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">เครื่องคอมพิวเตอร์</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
      </div>
      <div style="flex: 1; display: inline-flex;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_Printer . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">พริ้นเตอร์</span> </span> 
      </div>
      <div style="flex: 1; display: inline-flex;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_mouse . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">เม้าส์</span></span>  
      </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 12px;">
      <div style="flex: 1; display: inline-flex;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
      <div style="flex: 1; display: inline-flex;">
        <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_monitor . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">จอภาพ</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
      </div>
      <div style="flex: 1; display: inline-flex;">
        <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_keyboard . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">คีย์บอร์ด</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
      </div>
      <div style="flex: 1; display: inline-flex;">
        <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_network . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold">เครือข่าย</span></span> 
      </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px;">
      <div style="flex: 1; display: inline-flex;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
      <div style="flex: 1; display: inline-flex;">
        &nbsp;&nbsp;&nbsp;<span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_other . ' </span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold">อื่น ๆ</span> ' . $check_otherTool . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
      </div>
    
    </div>

    <div style="font-size:12px; font-weight:bold; display:flex; justify-content:between; margin-top:5px;">
      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;หมายเลขเครื่อง&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _____________<span style="text-decoration:underline;">' . $dt["DeviceToolID"] . '</span>_______________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;รุ่น ___________<span style="text-decoration:underline;">' . $dt["Model"] . '</span>___________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;รหัสทรัพย์สิน&nbsp;&nbsp;&nbsp;&nbsp;____<span style="text-decoration:underline;">' . $dt["ToolAssetID"] . '</span>__________________________</span>
    </div>
    <div style="font-size:12px; font-weight:bold; margin-top:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;รายละเอียดอาการ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="text-decoration:underline;"> ' . $dt["description"] . '_______________________________________________________________</span>
    </div>
    <div style="font-size:12px; font-weight:bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________________________________________________________________________________________________________________________________________________________
    </div>
    <div style="font-size: 12px;  margin-top:5px;">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ&nbsp;&nbsp;____________<span style="text-decoration:underline">' . $dt["EmpName"] . '</span>___________ผู้แจ้ง
    </div>

    <div style="font-size: 12px;  margin-top:2px;">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
    </div>

    <div style="font-size:12px; font-weight:bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;สำหรับฝ่าย MIS : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="text-decoration:underline;"></span>______________________________________________________________________________________________________________________________
    </div>

    <div style="margin-top:5px;font-size:12px; font-weight:bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;________________________________________________________________________________________________________________________________________________________
    </div>

    <div style="font-size:12px; font-weight:bold; margin-top: 5px; display: flex; justify-content: space-between;">
      <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ตรวจรับการดำเนินการ  :&nbsp;&nbsp;______/______/______ </label>
      <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ดำเนินการแล้วเสร็จ  :&nbsp;&nbsp;_______/_______/_______ </label>
    </div>

    <div style="font-size:12px; font-weight:bold; margin-top: 5px; display: flex; justify-content: space-between;">
      <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ__________________________________________________ผู้ร้องขอ  </label>
      <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ________________________________________________ผู้ดำเนินงาน</label>
    </div>

    <div style="font-size: 12px;  margin-top:2px; display:flex">
      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</label>
      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</label>
    </div>

    <div style="font-size:11px; margin-top:5px;">
      <span style="margin-right: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FM-MS-XX-002/Revision No.00</span>
    </div>

  </body>
</html>
';

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);
$options->set('defaultFont', 'Sarabun');
$options->set('isHtml5ParserEnabled', true);
$options->set('isFontSubsettingEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->set_option('isFontSubsettingEnabled', false);
$dompdf->set_option('isHtml5ParserEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("file.pdf", ["Attachment" => false]);

?>
