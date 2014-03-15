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

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>Events</h1>
        </div>
          <div class="col-md-9">
        <p class="lead">Below is a list upcoming events</p>
        <p>If you are arranging an event for us, please post it on the <a href="#lost">Events</a> page</p>
        <hr />
        <div class="container marketing">

      <!-- Three columns of text below the carousel -->
      <!--div class="row"-->
          <?php 
          $count = 0;

        $getEventsSQL = "SELECT Name, Description, DATE_FORMAT(Start, '%e-%b-%y %H:i') AS Start, DATE_FORMAT(End, '%e-%b-%y %H:i') AS End, Location, Image1, ID FROM CMS_Events WHERE CharityID = {$info['CharityID']} AND End > NOW() ORDER BY Start";
        $result = mysql_query($getEventsSQL);
        while($row = mysql_fetch_assoc($result))
        {
            $content[] = $row;
        }
                    
        foreach($content as $event){
            //var_dump($animal);
            if($count%2 == 0){
                echo '<div class="row lost-found-row">';
            }
            echo '<div class="col-lg-4">
              <img class="img-circle" data-src="holder.js/140x140" alt="140x140" style="width: 140px; height: 140px;" src="/';
            //TODO change image src to BLOB ($animal['Image1'])
            //echo $animal['Image1']; //display the image of the missing animal
            //echo data_uri($animal['Image1'], 'image/jpeg');
            echo 'images/clucker.jpg';
            echo '">
              <h2>';
            echo $event['Name']; //print the animals name
            echo'</h2>';
              echo $event['Description'];
              echo '</br>';
              echo 'Location: ' + $event['Location'];
              echo '</br>';
              echo $event['start'] + '-' + $event['end'];
              echo '<p><a class="btn btn-default" href="lostAndFound/';
              //echo $animal['animalPermaID'];
              echo '" role="button">View details »</a></p>
            </div><!-- /.col-lg-4 -->';
              if($count%2 == 1){
                  echo '</div>';
              }
              $count++;
              
        }
        
        if($count%2 == 1){ //final row is unclosed
            echo '</div>';
        } 
          ?>
      <!--/div><!-- /.row -->

    </div> <!-- /.container marketing -->
          </div> <!-- /.col-md-9 -->
          <?php
          require_once 'sidebar.php';
          ?>
      </div> <!-- /.container -->

<?php

require_once 'footer.php';

function data_uri($file, $mime) 
{  
  //$contents = file_get_contents($file);
  $base64   = base64_encode($file); 
  return ('data:' . $mime . ';base64,' . $base64);
}
