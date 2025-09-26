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

$qryApprove = '';

if($process == "Y"){
    $qryApprove = 'INSERT INTO "rp_Repair_Notify_Approve" ("RepairID","fullName","Approve","pos_id","user_id","description","ProcessDate","ApproveDate") VALUES (';
    $qryApprove .= '' . $RepairID . ',';
    $qryApprove .= '\'' . $fullname . '\',';
    $qryApprove .= '\'' . "Y" . '\',';
    $qryApprove .= '' . 0 . ',';
    $qryApprove .= '\'' . $user_id . '\',';
    $qryApprove .= '\'' . $description . '\',';
    $qryApprove .= '\'' . $date -> format('Y-m-d H:i:s') . '\',';
    $qryApprove .= '\'' . $date -> format('Y-m-d H:i:s') . '\'';
    $qryApprove .= ');';

    $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 4 . ' ,"user_id_IT" = \'' . $user_id . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . '';


}else{
    $qryApprove = 'INSERT INTO "rp_Repair_Notify_Approve" ("RepairID","fullName","Approve","pos_id","user_id","description","ProcessDate") VALUES (';
    $qryApprove .= '' . $RepairID . ',';
    $qryApprove .= '\'' . $fullname . '\',';
    if($process == "S"){
        $qryApprove .= '\'' . "S" . '\',';
    }else if($process == "W"){
        $qryApprove .= '\'' . "W" . '\',';
    }else{
        $qryApprove .= '\'' . "O" . '\',';
    }
    $qryApprove .= '' . 0 . ',';
    $qryApprove .= '\'' . $user_id . '\',';
    $qryApprove .= '\'' . $description . '\',';
    $qryApprove .= '\'' . $date -> format('Y-m-d H:i:s') . '\'';
    $qryApprove .= ');';

    if($process == "S"){
        $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 2 . ',"user_id_IT" = \'' . $user_id . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . '';

    }else if($process == "W"){
        $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 3 . ',"user_id_IT" = \'' . $user_id . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . '';

    }else{
        $qryUptTableMain = 'UPDATE "rp_Repair_Notify" SET "StatusWork" = ' . 1 . ',"user_id_IT" = \'' . $user_id . '\' where "StatusDelete" = 0 AND "RepairID" = ' . $RepairID . '';

    }

}

pg_query($Con, $qryApprove);
pg_query($Con, $qryUptTableMain);

echo json_encode([
    'success' => true,
    'qry' => $qryApprove,
    'qryUptMain' => $qryUptTableMain,
    'Status' => $process,
    'UserID' => $user_id
]);

pg_close($Con);

?>
