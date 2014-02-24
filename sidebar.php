<?php
//TODO Make sidebar dynamic - fetch content from database
?>

<div class="col-md-3 sidebar">
              
              <div class="panel">
                  Sidebar content goes in here! What the user wants, nobody knows! but we'll fill it up somehow!
              </div>
              
              <div class="panel">
                  The counter for total amount donated will look prettier when it's finished!
              </div>
              
              <div class="panel">
                  As a small charity we rely on donations from the public in order to achieve our goals.
              </div>
              
              <div class="panel">
                  <?php include '/charity/donateButton.html'; ?>
              </div>
              
              <div class="panel">
                  Total donations to date: &euro;
                  <?php
                    $donationsSQL = "SELECT SUM(Amount) FROM cms_donations WHERE CharityID = {$info['CharityID']}";
                    $donationResult = mysql_query($donationsSQL);
                    $totalDonations = mysql_result($donationResult, 0);
                    echo $totalDonations;
                  ?>
              </div>
