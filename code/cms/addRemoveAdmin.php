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

    $getExistingPagesSQL = "SELECT cms_charityusers.CharityUserID, cms_users.FirstName, cms_users.LastName"
            . "FROM cms_charityusers, cms_users"
            . "WHERE AuthLevelID = 2"
            . "AND CharityID = {$info['CharityID']}"
            . "AND cms_charityusers.UserID = cms_users.UserID";
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
          <th>User</th>
          <th>Remove</th>
        </tr>
      </thead>
      <tbody>';
    
    foreach($content as $user)
    {
          echo '<tr>';
          echo '<td>' . $user['FirstName'] . ' ' . $user['LastName'] . '</td>';
          echo '<td><a id="remove' . $user["CharityUserID"] . '" class="btn btn-default" href="javascript:;" onclick="removeAdmin(' . $user["CharityUserID"] . ')" role="button">Remove Admin</a></td>';          
          echo '</tr>';
    }
    echo '</tbody></table>';

    ?>

<div class="panel panel-default">
    <div class="panel-heading">
        Create Administrators
    </div>
    <div class="panel-body">
        <form>
            <div class="col-md-4 dropdown">
                <label for="selectUser">Select User:</label>
                     <select name="selectUser" id="selectUser" class="form-control" required>


                    <?php
                    $charitiesSQL = "SELECT * FROM cms_users WHERE CharityId > 0;";
                    $charitiesResult = mysql_query($charitiesSQL);
                    while($row = mysql_fetch_assoc($charitiesResult)){
                        echo '<option value="' . $row['UserID'] . '">' . $row['FirstName'] . ' ' . $row['LastName'] . '</option>';
                    }
                    ?>
                   </select> 
               </div>
            
            <div class="col-md-4">
                <label for="makeAdmin">Make Admin:</label><br />
                <a id="makeAdmin" class="btn btn-default" href="javascript:;" onclick="makeAdmin()" role="button">Make Admin</a>
            </div>
        </form>
    </div>
    
</div>
