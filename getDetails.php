<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'database_functions.php';
connect_to_database();
//Ensure we are connected to the database

$charityDomain = filter_input(INPUT_GET, 'domain', FILTER_SANITIZE_URL);
$charityPageRaw = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
$charityPageArray1 = explode('/', $charityPageRaw);
$arraySize = count($charityPageArray1);
$charityPageExtension = $charityPageArray1[$arraySize-1];
$charityPageArray2 = explode('.', $charityPageExtension);;
$charityPage = $charityPageArray2[0];

$charityInfoSQL = "SELECT * FROM cms_charities WHERE DomainName = '{$charityDomain}';";
$charityInfoResult = mysql_query($charityInfoSQL);
if(!$charityInfoResult || mysql_num_rows($charityInfoResult) != 1){ //TODO output a user friendly error page!
    echo mysql_error();
    exit();
}

while($row = mysql_fetch_assoc($charityInfoResult)){
    $info = $row;
}  

$headersSQL = "SELECT * FROM cms_charitypageheaders "
            . "WHERE CharityID = {$info['CharityID']} "
            . "AND PageID = "
                . "(SELECT PageID FROM cms_pages "
                . "WHERE FileName = '{$charityPage}');";
$headersReuslt = mysql_query($headersSQL);
if($headersReuslt && mysql_num_rows($headersReuslt) == 1){
    while($row = mysql_fetch_assoc($headersReuslt)){
        $headers = $row;
    }
}

//$navSQL = "SELECT * FROM cms_charitypages WHERE CharityID = {$info['CharityID']};";
$navSQL = "SELECT CustomTitle, FileName, Name FROM cms_pages, cms_charitypages WHERE cms_pages.PageID = cms_charitypages.PageID AND CharityID = {$info['CharityID']};";
$navResult = mysql_query($navSQL);

if(!$navResult || mysql_num_rows($navResult) < 1){ //TODO output a user friendly error page!
    echo mysql_error();
    exit();
}

while($row = mysql_fetch_assoc($navResult)){
    $info['nav'][] = $row;
}
