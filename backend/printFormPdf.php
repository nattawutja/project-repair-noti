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
        src: url("fonts/Sarabun-Light.ttf") format("truetype");
        font-weight: normal;
      }
      @font-face {
        font-family: "Sarabun";
        src: url("fonts/Sarabun-Light.ttf") format("truetype");
        font-weight: bold;
      }
      body {
        font-family: "Sarabun", sans-serif;
        font-size: 16pt;
      }
    </style>
  </head>
  <body>
    <div style="font-size: 15px; text-align: right;">
      เลขเอกสาร : ' . $dt["RepairNo"] . '
    </div>
    <div style="font-size: 18px; font-weight: bold; text-align: center;">
      บริษัท โรงงานผลิตภัณฑ์อาหารไทย จำกัด
    </div>
    <div style="align-items: center;  margin-top: 5px;">
      <div style="text-align: center; flex: 1; font-size: 22px; font-weight: bold;">ใบแจ้งซ่อมอุปกรณ์คอมพิวเตอร์</div>
    </div>
      <hr style="margin-top:15px;">
      <div style="font-size: 18px;">
        <span style="margin-right: 230px;">ฝ่าย / แผนก : ' . $dt["name"] . ' </span>
        <span style="margin-left: 130px;">วันที่ : ' . $dt["cvdate"] . ' <span></span></span>
      </div>
      <div style="font-size: 18px; margin-bottom: 4px;">
        <span style="margin-right: 185px;">ผู้แจ้ง : ' . $dt["EmpName"] . '</span>
        <span style="margin-left: 166px;">รหัสแผนก : ' . $dt["DptCode"] . '</span> 
      </div>
      <div style="font-size: 18px; margin-bottom: 4px;">ประเภท : ' . $dt["systemtype"] . ' </div>
      <div style="font-size: 18px; margin-bottom: 8px;">ชนิดอุปกรณ์ : ' . $dt["name_Device"] . ' </div>

      <div style="display: flex; justify-content: space-between; font-size: 18px; align-items: center; width: 100%;">
        <label style="display: inline-block; margin-top: 0;">หมายเลขอุปกรณ์ : ' . $dt["DeviceToolID"] . ' </label>
      </div>
      <div style="font-size: 18px; margin-bottom: 8px;">รุ่น : ' . $dt["Model"] . ' </div>
      <div style="font-size: 18px; margin-bottom: 8px;">รหัสทรัพย์สิน : ' . $dt["ToolAssetID"] . ' </div>
      <div style="font-size: 18px; margin-bottom: 8px;">รายละเอียด : ' . $dt["description"] . ' </div>

    <hr>

    <div style="font-size: 18px; text-align: right; width: 100%; margin-top:15px;">
      ลงชื่อ_____________________<span style="text-decoration:underline">' . $dt["EmpName"] . '</span>____________________ผู้แจ้ง
    </div>
  
  </body>
</html>
';

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setChroot(__DIR__);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("file.pdf", ["Attachment" => false]);
?>
