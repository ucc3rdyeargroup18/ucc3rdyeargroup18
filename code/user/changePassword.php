<?php
    $referer = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
    $refererArray = explode('/', $referer);
    $domain = $refererArray[3];
    $documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
    set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
    require_once 'database_functions.php';
    connect_to_database();
    session_start();
    $errors = array();
    $userDetails = array();
    if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
        //get the input
        $curPass = $_POST['curpassword'];
        $encCurPass = md5($curPass);
        $pass = $_POST['password1'];
        $pass2 = $_POST['password2'];
        
        $curPassSQL = "SELECT * FROM cms_users WHERE UserID = {$_SESSION['userID']} AND EncryptedPassword = '{$encCurPass}';";
        $curPassResult = mysql_query($curPassSQL);
        
        if($curPass == ""){
            $errors['password'] = "You must enter your current password";
        } elseif (mysql_num_rows($curPassResult) != 1) {
            $errors['password'] = "Current Password is incorrect";
        } elseif($pass == ""){
            $errors['password'] = "Please enter a new password";
        } elseif ($pass2 == ""){
            $errors['password'] = "You must confirm your new password";
        } elseif($pass != $pass2){
            $errors['password'] = "Passwords do not match.";
        } else{
            $userDetails['encPass'] = md5($pass);
        }
                
        
        if(count($errors) > 0){
            outputCharityForm($errors, $userDetails);
        } else {
            $updateSQL = "UPDATE cms_users SET "
                    . "EncryptedPassword = '{$userDetails['encPass']}' "
                    . "WHERE UserID = {$_SESSION['userID']};";
            $updateResult = mysql_query($updateSQL);
            if($updateResult){
                outputCharityForm($errors, $userDetails, true);
            } else{
                //TODO Output Error 
           }
        }
    } else {
        $detailsSQL = "SELECT * FROM cms_users WHERE UserID = {$_SESSION['userID']}";
        $detailsResult = mysql_query($detailsSQL);
        while($row = mysql_fetch_assoc($detailsResult)){
            $userDetails = $row;
        }
        outputCharityForm($errors, $userDetails);
    }

    
    function outputCharityForm(&$errors, &$userDetails, $success = false){
        ?>
            <div class="jumbotron">
                <div class="container">
                  <h1>Change Your Password</h1>
                  <p>You can change your password below.</p>

                </div>
              </div>

              <div class="container">
                  <?php
                  if(count($errors) > 0){
                    echo '<div class="alert alert-danger alert-dismissable">';
                        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        echo "<strong>We Found ";
                        echo count($errors) == 1 ? 'an Error' : 'Some Errors';
                        echo ": </strong><br />";
                        foreach($errors as $error){
                            echo '<span class="glyphicon glyphicon-warning-sign"></span>&nbsp;&nbsp;';
                            echo $error . '<br />';
                        }
                    echo '</div>';
                } elseif ($success){ //database has been updated
                    echo '<div class="alert alert-success alert-dismissable">';
                        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        echo '<span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Your password has been changed!';
                    echo '</div>';
                }
                  ?>
                  <form name="cmsForm" onsubmit="return submitForm()">

                       <label for="curpassword">Current Password</label>
              <input name="curpassword" id="curpassword" type="password" class="form-control" required>
                      
         <label for="password1">New Password</label>
              <input name="password1" id="password1" type="password" class="form-control" required>
              
              <label for="password2">New Password (again)</label>
              <input name="password2" id="password2" type="password" class="form-control" required>
          </br>
          <button type="submit" class="btn" id="submitButton" name="submission" value="true">Update</button>
          <button type="reset" class="btn" name="reset">Reset</button>

                  </form>
              </div><!-- /.container -->
        <?php
    }
    
    //TODO add support for northern irish phone numbers
    //TODO add support for landlines
    /**
     * Validate an Irish phone number
     * 
     * @author Diarmuid (http://archive.diarmuid.ie/blog/code/irish-mobile-phone-number-validation-in-php/index.html)
     * @param string $number
     * @return string|boolean The validated phone number or FALSE if number is invalid
     */
    function validate_number($number) {
    $number = preg_replace('/\D/', '', $number); //Strip all non numeric characters
    $length = strlen($number);
    
    // Strip the 00353, 353 or 0 from the start of the number
    if ($length == 10) // 08XXXXXXXX
    {
        $number = substr($number, 1);
    }
    elseif ($length == 12) // 3538XXXXXXXX
    {
        $number = substr($number, 3);
    }
    elseif ($length == 14) // 003538XXXXXXXX
    {
        $number = substr($number, 5);
    }
    else // Not a valid number
    {
        return FALSE;
    }
 
    if (preg_match('/8\d{8}$/', $number)) {
        //Number must have an '8' followed by eight other digits i.e. 8XXXXXXXX
        return '00353' . $number;
    }
    else // Not a valid number
    {
        return FALSE;
    }
}
