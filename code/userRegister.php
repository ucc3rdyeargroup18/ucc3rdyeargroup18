<?php
    $documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
    set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
    require_once 'database_functions.php';
    connect_to_database();
    $errors = array();
    $userDetails = array();
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); //start a session if one does not exist
    }

    if(isset($_SESSION['validUser']) && $_SESSION['validUser']){//user is logged in
        //TODO redirect to user panel
        die();
    }

    if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
        //Sanitize the input
        $userDetails['Email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $userDetails['Email'] = filter_var($userDetails['Email'], FILTER_VALIDATE_EMAIL);
        $userDetails['FirstName'] = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $userDetails['LastName'] = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
        $userDetails['Address1'] = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
        $userDetails['Address2'] = filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING);
        $options = array("options" => array("min_range" => 1, "max_range" => 32));
        $userDetails['CountyID'] = filter_input(INPUT_POST, 'county', FILTER_VALIDATE_INT, $options);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $pass = $_POST['password1'];
        $pass2 = $_POST['password2'];
        
        //validate the input - all fields are required
        if($userDetails['Email'] === ""){
            $errors['email'] = "Please enter a contact email";
        }
        if(!$userDetails['Email']){
            $errors['email'] = "Please enter a valid email address";
        }
        if($userDetails['FirstName'] === ""){
            $errors['FirstName'] = "Please enter the name of your Charity";
        }
        if($userDetails['LastName'] === ""){
            $errors['LastName'] = "Please enter the name of your Charity";
        }
        if($userDetails['Address1'] === ""){
            $errors['address1'] = "Please provide he first line of your address";
        }
        if($userDetails['Address2'] === ""){
            $errors['address2'] = "Please provide the second line of your address";
        }
        if(!$userDetails['CountyID']){
            $errors['county'] = "Please choose a valid county";
        }
        $userDetails['Phone'] = validate_number($phone);
        if(!$userDetails['Phone']){
            $errors['Phone'] = "Please enter a valid phone number";
            $userDetails['Phone'] = $phone;
        }
        if($pass == ""){
            $errors['password'] = "Please enter a password";
        } elseif ($pass2 == ""){
            $errors['password'] = "You must confirm your password";
        } elseif($pass != $pass2){
            $errors['password'] = "Passwords do not match.";
        } else{
            $userDetails['encPass'] = md5($pass);
        }
        
        
        if(count($errors) > 0){
            outputUserForm($errors, $userDetails);
        } else {
            $insertSQL = "INSERT INTO cms_users "
                    . "(EmailAddress, FirstName, LastName, Address1, Address2, CountyID, Phone, EncryptedPassword) "
                    . "VALUES ("
                    . "'{$userDetails['Email']}', "
                    . "'{$userDetails['FirstName']}', "
                    . "'{$userDetails['LastName']}', "
                    . "'{$userDetails['Address1']}', "
                    . "'{$userDetails['Address2']}', "
                    . "{$userDetails['CountyID']}, "
                    . "'{$userDetails['Phone']}',"
                    . "'{$userDetails['encPass']}'); ";
            $insertResult = mysql_query($insertSQL);
            if($insertResult){ //the charity has been registered
                require 'header.php';
                    ?>
<div class="container">
    <p class="lead">Your account has been created.</p>
    <p>Contact Email: <?=$userDetails['Email']?></p>
    <p>Name: <?=$userDetails['FirstName'] . " " . $userDetails['LastName']?></p>
    <p>Address 1: <?=$userDetails['Address1']?></p>
    <p>Address 2: <?=$userDetails['Address2']?></p>
    <?php
        $countySQL = "SELECT County FROM cms_counties WHERE CountyID = {$userDetails['CountyID']}";
        $countyResult = mysql_query($countySQL);
        $county = mysql_result($countyResult, 0);
    ?>
    <p>County: <?=$county?></p>
    <p>Phone: <?=$userDetails['Phone']?></p>
</div>
                    <?php
                require 'footer.php';
            } else{
                $mysqlError = mysql_errno();
                if($mysqlError == 1062){ //duplicate entry
                    $errors['email'] = "A user has already registered with this email address";
                    outputUserForm($errors, $userDetails);
                }
           }
        }
    } else {
        outputUserForm($errors, $userDetails);
    }
       
    function outputUserForm(&$errors, &$userDetails, $success = false){
        require 'header.php';
        ?>
            <div class="jumbotron">
                <div class="container">
                  <h1>Register As A User</h1>
                  <p>Please enter your details below.
                      <br />
                      <small class="text-muted">You must be a registered user to register your charity</small>
                      <br />
                      <small>
                      Already Registered? <a href="/login.php">Log In!</a>
                      </small>
                  </p>
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
                        echo '<span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;The Charity\'s details have been updated!';
                    echo '</div>';
                }
                  ?>
                  <form name="cmsForm" method="post">

                      <label for="email">Email</label>
                      <input name="email" id="email" type="email" class="form-control" required autofocus value="<?=isset($userDetails['Email']) ? $userDetails['Email'] : ''?>">
              <label for="password1">Password</label>
              <input name="password1" id="password1" type="password" class="form-control" required>
              
              <label for="password2">Password (again)</label>
              <input name="password2" id="password2" type="password" class="form-control" required>
                      
               <label for="firstname">First Name</label>
              <input name="firstname" id="firstname" type="text" class="form-control" required value="<?=isset($userDetails['FirstName']) ? $userDetails['FirstName'] : ''?>">
              <label for="surname">Surname</label>
              <input name="surname" id="surname" type="text" class="form-control" required value="<?=isset($userDetails['LastName']) ? $userDetails['LastName'] : ''?>">

              <label for="address1">Address1</label>
              <input name="address1" id="address1" type="text" class="form-control" required value="<?=isset($userDetails['Address1']) ? $userDetails['Address1'] : ''?>">
              <label for="address2">Address2</label>
              <input name="address2" id="address2" type="text" class="form-control" required value="<?=isset($userDetails['Address2']) ? $userDetails['Address2'] : ''?>">
              <label for="county">County</label>

             <div class="dropdown">

                 <select name="county" id="county" class="form-control" required>


                <?php
                $countiesSQL = "SELECT * FROM cms_counties ORDER BY County;";
                $countiesResult = mysql_query($countiesSQL);
                while($row = mysql_fetch_assoc($countiesResult)){
                    echo '<option ';
                    if(isset($userDetails['CountyID']) && $userDetails['CountyID'] == $row['CountyID']){
                        echo 'selected ';
                    }
                    echo 'value="' . $row['CountyID'] . '">' . $row['County'] . '</option>';
                }
                ?>
               </select> 
           </div>
              <label for="phone">Phone</label>
              <input name="phone" id="phone" type="tel" class="form-control" required value="<?=isset($userDetails['Phone']) ? $userDetails['Phone'] : ''?>">

          </br>
          <button type="submit" class="btn" id="submitButton" name="submission" value="true">Register</button>
          <button type="reset" class="btn" name="reset">Reset</button>

                  </form>
              </div><!-- /.container -->
        <?php
                        require 'footer.php';
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
