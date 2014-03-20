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
session_start();
?>
<?php

    $getExistingPagesSQL = "SELECT cms_accessrequest.PageID, CustomTitle, cms_charities.Name, Pending, cms_accessrequest.ID "
            . "FROM CMS_CharityPages, CMS_Charities, CMS_AccessRequest, cms_pages
                WHERE cms_charitypages.PageID = cms_accessrequest.PageID
                AND cms_charitypages.PageID = cms_pages.PageID
                AND cms_charitypages.CharityID = cms_charities.CharityID
                AND cms_charities.CharityID = cms_accessrequest.CharityID
                AND cms_accessrequest.UserID = {$_SESSION['userID']}";
    $result = mysql_query($getExistingPagesSQL);
    while($row = mysql_fetch_assoc($result))
    {
        $content[] = $row;
    }
    
    ?>
        
    <?php
    
    echo'<div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Charity</th>
          <th>Page</th>
          <th>Access</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>';
    
    foreach($content as $page)
    {
          echo '<tr>';
          echo '<td>' . $page['Name'] . '</td>';
          echo '<td>' . $page['CustomTitle'] . '</td>';
          echo '<td>';
          echo $page['Pending'] == '1' ? 'Pending' : 'Granted';
          echo '</td>';
          echo '<td><a class="btn btn-default" href="javascript:;" onclick="removeAccess(' . $page["ID"] . ')" role="button">Remove Access</a></td>';          
          echo '</tr>';
    }
    echo '</tbody></table>';
