<?php
//header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'db.php';
$data = [];
// ทดสอบ query ง่าย ๆ
$qry = 'SELECT * FROM "Department" order by "id"';
$result = pg_query($Con, $qry);
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
      $data[] = $row;
    }
    echo json_encode($data);
}

pg_close($Con);
?>
