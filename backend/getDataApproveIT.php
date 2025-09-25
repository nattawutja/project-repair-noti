<?php
ob_start();
header("Access-Control-Allow-Origin: *");              // อนุญาตทุก origin (จะเปลี่ยนเป็นชื่อโดเมนเฉพาะก็ได้)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");  // กำหนด method ที่อนุญาต
header("Access-Control-Allow-Headers: Content-Type");  // อนุญาต header Content-Type
header("Content-Type: application/json");

require_once 'db.php';
$repairID = $_GET["id"];
$data = [];
$qry = 'select "description","user_id","pos_id","ApproveID","RepairID","fullName","Approve",to_char("ApproveDate",\'DD/MM/YYYY\') as cvdateapprovedate,to_char("ApproveDate",\'HH24:MI:ss\') as cvdateapprovetime ,to_char("ProcessDate",\'DD/MM/YYYY\') as cvdateprocessdate,to_char("ProcessDate",\'HH24:MI:ss\') as cvdateprocesstime from "rp_Repair_Notify_Approve"
where "RepairID" = ' . $repairID . ' AND "pos_id" = 0 order by "ApproveID","ApproveDate"';
$result = pg_query($Con, $qry);
if ($result) {
  while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
  }
  echo json_encode($data);
} else {
  echo "Query ล้มเหลว";
}

pg_close($Con);
?>
