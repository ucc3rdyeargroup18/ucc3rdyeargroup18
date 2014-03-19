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
            <h1><?=$info['Name']?></h1>
          </div>
        <div class="col-md-9">
            <?php
                  echo '<p>' . $info['Description'] . '<p>';
            ?>            
        </div>
          <?php
          require_once 'sidebar.php';
          ?>
          </div>
      </div>

<?php

require_once 'footer.php';
