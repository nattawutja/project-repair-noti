<?php
header("Access-Control-Allow-Origin: *");  // อนุญาตทุกโดเมน
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // กำหนด method ที่อนุญาต
header("Access-Control-Allow-Headers: Content-Type"); // กำหนด header ที่อนุญาต
header("Content-Type: application/json");

require 'db.php';

$date = new DateTime("now", new DateTimeZone('Asia/Bangkok'));

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$id = intval($input["id"]);

$qry = 'UPDATE "rp_Repair_Notify" set ';

if($input["tbDateNoti"] != ""){
    $qry .= '"RepairNotifyDate" = \'' . $input["tbDateNoti"] . '\',';   
}
if($input["tbSystemType"] != ""){
    $qry .= '"SystemType" = \'' . $input["tbSystemType"] . '\',';   
}
if($input["tbTool"] != ""){
    $qry .= '"DeviceTypeID" = ' . $input["tbTool"] . ',';   
}
if($input["tbOtherTool"] != ""){
    $qry .= '"OtherTool" = \'' . $input["tbOtherTool"] . '\',';   
}
if($input["tbToolNumber"] != ""){
    $qry .= '"DeviceToolID" = \'' . $input["tbToolNumber"] . '\',';   
}
if($input["tbModel"] != ""){
    $qry .= '"Model" = \'' . $input["tbModel"] . '\',';   
}
if($input["tbAssetID"] != ""){
    $qry .= '"ToolAssetID" = \'' . $input["tbAssetID"] . '\',';   
}
if($input["tbDesc"] != ""){
    $qry .= '"description" = \'' . $input["tbDesc"] . '\',';   
}
$qry .= '"edit_by" = \'' . $input["userID"] . '\',"edit_date" = \'' . $date->format('Y-m-d H:i:s') . '\'';   
$qry .= ' WHERE "StatusDelete" = 0 AND "RepairID" = ' . $id . ';';
$res = pg_query($Con,$qry);

pg_close($Con);

echo json_encode([
    'success' => true
]);

?>
