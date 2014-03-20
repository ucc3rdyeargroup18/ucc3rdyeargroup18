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
          <h1>Lost Chickens</h1>
        </div>
          <div class="col-md-9">
        <p class="lead">Below is a list of chickens currently missing from their coops</p>
        <p>If you have lost or found a hen, please report it on our <a href="submitLost">Lost and Found</a> page</p>
        <hr />
        <div class="container marketing">

      <!-- Three columns of text below the carousel -->
      <!--div class="row"-->
          <?php 
          $count = 0;
          //XXX temporary array to test foreach
//          $lostAndFound = array(
//              array(
//                  'animalPermaID' => 'Clucker',
//                  'img' => 'images/clucker.jpg',
//                  'animalName' => 'Clucker',
//                  'animalInfo' => '<p>Clucker is missing from his home in Clonakilty. There are fears he may have been an imposter</p>'
//              )
//          );
          //$lostAndFound[] = $lostAndFound[0];$lostAndFound[] = $lostAndFound[0];$lostAndFound[] = $lostAndFound[0];$lostAndFound[] = $lostAndFound[0];
          //TODO update to use new databse
          
        $getLostPetsSQL = "SELECT Name, Details, Image1, LostFoundID, LastSeen FROM CMS_LostFounds WHERE CharityID = {$info['CharityID']} AND isLost = 1 ORDER BY CreatedOn DESC";
        $result = mysql_query($getLostPetsSQL);
        while($row = mysql_fetch_assoc($result))
        {
            $content[] = $row;
        }
                    
        foreach($content as $animal){
            //var_dump($animal);
            if($count%2 == 0){
                echo '<div class="row lost-found-row">';
            }
            echo '<div class="col-lg-4">
              <img class="img-circle" data-src="holder.js/140x140" alt="140x140" style="width: 140px; height: 140px;" src="/';
            echo 'images/lostAndFound/' . $animal['Image1'];
            echo '">
              <h2>';
            echo $animal['Name']; //print the animals name
            echo'</h2>';
             $date = date_parse_from_format("Y-m-d H:i:s", $animal['LastSeen']);
             $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
             $animal['LastSeen'] = date("d/m/Y @ H:i", $time);
              echo "Last Seen: " . $animal['LastSeen'] . '<br />';
              echo $animal['Details'];
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
