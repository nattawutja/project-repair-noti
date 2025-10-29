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
$check_otherTool = ($dt["OtherTool"] != "") ? $dt["OtherTool"] : '________________________________________';


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
      size: A5 portrait;
      margin: 7.5mm; /* ปลอดภัยกว่า 2.2mm */
    }
    body {
      margin: 0;
      padding: 0;
      height: 210mm;
      font-family: "Sarabun", sans-serif;
      font-weight: 400;
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
    <div style="font-size:11px; font-weight:bold; text-align: right; white-space: nowrap;">
      เลขที่ <span style="text-decoration: underline;">' . $dt["RepairNo"] . '</span>
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 5px;">
      <div style="font-size: 15px; font-weight: bold; text-align: left;">
        บริษัท โรงงานผลิตภัณฑ์อาหารไทย จำกัด
      </div>
    </div>
     
    <div style="display: flex; align-items: center; justify-content: center; margin-top: 5px;">
      <div style="flex: 1; text-align: center; font-size: 17px; font-weight: bold;">
        ใบแจ้งซ่อมเครื่องและอุปกรณ์คอมพิวเตอร์
      </div>
    </div>
    


    <div style="font-size:11px; font-weight:bold; display: flex; justify-content: space-between; align-items: center; font-size: 10px; margin-top: 10px; width: 100%;">

      <div style="display: inline-flex; align-items: center;">
        <span>ฝ่าย / แผนก </span>
        <span style="text-decoration: underline; min-width: 100px;">' . $dt["name"] . '</span>
      </div>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <div style="display: inline-flex; align-items: right;">
        <label>วันที่  </label>
        <label style="text-decoration:underline;"> ' . $date . '  </label>
        <label>/</label>
        <label style="text-decoration:underline;"> ' . $month . '  </label>
        <label>/</label>
        <label style="text-decoration:underline;"> ' . $year . '  </label>
      </div>

    </div>


    <div style="font-size:11px; font-weight:bold;  display: flex; justify-content: space-between; align-items: center; font-size: 10px; margin-top: 5px; width: 100%;">

      <div style="display: inline-flex; align-items: center;">
        <span>ชื่อ </span>
        <span style="text-decoration: underline; min-width: 100px;">' . $dt["EmpName"] . '</span>
      </div>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <div style="display: inline-flex; align-items: right;">
        <span>รหัสแผนก </span>
        <span style="text-decoration: underline; min-width: 60px;">' . $dt["DptCode"] . '</span>
      </div>

    </div>


      <div style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 10px;">
        <div style="flex: 1; display: inline-flex; font-size:11px; font-weight:bold;">
          ประเภท&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
        </div>
        <div style="flex: 1; display: inline-flex; ">
          <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_pc . ' </span>P/C&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
        </div>
        <div style="flex: 1; display: inline-flex;">
          <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;">' . $check_400 . ' </span>AS/400</span> 
        </div>
      </div>
  
      <div style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 10px;">
        <div style="flex: 1; display: inline-flex; font-size:11px; font-weight:bold;">
          ชนิดอุปกรณ์&nbsp;
        </div>
        <div style="flex: 1; display: inline-flex;">
          <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_computer . ' </span>เครื่องคอมพิวเตอร์</span> 
        </div>
        <div style="flex: 1; display: inline-flex;">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_Printer . ' </span>พริ้นเตอร์</span> 
        </div>
         <div style="flex: 1; display: inline-flex;">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_mouse . ' </span>เม้าส์</span> 
        </div>
      </div>

      <div style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 11px;">
        <div style="flex: 1; display: inline-flex;">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <div style="flex: 1; display: inline-flex;">
          <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_monitor . ' </span>จอภาพ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
        </div>
        <div style="flex: 1; display: inline-flex;">
          <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_keyboard . ' </span>คีย์บอร์ด &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
        </div>
        <div style="flex: 1; display: inline-flex;">
          <span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_network . ' </span>เครือข่าย</span> 
        </div>
      </div>

      <div style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 11px;">
        <div style="flex: 1; display: inline-flex;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <div style="flex: 1; display: inline-flex;">
          &nbsp;&nbsp;&nbsp;<span class="checkbox-label" style="margin-left: 10px;"><span class="checkbox" style="font-size:20px;"> ' . $check_other . ' </span>อื่นๆ ' . $check_otherTool . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
        </div>
       
      </div>

      <div style="font-size:11px; font-weight:bold; display:flex; justify-content:between; margin-top:5px;">
        <span>หมายเลขเครื่อง ________<span style="text-decoration:underline;">' . $dt["DeviceToolID"] . '</span>__________</span>
        <span>รุ่น ___________<span style="text-decoration:underline;">' . $dt["Model"] . '</span>___________</span>
        <span>รหัสทรัพย์สิน ____<span style="text-decoration:underline;">' . $dt["ToolAssetID"] . '</span>_________</span>
      </div>
      <div style="font-size:11px; font-weight:bold; margin-top:10px;">รายละเอียดอาการ <span style="text-decoration:underline;"> ' . $dt["description"] . '_______________________________________________________________</span>
      </div>
      <div style="font-size:11px; font-weight:bold;">________________________________________________________________________________________________________________________
      </div>
    <div style="font-size: 11px; text-align: right; width: 100%; margin-top:5px;">
      ลงชื่อ_____________________<span style="text-decoration:underline">' . $dt["EmpName"] . '</span>____________________ผู้แจ้ง
    </div>

    <div style="margin-top:5px;font-size:11px; font-weight:bold;">สำหรับฝ่าย MIS : <span style="text-decoration:underline;"></span>__________________________________________________________________________________________________
    </div>

    <div style="margin-top:5px;font-size:11px; font-weight:bold;">_______________________________________________________________________________________________________________________
    </div>

    <div style="font-size:11px; font-weight:bold; margin-top: 10px; display: flex; justify-content: space-between;">
      <label>วันที่ตรวจรับการดำเนินการ  : ________/________/________ </label>
      <label>&nbsp;&nbsp;&nbsp;&nbsp;วันที่ดำเนินการแล้วเสร็จ  : _______/_______/_______ </label>
    </div>

    <div style="font-size:11px; font-weight:bold; margin-top: 10px; display: flex; justify-content: space-between;">
      <label>ลงชื่อ_______________________________________ผู้ร้องขอ  </label>
      <label>ลงชื่อ_______________________________________ผู้ดำเนินงาน</label>
    </div>

    <div style="font-size:11px; font-weight:bold; margin-top:20px;">
      <span style="margin-right: 10px;">FM-MS-XX-002/Revision No.00</span>
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
$dompdf->setPaper('A5', 'portrait');
$dompdf->render();
$dompdf->stream("file.pdf", ["Attachment" => false]);

?>
