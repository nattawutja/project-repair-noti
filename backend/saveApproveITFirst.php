<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';
$date = new DateTime("now", new DateTimeZone('Asia/Bangkok'));

$RepairID = intval($_GET['idrp']); // แปลงเป็นเลขจำนวนเต็ม
$description = $_GET['description'];
$fullname = $_GET['fullname'];
$user_id = $_GET['user_id'];
$process = $_GET['process'];

$qryApprove = 'INSERT INTO "rp_Repair_Notify_Approve" ("RepairID","fullName","Approve","pos_id","user_id","description","ProcessDate") VALUES (';
$qryApprove .= '' . $RepairID . ',';
$qryApprove .= '\'' . $fullname . '\',';
$qryApprove .= '\'' . "O" . '\',';
$qryApprove .= '' . 0 . ',';
$qryApprove .= '\'' . $user_id . '\',';
$qryApprove .= '\'' . $description . '\'';
$qryApprove .= '\'' . $date -> format('Y-m-d H:i:s') . '\'';
$qryApprove .= ');';
//pg_query($Con, $qryApprove);

$qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 1 . ' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . ' AND "user_id_IT" = \'' . $user_id . '\'';
//pg_query($Con, $qryUptTableMain);

echo json_encode([
    'success' => true,
    'qry' => $qryApprove,
    'qryUptMain' => $qryUptTableMain,
    'Status' => $process
]);

pg_close($Con);

?>
