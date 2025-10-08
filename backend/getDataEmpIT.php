<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require 'db.php';
$data = [];
$qryEmpIT = 'WITH cte as (SELECT array_agg(concat("firstName",\' \',"lastName")) AS names FROM "User"
WHERE "position" = \'เจ้าหน้าที่\' and "department" = \'MIS\'
GROUP BY "position")
SELECT * from cte';

$res = pg_query($Con,$qryEmpIT);
if(pg_num_rows($res) > 0){
 while($dt = pg_fetch_assoc($res)){
    $substrname = str_replace(['{','}'],'',$dt["names"]);
    $substrname = str_replace('"', '', $substrname); 
    $data[] = $substrname;
 }
}

echo json_encode([
    'success' => true,
    'data' => $data
]);


pg_close($Con);

?>
