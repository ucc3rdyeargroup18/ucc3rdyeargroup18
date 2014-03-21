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

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
            <h1>Stories</h1>
        </div>
          <div class="col-md-9">
        <p class="lead">Below is a list of stories for this charity</p>
        <p>A story is a number of related posts</p>
        <hr />
        <div class="container marketing">
            
  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.9.1.js"></script>
  <script>
  $(function() {
    $( "#accordion" ).accordion();
  });
  </script>
      <!-- Three columns of text below the carousel -->
      <!--div class="row"-->
          <?php 
          $tables = array(
              array("CMS_Events", "EventID", "eventDetails.php", "eventID"),
              array("CMS_LostFounds", "LostFoundID", "foundDetails.php", "foundID"),
              array("CMS_LostFounds", "LostFoundID", "lostDetails.php", "lostID"),
              array("CMS_Pets", "PetID", "residentDetails.php", "residentID"),
              array("CMS_Pets", "PetID", "adoptableDetails.php", "adoptableID"),
          );
          
            echo '<div id="accordian">';
        $getStoriesSQL = "SELECT Title, StoryID FROM CMS_Stories WHERE CharityID = {$info['CharityID']} ORDER BY Title DESC";
        $result = mysql_query($getStoriesSQL);
        while($row = mysql_fetch_assoc($result))
        {
            $content[] = $row;
        }
                    
        foreach($content as $story){
            echo '<h3>' . $story['Title'] . '</h3>';
            echo '<div>';
                $getContentSQL = "SELECT CMS_StoryContent.ContentID, TableName, ReferenceID FROM CMS_StoryContent, CMS_Content WHERE StoryID = {$story['StoryID']} ";
                $result = mysql_query($getContentSQL);
                while($contentRow = mysql_fetch_assoc($result))
                {
                    $postContent[] = $contentRow;
                }
                foreach($postContent as $post)
                {
                    for($i = 0; $i< count($tables); $i++)
                    {
                        if($post['TableName'] == $tables[$i][0])
                        {
                            $table[] = $tables[$i];
                            break;
                        }
                    }
                    $getPostSQL = "SELECT Name FROM {$post['TableName']} WHERE {$table[1]} = {$post['ReferenceID']}";
                    $result = mysql_query($getPostSQL);
                    while($nameRow = mysql_fetch_assoc($result))
                    {
                        $names[] = $nameRow;
                    }
                    echo '<li>';
                    echo '<a href="'. $table[2] . '?' . $table[3] . '=' . $post['ReferenceID'] . '">' . $names['Name'] . '</a>';
                    echo '</li>';
                }
            echo '</div>';
        }
        echo '</div>'
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
