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
    $getEventSQL = "SELECT Name, Start1, End1, Description, Contact, Number1, Email, Location, Image1, Image2, CreatedOn, EditedOn, Creators.Name AS Creator, Editors.Name AS Editor "
            . "FROM CMS_Events, (SELECT UserID, FirstName + ' ' + LastName AS Name FROM CMS_Users) Creators, (SELECT UserID, FirstName + ' ' + LastName AS Name FROM CMS_Users) Editors "
            . "WHERE EventID = {$_GET['eventID']} AND Creators.UserID = CreatedOn AND Editors.UserID = EditedOn";
    $result = mysql_query($getHeaderSQL);
    $event = mysql_fetch_row($result);
    echo '<div class="container">';
        echo '<div class="page-header">';
            echo '<h1>' . $event['Name'] . '</h1>';
        echo '</div>';
        echo '<div class="col-md-9">';
            echo '<p class="lead">';
                echo $event['Start1'] . ' - ' . $event['End1'] . ', ' . $event['Location'];
            echo '</p>';
            echo '<hr />';
            echo '<div class="container marketing">';
            if($event['Image2'] != null)
            {
                echo '<img src="event\\'. $event['Image1'] . '" max-width="40%">';
                echo '<img src="event\\'. $event['Image2'] . '" max-width="40%">';
            } 
            else
            {
                echo '<img src="event\\'. $event['Image1'] . '" max-width="100%">';  
            }
                echo '</br>';
                echo '<p>';
                    echo $event['Description'];
                echo '</p>';
                echo '<p>';
                    echo 'Contact: ' . $event['Contact'] . ' on ' . $event['Number1'] . ' or email ' . $event['Email'] . 'for more information';
                echo '</p>';
                echo '<p>';
                    echo 'Created by: ' . $event['Creator'] . ' on ' . $event['CreatedOn'] . '; Last edited by ' . $event['Editor'] . ' on ' . $event['EditedOn'];
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
