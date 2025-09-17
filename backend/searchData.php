<?php
ob_clean(); // เคลียร์ output buffer ถ้ามีอะไรหลุดออกมาก่อน
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'db.php';

$docNo = $_GET['tbDocNoSearch'] ?? '';
$dptName = $_GET['tbDptNameSearch'] ?? '';
$dptCode = $_GET['tbDptCodeSearch'] ?? '';
$SystemType = $_GET['tbSystemTypeSearch'] ?? '';
$Tool = $_GET['tbToolSearch'] ?? '';
$ToolNumber = $_GET['tbToolNumberSearch'] ?? '';
$Model = $_GET['tbModelSearch'] ?? '';
$AssetID = $_GET['tbAssetIDSearch'] ?? '';
$StatusWork = $_GET['tbStatusWorkSearch'] ?? '';
$DateNotiStart = $_GET['tbDateNotiStartSearch'] ?? '';
$DateNotiEnd = $_GET['tbDateNotiEndSearch'] ?? '';
$EmpName = $_GET['tbEmpNameSearch'] ?? '';
$dviCode = $_GET['tbDviNameSearch'] ?? '';

$page = isset($_GET['tbpage']) ? intval($_GET['tbpage']) : 0;
$itemsPerPage = 15;
$offset = $page * $itemsPerPage;

$strWhere = '';
$strOrderBy = '';
$qry = 'select t4."name" as dviname,CASE WHEN t1."StatusWork" = 0 THEN \'รอ IT ตรวจสอบ\' WHEN t1."StatusWork" = 1 THEN \'กำลังดำเนินการ\' WHEN t1."StatusWork" = 2 THEN \'รอผู้แจ้งตรวจสอบ\' else \'จบงาน\' end as status, CASE WHEN t1."SystemType" = \'P\' THEN \'P/C\' else \'AS/400\' end as systemname,t2."name_Device",to_char(t1."create_date",\'DD/MM/YYYY HH24:MI:SS\')as cvcreatedate,t1.*,t3."name" from "rp_Repair_Notify" t1
left join "Master_Device_Type" t2 on t1."DeviceTypeID" = t2."id" and t2."StatusDelete" = 0
left join "Department" t3 on t1."DptCode" = t3."code"
left join "Division" t4 on t1."DviCode" = t4."code"
where 1=1 and t1."StatusDelete" = 0 ';

if (!empty($docNo)) {
  $strWhere .= ' AND "RepairNo"  Ilike \'%' . $docNo . '%\' ';
}

if (!empty($DateNotiStart) && !empty($DateNotiEnd)) {
  $strWhere .= ' AND "RepairNotifyDate"  between \'' . $DateNotiStart . '\' and \'' . $DateNotiEnd . '\' ';
}else if(!empty($DateNotiStart) && empty($DateNotiEnd)){
  $strWhere .= ' AND "RepairNotifyDate"  = \'' . $DateNotiStart . '\' ';
}

if (!empty($dviCode)) {
  $strWhere .= ' AND "DviCode"  = \'' . $dviCode . '\' ';
}

if (!empty($dptCode)) {
  $strWhere .= ' AND "DptCode"  Ilike \'%' . $dptCode . '%\' ';
}
if (!empty($dptName)) {
  $strWhere .= ' AND "name"  Ilike \'%' . $dptName . '%\' ';
}
if (!empty($EmpName)) {
  $strWhere .= ' AND "EmpName"  Ilike \'%' . $EmpName . '%\' ';
}
if (!empty($SystemType)) {
  $strWhere .= ' AND "SystemType" = \'' . $SystemType . '\' ';
}

if (!empty($Model)) {
  $strWhere .= ' AND "Model"  Ilike \'%' . $Model . '%\' ';
}
if (!empty($AssetID)) {
  $strWhere .= ' AND "ToolAssetID"  Ilike \'%' . $AssetID . '%\' ';
}

if (!empty($StatusWork)) {
  $strWhere .= ' AND "StatusWork"  = ' . $StatusWork . ' ';
}

if (!empty($Tool)) {
  $strWhere .= ' AND "DeviceTypeID" = ' . $Tool . ' ';
}
if (!empty($ToolNumber)) {
  $strWhere .= ' AND "DeviceToolID"  Ilike \'%' . $ToolNumber . '%\' ';
}

$strOrderBy .= ' ORDER BY t1."RepairID" DESC ';

$strLimit = 'LIMIT ' . $itemsPerPage . ' OFFSET ' . $offset;

$res = pg_query($Con, $qry . $strWhere . $strOrderBy . $strLimit);

$data = [];
$countData = 0;

$qryCountData = 'SELECT Count("RepairID") as countdata FROM "rp_Repair_Notify" t1
left join "Department" t3 on t1."DptCode" = t3."code"
left join "Division" t4 on t1."DviCode" = t4."code"
WHERE 1=1 AND "StatusDelete" = 0 ' . $strWhere ;
$resCountData = pg_query($Con, $qryCountData);
if(pg_num_rows($resCountData) > 0){
  $dtCountData = pg_fetch_assoc($resCountData);
  $countData = $dtCountData["countdata"];
}

while ($dt = pg_fetch_assoc($res)) {
  $data[] = $dt;
}

echo json_encode([
  'success' => true,
  'received' => $qry,
  'data' => $data,
  'countdata' => $countData
]);

pg_close($Con);
?>
