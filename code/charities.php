<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
require_once 'database_functions.php';
connect_to_database();

require_once 'header.php';
require_once 'navBar.php';

?>

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>Charity Hosting Ireland</h1>
        </div>
          <?php
            $charitiesSQL = "SELECT * FROM cms_charities WHERE CharityID > -1 ORDER BY Name ASC;";
            $charitiesResult = mysql_query($charitiesSQL);
            if (mysql_num_rows($charitiesResult) > 0) :
                $countiesSQL = "SELECT * FROM cms_counties;";
                $countiesResult = mysql_query($countiesSQL);
                $counties = array();
                while($row = mysql_fetch_assoc($countiesResult)){
                    $counties[$row['CountyID']] = $row['County'];
                }
          ?>
        <div class="table-responsive">
            <table class="table table-striped tablesorter">
              <thead>
                <tr>
                  <th>Charity</th>
                  <th>Location</th> 
                </tr>
              </thead>
              <tbody>
                  <?php
                    while ($row = mysql_fetch_assoc($charitiesResult)) {
                        echo '<tr>';
                        echo "<td><a href=\"{$row['DomainName']}/home\">{$row['Name']}</a></td>";
                        echo "<td>{$counties[$row['CountyID']]}</td>";
                        echo '</tr>';
                    }
                   ?>
              </tbody>
            </table>
          </div>
          <?php
              else:
                  //output error
              endif;
          ?>
      </div>
<?php

require_once 'footer.php';
