<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );

require_once 'header.php';

if(isset($_GET['pages'])){
    //output the page for that charity
}

?>

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>Page Selection</h1>
        </div>
          <div class="col-md-9">
        <p class="lead">Choose the pages that are relevant to this charity</p>
        <p>You can give each of them a custom name, or leave them as the default.</p>
        <hr />
        <div class="container marketing">

      <!-- Three columns of text below the carousel -->
      <!--div class="row"-->
          <?php 
          $count = 0;

        $getPagesSQL = "SELECT PageID, Name, CustomTitle, isUsed FROM CMS_CharityPages, CMS_Pages WHERE CharityID = {$info['CharityID']} AND PageID = CMS_Pages.ID ORDER BY Name";
        $result = mysql_query($getPagesSQL);
        while($row = mysql_fetch_assoc($result))
        {
            $content[] = $row;
        }
                    
        foreach($content as $page){
            echo '<input id="' . $page["PageID"] . '" type="checkbox" value="' . $page["PageID"] . '" checked="' . $page["isUsed"] . '" />';
            echo '<label for="' . $page["PageID"] . '">' . $page["name"] . '</label>';
            echo '<input id="' . $page["PageID"] . '" type="text" value="'. $page["customTitle"] .'"name="customTitle["' . $page["pageID"] . '"]"/></br>';
            
              echo '<p><a class="btn btn-default" href="lostAndFound/';
              //echo $animal['animalPermaID'];
              echo '" role="button">View details »</a></p>
            </div><!-- /.col-lg-4 -->';              
        }
    ?>
      <!--/div><!-- /.row -->
      <?php
            $page['customTitle'] = filter_input(INPUT_POST, 'customTitle', FILTER_SANITIZE_STRING);
            $page['isUsed'] = filter_input(INPUT_POST, 'customTitle', FILTER_SANITIZE_STRING);
            
            $updateSQL = "UPDATE cms_charityPages SET "
            . "CustomTitle = '{$page['CustomTitle']}', "
            . "isUsed = '{$page['isUsed']}'"
            . "WHERE CharityID = '{$info['CharityID']}'"
            . "AND PageID = '{$page['pageID']}'";
            $updateResult = mysql_query($updateSQL);
      ?>

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
