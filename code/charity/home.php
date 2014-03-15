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
            <h1><?=$headers['Header1']?></h1>
          </div>
        <div class="col-md-9">
            
            <p class="lead"><?=$content['text1']?></p>
            <?php
            //remove 
            $numItems = count($headers);
            if(count($content) > $numItems){
                $numItems = count($content);
            }
            for($i=2; $i < $numItems; $i++){//start from 2 to skip content that has already been output
                if(isset($headers['Header' . $i]))//if the header exists and != null
                    echo '<h2>' . $headers['Header' . $i] . '</h2>';
                if(isset($content['text' . $i]))//if the content exists and != null
                    echo '<p>' . $content['text' . $i] . '</p>';
            }
            
            ?>
        </div>
          <?php
          require_once 'sidebar.php';
          ?>
          </div>
      </div>

<?php

require_once 'footer.php';
