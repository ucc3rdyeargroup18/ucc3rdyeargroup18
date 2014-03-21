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

    $getExistingAccessSQL = "SELECT cms_accessrequest.PageID, CustomTitle, cms_users.FirstName, Pending, cms_accessrequest.ID "
            . "FROM CMS_CharityPages, CMS_Users, CMS_AccessRequest, cms_pages
                WHERE cms_charitypages.PageID = cms_accessrequest.PageID
                AND cms_charitypages.PageID = cms_pages.PageID
                AND cms_charitypages.CharityID = {$info['charityID']}
                AND cms_accessrequest.CharityID =  {$info['charityID']}
                AND cms_accessrequest.UserID = cms_users.UserID
                AND Pending = '0'
                ORDER BY CMS_Users.FirstName";
    $result = mysql_query($getExistingAccessSQL);
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
          <th>Person</th>
          <th>Page</th>
          <th>Revoke</th>
        </tr>
      </thead>
      <tbody>';
    
    foreach($content as $page)
    {
          echo '<tr>';
          echo '<td>' . $page['Name'] . '</td>';
          echo '<td>' . $page['CustomTitle'] . '</td>';
          echo '<td><a id="remove' . $page["ID"] . '" class="btn btn-default" href="javascript:;" onclick="removeAccess(' . $page["ID"] . ')" role="button">Grant</a></td>'; 
          echo '</tr>';
    }
    echo '</tbody></table>';

    ?>
</div>
