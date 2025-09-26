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
header("Content-Type: application/pdf");

$repairID = intval($_GET['id']); // แปลงเป็นเลขจำนวนเต็ม

require 'vendor/autoload.php';
require_once 'db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$qry = 'SELECT t1."RepairNo",to_char(t1."RepairNotifyDate",\'DD/MM/YYYY\') as cvdate,t1."DptCode",t1."EmpName",t3."name",
CASE WHEN t1."SystemType" = \'P\' THEN \'P/C\' else \'AS/400\' end as systemtype,t2."name_Device",t1."DeviceToolID",t1."Model",t1."ToolAssetID",t1."description" from "rp_Repair_Notify" t1 
left join "Master_Device_Type" t2 on t1."DeviceTypeID" = t2."id"
left join "Division" t3 on t1."DviCode" = t3."code"
where t1."StatusDelete" = 0 and t1."RepairID" = ' . $repairID;

$res = pg_query($Con,$qry);

if(pg_num_rows($res) > 0){
  $dt = pg_fetch_assoc($res);
} 

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
    body {
      font-family: "Sarabun", sans-serif;
      font-weight: 400;
    }
    </style>
  </head>
  <body>
   <div style="font-size: 22px; font-weight: bold; text-align: left;">
      บริษัท โรงงานผลิตภัณฑ์อาหารไทย จำกัด
    </div>
    <div style="font-size: 15px; text-align: right;">
     เลขเอกสาร ______<span style="text-decoration:underline;"> ' . $dt["RepairNo"] . '</span>_______

    </div>
    <div style="font-size: 15px; text-align: right; margin-top:5px;">
     วันที่ ______<span style="text-decoration:underline;"> ' . $dt["cvdate"] . '</span>_______
    </div>
    <div style="font-size: 15px; text-align: right;  margin-top:5px;">
      รหัสแผนก _________<span style="text-decoration:underline;"> ' . $dt["DptCode"] . '</span>_______

    </div>
   
    <div style="align-items: center;  margin-top: 5px;">
      <div style="text-align: center; flex: 1; font-size: 35px; font-weight: bold;">ใบแจ้งซ่อมอุปกรณ์คอมพิวเตอร์</div>
    </div>
    <div style="font-size: 18px; margin-top:20px;">
      <span style="margin-right: 20px;">ฝ่าย / แผนก  _____________________<span style="text-decoration:underline;">' . $dt["name"] . ' </span>_____________________</span>
      <span">ชื่อ _______________________<span style="text-decoration:underline;"> ' . $dt["EmpName"] . ' </span>___________________________________</span>
    </div>
      

      <div style="font-size: 18px;margin-top:15px;">
      <span>ประเภท  _________________<span style="text-decoration:underline;"> ' . $dt["systemtype"] . '</span>_________________ </span>
      <span style="margin-left:20px;">ชนิดอุปกรณ์ __________________<span style="text-decoration:underline;">' . $dt["name_Device"] . '</span>__________________ </span>
      <span style="margin-left:20px;">อื่นๆ _____________________________________ </span>
      </div>
   
      <div style="font-size: 18px; display:flex; justify-content:between; margin-top:15px;">
        <span>หมายเลขเครื่อง ________________<span style="text-decoration:underline;">' . $dt["DeviceToolID"] . '</span>__________________</span>
        <span style="margin-left:20px;">รุ่น _____________________<span style="text-decoration:underline;">' . $dt["Model"] . '</span>_____________________</span>
        <span style="margin-left:20px;">รหัสทรัพย์สิน ___________________<span style="text-decoration:underline;">' . $dt["ToolAssetID"] . '</span>______________________</span>
      </div>
      <div style="font-size: 18px; margin-top:15px;">รายละเอียดอาการ <span style="text-decoration:underline;"> ' . $dt["description"] . '</span>____________________________________________________________________________________________________________________________________________________
      ___________________________________________________________________________________________________________________________________________________________________________________ 
      </div>

    <div style="font-size: 18px; text-align: right; width: 100%; margin-top:15px;">
      ลงชื่อ_____________________<span style="text-decoration:underline">' . $dt["EmpName"] . '</span>____________________ผู้แจ้ง
    </div>

    <div style="margin-top:15px;font-size: 18px;">สำหรับฝ่าย MIS : <span style="text-decoration:underline;"></span>______________________________________________________________________________________________________________________________________________________
    _____________________________________________________________________________________________________________________________________________________________________________
    </div>

    <div style="font-size: 18px; margin-top:15px;">
      <span style="margin-right: 120px;">วันที่ตรวจรับการดำเนินการ : ___________/__________/__________ </span>
      <span style="margin-left: 50px;">วันที่ดำเนินการแล้วเสร็จ : ___________/___________/__________ </span>
    </div>

    <div style="font-size: 18px; margin-top:15px;">
      <label style="margin-right: 5px;">ลงชื่อ__________________________________________________________________ผู้ร้องขอ </label>
      <span style="margin-left: 5px;">ลงชื่อ___________________________________________________________________ผู้ดำเนินงาน </span>
    </div>

    <div style="font-size: 15px; margin-top:20px;">
      <span style="margin-right: 10px;">FM-MS-XX-002/Revision No.00</span>
    </div>

  </body>
</html>
';

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);
$dompdf->set_option('isFontSubsettingEnabled', false);
$dompdf->set_option('isHtml5ParserEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("file.pdf", ["Attachment" => false]);

?>
