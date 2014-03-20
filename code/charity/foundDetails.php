<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );

require_once 'header.php';

if(isset($_GET['anml'])){
    //output the page for that animal
}

?>
<?php
    $getLostSQL = "SELECT cms_lostfounds.Name, LastSeen, Description, Contact, Number1, Email, Details, Image1, Image2, CreatedOn, EditedOn, Concat(Creators.FirstName, ' ', Creators.LastName) AS Creator, Concat(Editors.FirstName , ' ', Editors.LastName) AS Editor "
            . "FROM CMS_LostFounds, (SELECT UserID, FirstName, LastName FROM CMS_Users) Creators, (SELECT UserID, FirstName, LastName FROM CMS_Users) Editors "
            . "WHERE LostFoundID = {$_GET['foundID']} AND Creators.UserID = CreatorID AND Editors.UserID = EditorID";
    $result = mysql_query($getLostSQL);
    $lost = mysql_fetch_assoc($result);
    echo $getLostSQL;
    echo '<div class="container">';
        echo '<div class="page-header">';
            echo '<h1>' . $lost['Name'] . '</h1>';
        echo '</div>';
        echo '<div class="col-md-9">';
            echo '<p class="lead">';
                $date = date_parse_from_format("Y-m-d H:i:s", $lost['LastSeen']);
                $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
                $lost['LastSeen'] = date("H:i, d M Y", $time);
                
                echo 'Found on: ' . $lost['LastSeen'];
            echo '</p>';
            echo '<hr />';
            echo '<div class="col-md-12 container marketing">';
            if($lost['Image2'] != null)
            {
                echo '<img class="pull-left" src="/images/lostandfound/'. $lost['Image1'] . '" style="max-width:40%;">';
                echo '<img class="pull-right" src="/images/lostandfound/'. $lost['Image2'] . '" style="max-width:40%;">';
            } 
            else
            {
                echo '<img class="center" src="/images/lostandfound/'. $lost['Image1'] . '" style="max-width:100%; max-height: 30em;">';  
            }
                echo '<br class="clear-both" /><hr class="clear-both">';
                echo '<p class="well clear-both">';
                    echo '<b>Description</b><br />';
                    echo $lost['Description'];
                echo '</p>';
                echo '<p class="well clear-both">';
                    echo '<b>Details</b><br />';
                    echo $lost['Details'];
                echo '</p>';
                echo '<p>';
                    echo 'Contact: ' . $lost['Contact'] . ' on ' . $lost['Number1'] . ' or email ' . $lost['Email'];
                echo '</p>';
                echo '<em class="text-muted">';
                    echo 'Created by: ' . $lost['Creator'] . ' on ' . $lost['CreatedOn'] . '<br />Last edited by ' . $lost['Editor'] . ' on ' . $lost['EditedOn'];
                echo '</em>';
            echo '</div> <!-- /.container marketing -->';
        echo '</div> <!-- /.col-md-9 -->'; 
        require_once 'sidebar.php';
    echo '</div> <!-- /.container -->';
require_once 'footer.php';

function data_uri($file, $mime) 
{  
  //$contents = file_get_contents($file);
  $base64   = base64_encode($file); 
  return ('data:' . $mime . ';base64,' . $base64);
}
