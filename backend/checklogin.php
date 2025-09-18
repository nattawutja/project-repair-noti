<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    require_once 'db.php';

    $inputJSON = file_get_contents('php://input');
    $dt = json_decode($inputJSON, true);

    $username = $dt['username'] ?? '';
    $password = $dt['password'] ?? '';

    $qry = 'SELECT concat(t1."firstName",\' \',t1."lastName") as fullname,t1."id",t1."department_code",t1."division_code",t2."name",t1."password"  FROM "User" t1
    left join "Division" t2 on t1."division_code" = t2."code"
    left join "Department" t3 on t1."department_code" = t3."code"
    WHERE t1."employeeId" = \'' . $username . '\'';
    $res = pg_query($Con,$qry);
    if(pg_num_rows($res) > 0){
        $dt = pg_fetch_assoc($res);
        $passwordHash = $dt["password"];
        $fullname = $dt["fullname"];
    }

    $newHash = crypt($password,$passwordHash);
    if($newHash == $passwordHash){
        $status = "Success";
    }else{
        $status = "False";
    }

    echo json_encode([
        'Status' => $status,
        'fullname' => $fullname,
        'id' => $dt["id"],
        'dpt_code' => $dt["department_code"],
        'dvi_code' => $dt["division_code"],
        'name_dvi' => $dt["name"]
    ]);

    pg_close($Con);
?>