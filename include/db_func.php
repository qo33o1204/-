<?php
function connect2db($dbhost, $dbuser, $dbpwd, $dbname) {
    $dsn = "mysql:host=$dbhost;dbname=$dbname";
    try {
        $db_conn = new PDO($dsn, $dbuser, $dbpwd);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        die ("錯誤: 無法連接到資料庫");
    }
    $db_conn->query("SET NAMES UTF8");
    return $db_conn;
}

function updatedb($updatestr, $conn_id) {
    try {
        $result = $conn_id->query($updatestr); 
    } catch (PDOException $e) {
        echo $e->getMessage();
        die ("資料庫異動失敗，請重試，若問題仍在，請通知管理單位。");
    }
    return $result;
}
function querydb($querystr, $conn_id) {
    try {
        $result = $conn_id->query($querystr);
    } catch (PDOException $e) {
        die ("$querystr 資料庫查詢失敗，請重試，若問題仍在，請通知管理單位。");
    }
    $rs = array();
    if ($result) $rs = $result->fetchall(); 
    return $rs;
}

function sql_limit($count, $offset) {
    return " LIMIT $offset,$count ";
}

function newid($db) {
    return $db->lastInsertId();;
}

function GetInsertID($db) {
    return $db->lastInsertId();;
}

function xssfix($InString) {
    return htmlspecialchars($InString);
}
?>
