<?php
//header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'db.php';
$data = [];
// ทดสอบ query ง่าย ๆ
$qry = 'select t3."name" as dviname,t4."name",CASE WHEN t1."StatusWork" = 0 THEN \'รอ IT ตรวจสอบ\' WHEN t1."StatusWork" = 1 THEN \'กำลังดำเนินการ\' WHEN t1."StatusWork" = 2 THEN \'รอผู้แจ้งตรวจสอบ\' else \'จบงาน\' end as status, CASE WHEN t1."SystemType" = \'P\' THEN \'P/C\' else \'AS/400\' end as systemname,t2."name_Device",to_char(t1."create_date",\'DD/MM/YYYY HH24:MI:SS\')as cvcreatedate,t1.* from "rp_Repair_Notify" t1
left join "Master_Device_Type" t2 on t1."DeviceTypeID" = t2."id" and t2."StatusDelete" = 0
left join "Division" t3 on t1."DviCode" = t3."code"
left join "Department" t4 on t1."DptCode" = t4."code"
where t1."StatusDelete" = 0 ORDER BY t1."RepairID" DESC limit 15 offset 0';
$result = pg_query($Con, $qry);

$qryCountData = 'Select count("RepairID") as countdata from "rp_Repair_Notify" where "StatusDelete" = 0;';
$res = pg_query($Con, $qryCountData);
$dt = pg_fetch_assoc($res);
$countData = $dt["countdata"];


if ($result) {
  while ($row = pg_fetch_assoc($result)) {
    $data[] = $row; // ✅ ดึงทุกแถวเก็บไว้ใน array
  }
  echo json_encode([
    'data' => $data,
    'countdata' => $countData
  ]);
} else {
  echo "Query ล้มเหลว";
}
?>
