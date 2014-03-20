<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );

require_once 'header.php';
?>
<?php
    $getExistingPagesSQL = "SELECT PageID, CustomTitle, CharityName, Pending "
            . "FROM CMS_CharityPages, CMS_Pages, CMS_Charities, CMS_AccessRequests "
            . "WHERE CMS_AccessRequests.CharityID = CMS_Charities.CharityID"
            . "AND CMS_Charities.CharityID = CMS_CharityPages.CharityID"
            . "AND CMS_AccessRequests.UserID = {$_SESSION['UserID']}"
            . "ORDER BY CharityName";
    $result = mysql_query($getExistingPagesSQL);
    while($row = mysql_fetch_assoc($result))
    {
        $content[] = $row;
    }
    
    foreach($content as $page)
    {
        echo '<div class="row">';
            echo '<div class="col-lg-6">';
                echo '<label for="' . $page["PageID"] . '">' . $page["CustomTitle"] . '</label>';
                echo '<div class="input-group">';
                    echo '<span class="input-group-addon">';
                        echo '<input type="checkbox" id="box' . $page["PageID"] . '" type="checkbox"  name=isUsed[' . $page["PageID"] . ']' ;
                        echo $page['Pending'] ? '' :' checked' ;
                    echo '</span>';
                    echo '<input class="form-control" id="' . $page["PageID"] . '" type="text" value="'. $page["CustomTitle"] .'"name="customTitle[' . $page["PageID"] . ']" />';
                    echo '<input type="hidden" name="name[' . $page["PageID"] . ']" value="' . $page["Name"] . '" />  ';  
                echo '</div><!-- /input-group -->';
            echo '</div><!-- /.col-lg-6 -->';
          echo '</div><!-- /.row -->';
    }
require_once 'footer.php';

function data_uri($file, $mime) 
{  
  //$contents = file_get_contents($file);
  $base64   = base64_encode($file); 
  return ('data:' . $mime . ';base64,' . $base64);
}
