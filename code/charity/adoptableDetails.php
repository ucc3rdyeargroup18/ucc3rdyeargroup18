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
    $getadoptableSQL = "SELECT cms_pets.Name, Description, Contact, Number1, Email, Location, Image1, Image2, CreatedOn, EditedOn, Concat(Creators.FirstName, ' ', Creators.LastName) AS Creator, Concat(Editors.FirstName , ' ', Editors.LastName) AS Editor "
            . "FROM CMS_pets, (SELECT UserID, FirstName, LastName FROM CMS_Users) Creators, (SELECT UserID, FirstName, LastName FROM CMS_Users) Editors "
            . "WHERE PetID = {$_GET['adoptableID']} AND Creators.UserID = CreatorID AND Editors.UserID = EditorID";
    $result = mysql_query($getadoptableSQL);
    $adoptable = mysql_fetch_assoc($result);
    echo '<div class="container">';
        echo '<div class="page-header">';
            echo '<h1>' . $adoptable['Name'] . '</h1>';
        echo '</div>';
        echo '<div class="col-md-9">';
            echo '<p class="lead">Location: ';
                echo $adoptable['Location'];
            echo '</p>';
            echo '<hr />';
            echo '<div class="col-md-12 container marketing">';
            if($adoptable['Image2'] != null)
            {
                echo '<img class="pull-left" src="/images/pets/'. $adoptable['Image1'] . '" style="max-width:40%;">';
                echo '<img class="pull-right" src="/images/pets/'. $adoptable['Image2'] . '" style="max-width:40%;">';
            } 
            else
            {
                echo '<img class="center" src="/images/pets/'. $adoptable['Image1'] . '" style="max-width:100%; max-height: 30em;">';  
            }
                echo '<br class="clear-both" /><hr class="clear-both">';
                echo '<p class="well clear-both">';
                    echo '<b>Description</b><br />';
                    echo $adoptable['Description'];
                echo '</p>';
                echo '<p>';
                    echo 'Contact: ' . $adoptable['Contact'] . ' on ' . $adoptable['Number1'] . ' or email ' . $adoptable['Email'];
                echo '</p>';
                echo '<em class="text-muted">';
                    echo 'Created by: ' . $adoptable['Creator'] . ' on ' . $adoptable['CreatedOn'] . '<br />Last edited by ' . $adoptable['Editor'] . ' on ' . $adoptable['EditedOn'];
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
