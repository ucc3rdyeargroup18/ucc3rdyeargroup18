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
    $getEventSQL = "SELECT cms_events.Name, Start1, End1, Description, Contact, Number1, Email, Location, Image1, Image2, CreatedOn, EditedOn, Concat(Creators.FirstName, ' ', Creators.LastName) AS Creator, Concat(Editors.FirstName , ' ', Editors.LastName) AS Editor "
            . "FROM CMS_Events, (SELECT UserID, FirstName, LastName FROM CMS_Users) Creators, (SELECT UserID, FirstName, LastName FROM CMS_Users) Editors "
            . "WHERE EventID = {$_GET['eventID']} AND Creators.UserID = CreatorID AND Editors.UserID = EditorID";
    $result = mysql_query($getEventSQL);
    $event = mysql_fetch_assoc($result);
    echo '<div class="container">';
        echo '<div class="page-header">';
            echo '<h1>' . $event['Name'] . '</h1>';
        echo '</div>';
        echo '<div class="col-md-9">';
            echo '<p class="lead">';
                $date = date_parse_from_format("Y-m-d H:i:s", $event['Start1']);
                $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
                $event['Start1'] = date("H:i, d M Y", $time);
                
                $date = date_parse_from_format("Y-m-d H:i:s", $event['End1']);
                $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
                $event['End1'] = date("H:i, d M Y", $time);
                
                echo $event['Start1'] . ' - ' . $event['End1'] . '<br />' . $event['Location'];
            echo '</p>';
            echo '<hr />';
            echo '<div class="col-md-12 container marketing">';
            if($event['Image2'] != null)
            {
                echo '<img class="img-circle pull-left" src="/images/events/'. $event['Image1'] . '" style="max-width:40%;">';
                echo '<img class="img-circle pull-right" src="/images/events/'. $event['Image2'] . '" style="max-width:40%;">';
            } 
            else
            {
                echo '<img class="img-circle" src="/images/events/'. $event['Image1'] . '" style="max-width:100%;">';  
            }
                echo '<br class="clear-both" /><hr class="clear-both">';
                echo '<p class="well clear-both">';
                    echo $event['Description'];
                echo '</p>';
                echo '<p>';
                    echo 'Contact: ' . $event['Contact'] . ' on ' . $event['Number1'] . ' or email ' . $event['Email'] . ' for more information';
                echo '</p>';
                echo '<p>';
                    echo 'Created by: ' . $event['Creator'] . ' on ' . $event['CreatedOn'] . '<br />Last edited by ' . $event['Editor'] . ' on ' . $event['EditedOn'];
                echo '</p>';
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
