<?php
//header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';
$data = [];
// ทดสอบ query ง่าย ๆ
$qry = 'SELECT * FROM "Master_Device_Type" where "StatusDelete" = 0 order by "id"';
$result = pg_query($Con, $qry);
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
      $data[] = $row; // ✅ ดึงทุกแถวเก็บไว้ใน array
      //echo "test";
    }
    echo json_encode($data);
} else {
  echo "Query ล้มเหลว";
}
?>
