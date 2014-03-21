<?php

$charityID = filter_input(INPUT_POST, 'charity', FILTER_VALIDATE_INT);
$pageID = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);

if(!$charityID || !$pageID){
    echo "false1";
    die();
}
$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
require_once 'database_functions.php';
connect_to_database();

session_start();

$checkSQL = "SELECT * FROM cms_accessrequest WHERE UserID = {$_SESSION['userID']} AND PageID = {$pageID} AND CharityID = {$charityID};";
$checkResult = mysql_query($checkSQL);
if($checkResult && mysql_num_rows($checkResult) == 0){
    $insertSQL = "INSERT INTO cms_accessrequest VALUES ({$_SESSION['userID']}, {$pageID}, {$charityID}, 1, null);";
    $insertResult = mysql_query($insertSQL);
    if($insertResult){
        echo "true";
    } else{
        echo "false2";
    }
} else{
    echo "already";
}

