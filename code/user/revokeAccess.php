<?php

$removeID = filter_input(INPUT_POST, 'remove', FILTER_VALIDATE_INT);
if(!$removeID){
    echo "false1";
    die();
}
$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
require_once 'database_functions.php';
connect_to_database();

session_start();

$checkSQL = "SELECT * FROM cms_accessrequest WHERE UserID = {$_SESSION['userID']} AND ID = {$removeID};";
$checkResult = mysql_query($checkSQL);
if($checkResult && mysql_num_rows($checkResult) == 1){
    $removeSQL = "DELETE FROM cms_accessrequest WHERE ID = {$removeID};";
    $removeResult = mysql_query($removeSQL);
    if($removeResult){
        echo "true";
    } else{
        echo "false2";
    }
} else{
    echo "false3";
}

