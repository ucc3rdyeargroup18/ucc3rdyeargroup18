<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$referer = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
$refererArray = explode('/', $referer);
$domain = $refererArray[3];
$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
require_once 'database_functions.php';
connect_to_database();$charityIDSQL = "SELECT CharityID FROM cms_charities WHERE DomainName = '{$domain}';";
$charityIDResult = mysql_query($charityIDSQL);
$info['charityID'] = mysql_result($charityIDResult, 0);

session_start();
?>
<?php

    $getPendingSQL = "SELECT cms_accessrequest.PageID, CustomTitle, cms_users.FirstName, cms_users.LastName, Pending, cms_accessrequest.ID "
            . "FROM CMS_CharityPages, CMS_Users, CMS_AccessRequest, cms_pages
                WHERE cms_charitypages.PageID = cms_accessrequest.PageID
                AND cms_charitypages.PageID = cms_pages.PageID
                AND cms_charitypages.CharityID = {$info['charityID']}
                AND cms_accessrequest.CharityID =  {$info['charityID']}
                AND cms_accessrequest.UserID = cms_users.UserID
                AND Pending = '1'";
    $result = mysql_query($getPendingSQL);
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
          <th>Grant</th>
          <th>Deny</th>
        </tr>
      </thead>
      <tbody>';
    
    foreach($content as $page)
    {
          echo '<tr>';
          echo '<td>' . $page['FirstName'] . ' ' . $page['LastName'] . '</td>';
          echo '<td>' . $page['CustomTitle'] . '</td>';
          echo '<td><a id="grant' . $page["ID"] . '" class="btn btn-default" href="javascript:;" onclick="grantAccess(' . $page["ID"] . ')" role="button">Grant</a></td>'; 
          echo '<td><a id="remove' . $page["ID"] . '" class="btn btn-default" href="javascript:;" onclick="removeAccess(' . $page["ID"] . ')" role="button">Deny</a></td>';          
          echo '</tr>';
    }
    echo '</tbody></table>';

    ?>
</div>
