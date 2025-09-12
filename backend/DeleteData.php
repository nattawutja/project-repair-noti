<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 86400");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';

$repairID = intval($_GET['id']); // แปลงเป็นเลขจำนวนเต็ม

$qry = 'UPDATE "rp_Repair_Notify" SET "StatusDelete" = 1 where "StatusDelete" = 0 AND "RepairID" = ' . $repairID . '';
pg_query($Con, $qry);

pg_close($Con);

?>
