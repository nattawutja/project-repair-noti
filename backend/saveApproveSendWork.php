<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';
$date = new DateTime("now", new DateTimeZone('Asia/Bangkok'));

$ApproveID = intval($_GET['id']); // แปลงเป็นเลขจำนวนเต็ม
$RepairID = intval($_GET['idrp']); // แปลงเป็นเลขจำนวนเต็ม
$posid = intval($_GET['posid']); // แปลงเป็นเลขจำนวนเต็ม
$process = $_GET['process'];

if($process == "Y"){
    $qryApprove = 'UPDATE "rp_Repair_Notify_Approve" SET "Approve" = \'' . "Y" . '\',"ApproveDate" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "ApproveID" = ' . $ApproveID . ' AND "RepairID" = ' . $RepairID . ' AND "pos_id" = ' . $posid . ' ';
}else if($process == "W"){
    $qryApprove = 'UPDATE "rp_Repair_Notify_Approve" SET "Approve" = \'' . "W" . '\',"ProcessDate" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "ApproveID" = ' . $ApproveID . ' AND "RepairID" = ' . $RepairID . ' AND "pos_id" = ' . $posid . ' ';
}else if($process == "S"){
    $qryApprove = 'UPDATE "rp_Repair_Notify_Approve" SET "Approve" = \'' . "S" . '\',"ProcessDate" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "ApproveID" = ' . $ApproveID . ' AND "RepairID" = ' . $RepairID . ' AND "pos_id" = ' . $posid . ' ';
}else{
    $qryApprove = 'UPDATE "rp_Repair_Notify_Approve" SET "Approve" = \'' . "O" . '\',"ProcessDate" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "ApproveID" = ' . $ApproveID . ' AND "RepairID" = ' . $RepairID . ' AND "pos_id" = ' . $posid . ' ';
}

if($process == "Y"){
    $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 4 . ',"send_work_date" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . ' ';
}else if($process == "W"){
    $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 3 . ',"send_work_date" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . ' ';
}else if($process == "S"){
    $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 2 . ',"send_work_date" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . ' ';
}else{
    $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 1 . ',"send_work_date" = \'' . $date -> format('Y-m-d H:i:s') . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . ' ';
}

pg_query($Con, $qryApprove);
pg_query($Con, $qryUptTableMain);

echo json_encode([
    'success' => true,
    'qryApprove' => $qryApprove,
    'process' => $process,
    'qryUptTableMain' => $qryUptTableMain
]);


pg_close($Con);

?>
