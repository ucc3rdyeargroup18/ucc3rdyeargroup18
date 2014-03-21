<?php
    $referer = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
    $refererArray = explode('/', $referer);
    $domain = $refererArray[3];
    $documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
    set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
    require_once 'database_functions.php';
    connect_to_database();
    $errors = array();
        
    $content = array();
    
    if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
        foreach($_POST['isUsed'] as $key => $val){
            $content[$key]['isUsed'] = $val == "true" ? true : false;
            $content[$key]['PageID'] = $key;
            $content[$key]['CustomTitle'] = filter_var($_POST['customTitle'][$key], FILTER_SANITIZE_STRING);
            $content[$key]['Name'] = filter_var($_POST['name'][$key], FILTER_SANITIZE_STRING);
        }
        $updated = true;
        foreach($content as $page){            
            $updateSQL = "UPDATE cms_charityPages SET "
            . "CustomTitle = '{$page['CustomTitle']}', "
            . "isUsed = '{$page['isUsed']}'"
            . "WHERE CharityID = "
                . "(SELECT CharityID FROM cms_charities WHERE DomainName = '{$domain}')"
            . "AND PageID = '{$page['PageID']}'";
            $updateResult = mysql_query($updateSQL);
            if(!$updateResult){
                $updated = false;
            }
        }
        if($updated){
            outputPageForm($errors, $content, true);
        }
    } else{
        $getPagesSQL = "SELECT cms_charitypages.PageID, Name, CustomTitle, isUsed FROM CMS_CharityPages, CMS_Pages WHERE CharityID = "
                    . "(SELECT CharityID FROM cms_charities WHERE DomainName = '{$domain}') "
                    . "AND cms_charitypages.PageID = CMS_Pages.PageID";
        $result = mysql_query($getPagesSQL);
        while($row = mysql_fetch_assoc($result))
        {
            $content[] = $row;
        }
    }
    
    outputPageForm($errors, $content);
    



function outputPageForm(&$errors, &$content, $success = false){
    global $domain;
        ?>
            <div class="jumbotron">
                <div class="container">
                  <h1>Edit Charity Pages</h1>
                  <p>
                      Select your charity's below.<br />
                      <small class="text-muted">
                          Selected pages appear in the navigation bar at the top of your site.<br />
                          Unselected pages will not appear in the nav-bar, but can still be access via links on other pages.
                      </small>
                  </p>

                </div>
              </div>

              <div class="container">
                  <?php
                  if ($success){ //database has been updated
                    echo '<div class="alert alert-success alert-dismissable">';
                        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        echo '<span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;The Charity\'s pages have been updated!';
                    echo '</div>';
                  }
                  $count = 0;


                    ?>
                  <form name="cmsForm" onsubmit="return submitForm()">
                      <?php
        foreach($content as $page){
            ?>
                  <div class="row">
                    <div class="col-lg-6">
                        <?='<label for="' . $page["PageID"] . '">' . $page["Name"] . '</label>'?>
                      <div class="input-group">
                        <span class="input-group-addon">
                          <input type="checkbox" id="box<?=$page["PageID"] . '" type="checkbox"  name=isUsed[' . $page["PageID"] . ']' ;
                          echo $page['isUsed'] ? ' checked' : '';
                    ?>
                          />
                        </span>
                        <input class="form-control" <?='id="' . $page["PageID"] . '" type="text" value="'. $page["CustomTitle"] .'"name="customTitle[' . $page["PageID"] . ']"'?> />
                        <input type="hidden" name="name[<?=$page["PageID"]?>]" value="<?=$page["Name"]?>" />    
                      </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                  </div><!-- /.row -->
            <?php
            
        }
        ?>
        </br>
          <button type="submit" class="btn" id="submitButton" name="submission" value="true">Submit Changes</button>
          <button type="reset" class="btn" name="reset">Reset Changes</button>
              </form>
          
          <?php
            die();
    }
