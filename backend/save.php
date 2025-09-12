<?php
header("Access-Control-Allow-Origin: *");  // อนุญาตทุกโดเมน
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // กำหนด method ที่อนุญาต
header("Access-Control-Allow-Headers: Content-Type"); // กำหนด header ที่อนุญาต
header("Content-Type: application/json");

include 'db.php';

$date = new DateTime("now", new DateTimeZone('Asia/Bangkok'));


$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
$RepairNo = '';
$RepairNoWhere = '';

$explodeDate = explode("-",$input["tbDateNoti"]);

$year = $explodeDate[0];

$substrYear = substr($year,2);

$month = $explodeDate[1];


$RepairNoWhere .= "RP" . $substrYear . "/" . $month;

$qryNo = 'SELECT Distinct("RepairNo") FROM "rp_Repair_Notify" 
WHERE "RepairNo" LIKE \'' . $RepairNoWhere . '%\' and "StatusDelete" = 0
ORDER BY "RepairNo" DESC 
LIMIT 1';

$resDocno = pg_query($Con,$qryNo);

if(pg_num_rows($resDocno) > 0){
    while($dtDocno = pg_fetch_assoc($resDocno)){
        $DocNoLates =  substr($dtDocno["RepairNo"], -4) + 1;
        $runningStr = str_pad($DocNoLates, 4, "0", STR_PAD_LEFT);
        $RepairNo = "RP" . $substrYear . "/" . $month . $runningStr;
    }
}else{
    $RepairNo = "RP" . $substrYear . "/" . $month . "0001";
}


$qry = 'insert into "rp_Repair_Notify"("RepairNo","RepairNotifyDate","DptCode","DptName","EmpName","SystemType","DeviceToolID","OtherTool","Model","ToolAssetID","description","create_by","create_date","DeviceTypeID") values(';
$qry .= "'" . $RepairNo . "',"; 
if($input["tbDateNoti"] != ""){
    $qry .= "'" . $input["tbDateNoti"] . "',";   
}
if($input["tbDptCode"] != ""){
    $qry .= "'" . $input["tbDptCode"] . "',";   
}
if($input["tbDptName"] != ""){
    $qry .= "'" . $input["tbDptName"] . "',";   
}
if($input["tbNameEmp"] != ""){
    $qry .= "'" . $input["tbNameEmp"] . "',";   
}
if($input["tbSystemType"] != ""){
    $qry .= "'" . $input["tbSystemType"] . "',";   
}
if($input["tbToolNumber"] != ""){
    $qry .= "'" . $input["tbToolNumber"] . "',";   
}

if($input["tbOtherTool"] != ""){
    $qry .= "'" . $input["tbOtherTool"] . "',";   
}else{
    $qry .= "'',";
}
if($input["tbModel"] != ""){
    $qry .= "'" . $input["tbModel"] . "',";   
}
if($input["tbAssetID"] != ""){
    $qry .= "'" . $input["tbAssetID"] . "',";   
}
if($input["tbDesc"] != ""){
    $qry .= "'" . $input["tbDesc"] . "',";   
}
$qry .= "" . 1 . ",";   
$qry .= "'" . $date->format('Y-m-d H:i:s') . "',";   
if($input["tbTool"] != ""){
    $qry .= "" . $input["tbTool"] . "";   
}
$qry .= ') RETURNING "RepairID";';

$res = pg_query($Con,$qry);

if(pg_num_rows($res) > 0){
    $dt = pg_fetch_assoc($res);

    $qryIT = 'select concat("firstName",\' \',"lastName") as fullname from "User" WHERE "employeeId" = \'EMP001\' ';
    $resIT = pg_query($Con,$qryIT);
    if(pg_num_rows($resIT) > 0){
        $dtMemberIT = pg_fetch_assoc($resIT);
        $fullNameIT = $dtMemberIT["fullname"];
    }

    $qryUser = 'select concat("firstName",\' \',"lastName") as fullname from "User" WHERE "employeeId" = \'EMP002\' ';
    $resUser = pg_query($Con,$qryUser);
    if(pg_num_rows($resUser) > 0){
        $dtMemberUser = pg_fetch_assoc($resUser);
        $fullNameUser = $dtMemberUser["fullname"];
    }


    $qryApprove = 'INSERT INTO "rp_Repair_Notify_Approve"("RepairID","fullName") VALUES('. $dt["RepairID"] .',\'' . $fullNameIT . '\');';
    $resInsApproveIT = pg_query($Con,$qryApprove);

    $qryInsApproveUser = 'INSERT INTO "rp_Repair_Notify_Approve"("RepairID","fullName") VALUES('. $dt["RepairID"] .',\'' . $fullNameUser . '\');';
    $resInsApproveUser = pg_query($Con,$qryInsApproveUser);
}

echo json_encode([
    'success' => true,
    'received' => $qryApprove
]);

?>
