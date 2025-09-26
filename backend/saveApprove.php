<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';
$date = new DateTime("now", new DateTimeZone('Asia/Bangkok'));

$ApproveID = intval($_GET['id']); // แปลงเป็นเลขจำนวนเต็ม
$RepairID = intval($_GET['idrp']); // แปลงเป็นเลขจำนวนเต็ม
$posid = intval($_GET['posid']); // แปลงเป็นเลขจำนวนเต็ม

$qryApprove = 'UPDATE "rp_Repair_Notify_Approve" SET "Approve" = \'' . "Y" . '\',"ApproveDate" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "ApproveID" = ' . $ApproveID . ' AND "RepairID" = ' . $RepairID . '';
pg_query($Con, $qryApprove);


$qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 5 . ' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . ' ';
pg_query($Con, $qryUptTableMain);

echo json_encode([
    'success' => true
]);


pg_close($Con);

?>
