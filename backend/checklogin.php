<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    require_once 'db.php';

    $inputJSON = file_get_contents('php://input');
    $dt = json_decode($inputJSON, true);

    $username = $dt['username'] ?? '';
    $password = $dt['password'] ?? '';

    $qry = 'SELECT concat("firstName",\' \',"lastName") as fullname,* From "User" WHERE "employeeId" = \'' . $username . '\'';
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
        'id' => $dt["id"]

    ]);

    pg_close($Con);
?>