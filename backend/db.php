<?php 
    $host = 'mis-warin';     // ชื่อ Host หรือ IP ที่คุณตั้งไว้ (ถ้าใน network เดียวกัน หรือ localhost ก็ตั้ง localhost)
    $dbname = 'as400_import_db';     // ชื่อฐานข้อมูลจริงที่คุณสร้างไว้ใน PostgreSQL
    $user = 'postgres';      // username ตามที่ตั้งไว้
    $password = 'mis2001';   // รหัสผ่านที่ตั้งไว้

    $conn_string = "host=$host dbname=$dbname user=$user password=$password";
 
    $Con = pg_connect($conn_string);
?>