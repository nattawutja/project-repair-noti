<?php
ob_start();
header("Access-Control-Allow-Origin: *");              // อนุญาตทุก origin (จะเปลี่ยนเป็นชื่อโดเมนเฉพาะก็ได้)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");  // กำหนด method ที่อนุญาต
header("Access-Control-Allow-Headers: Content-Type");  // อนุญาต header Content-Type
header("Content-Type: application/json");

require_once 'db.php';
$repairID = $_GET["id"]; // ✅ ตรงนี้คือแก้ให้รับแบบ GET
$data = [];
$qry = 'select t1."StatusWork",t1."OtherTool",t1."DeviceTypeID",t1."SystemType",t1."RepairNotifyDate",t4."name" as dviname,t3."name",t1."RepairID",to_char(t1."create_date",\'DD/MM/YYYY HH24:MI:ss\') as cvcreatedate,t1."RepairNo",to_char(t1."RepairNotifyDate",\'DD/MM/YYYY\') as cvdate,t1."DptCode",t1."EmpName",CASE WHEN t1."SystemType" = \'P\' THEN \'P/C\' else \'AS/400\' end as systemtype,t2."name_Device",t1."DeviceToolID",t1."Model",t1."ToolAssetID",t1."description" from "rp_Repair_Notify" t1 
left join "Master_Device_Type" t2 on t1."DeviceTypeID" = t2."id"
left join "Department" t3 on t1."DptCode" = t3."code"
left join "Division" t4 on t1."DviCode" = t4."code"
where t1."StatusDelete" = 0 and t1."RepairID" = ' . $repairID . ' ';
$result = pg_query($Con, $qry);
if ($result) {
  while ($row = pg_fetch_assoc($result)) {
    $data[] = $row; // ✅ ดึงทุกแถวเก็บไว้ใน array
  }
  echo json_encode($data);
} else {
  echo "Query ล้มเหลว";
}

pg_close($Con);
?>
