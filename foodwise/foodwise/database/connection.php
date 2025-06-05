<?php
function connessione()
{

    $host = "aws-0-eu-central-1.pooler.supabase.com";
    $port = "6543";
    $dbname = "postgres";
    $user = "postgres.jfshzxaoiazolfzuzism";
    $password = "a4FWImTD4BR9z1vQ";

    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

    if (!$conn) {
        die(json_encode(["success" => false, "message" => "Connessione fallita: " . pg_last_error()]));
    }

    return $conn;
}
?>