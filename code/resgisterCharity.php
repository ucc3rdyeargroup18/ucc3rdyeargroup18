<?php
    $documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
    set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
    require_once 'database_functions.php';
    connect_to_database();
    $errors = array();
    $charityDetails = array();
    if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
        //Sanitize the input
        $charityDetails['Email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $charityDetails['Email'] = filter_var($charityDetails['Email'], FILTER_VALIDATE_EMAIL);
        $charityDetails['Name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $charityDetails['Address1'] = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
        $charityDetails['Address2'] = filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING);
        $options = array("options" => array("min_range" => 1, "max_range" => 32));
        $charityDetails['CountyID'] = filter_input(INPUT_POST, 'county', FILTER_VALIDATE_INT, $options);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $charityNo = $_POST['charityNo'];
        $charityDetails['CharityNo'] = filter_input(INPUT_POST, 'charityNo', FILTER_VALIDATE_INT);
        $charityDetails['BIC'] = filter_input(INPUT_POST, 'bic', FILTER_SANITIZE_STRING);
        $charityDetails['IBAN'] = filter_input(INPUT_POST, 'iban', FILTER_SANITIZE_STRING);
        $charityDetails['Description'] = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_MAGIC_QUOTES);
                
        //validate the input - all fields are required
        if($charityDetails['Email'] === ""){
            $errors['email'] = "Please enter a contact email";
        }
        if(!$charityDetails['Email']){
            $errors['email'] = "Please enter a valid email address";
        }
        if($charityDetails['Name'] === ""){
            $errors['name'] = "Please enter the name of your Charity";
        }
        if($charityDetails['Address1'] === ""){
            $errors['address1'] = "Please provide he first line of your address";
        }
        if($charityDetails['Address2'] === ""){
            $errors['address2'] = "Please provide the second line of your address";
        }
        if(!$charityDetails['CountyID']){
            $errors['county'] = "Please choose a valid county";
        }
        $charityDetails['Phone'] = validate_number($phone);
        if(!$charityDetails['Phone']){
            $errors['Phone'] = "Please enter a valid phone number";
            $charityDetails['Phone'] = $phone;
        }
        if(!$charityDetails['CharityNo']){
            $errors['CharityNo'] = "Please enter a valid Charity Number";
            $charityDetails['CharityNo'] = $charityNo;
        }
        if($charityDetails['BIC'] === ""){
            //TODO validate BIC further
            $errors['BIC'] = "Please enter the BIC (Bank Identifier Code) for your charitiy's bank account";
        }
        if($charityDetails['IBAN'] === ""){
            //TODO validate IBAN further
            $errors['IBAN'] = "Please enter the IBAN (International Bank Account Number) for your charitiy's bank account";
        }
        if($charityDetails['Description'] === ""){
            $errors['Description'] = "You must enter a description of your charity.";
        }
        
        
        if(count($errors) > 0){
            outputCharityForm($errors, $charityDetails);
        } else {
            $newDomain = strtolower($charityDetails['Name']);
            $newDomain = preg_replace("![^a-z0-9]+!i", "-", $newDomain);
            $newDomain = rtrim($newDomain, "-");
            $first = true;
            $i = 0;
            do{
                    $perma = $first ? $newDomain : $newDomain . '-' . $i;
                    $sql = "SELECT * FROM cms_charities WHERE DomainName = '{$perma}';";
                    $result = mysql_query($sql);
                    $first = false;
                    $i++;
            } while (mysql_num_rows($result) != 0);
            $mysqli = new mysqli("localhost", 'cdt3', 'aichedop', 'charity1');
            if ($mysqli->connect_errno) {
                echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            }
            $updateSQL = "INSERT INTO cms_charities "
                    . "(Email, Name, Address1, Address2, CountyID, Phone, CharityNo, BIC, IBAN, Description, DomainName) "
                    . "VALUES ("
                    . "'{$charityDetails['Email']}', "
                    . "'{$charityDetails['Name']}', "
                    . "'{$charityDetails['Address1']}', "
                    . "'{$charityDetails['Address2']}', "
                    . "'{$charityDetails['CountyID']}', "
                    . "'{$charityDetails['Phone']}', "
                    . "'{$charityDetails['CharityNo']}', "
                    . "'{$charityDetails['BIC']}', "
                    . "'{$charityDetails['IBAN']}', "
                    . "'{$charityDetails['Description']}', "
                    . "'{$perma}'); "
                    . "SET @insertid=LAST_INSERT_ID();"
                    . "INSERT INTO cms_charitypages (CharityID, PageID) VALUES (@insertid, 1), (@insertid, 2), (@insertid, 5); "
                    . "INSERT INTO cms_charitylayout (CharityID, Color1, Color2, Color3) VALUES (@insertid, 'f8f8f8', 'ffffff', '333333');";
            $updateResult = mysqli_multi_query($mysqli, $updateSQL);
            if($updateResult){ //the charity has been registered
                require 'header.php';
                    ?>
<div class="container">
    <p class="lead">Your charity has been created.</p>
    <p>Contact Email: <?=$charityDetails['Email']?></p>
    <p>Charity Name: <?=$charityDetails['Name']?></p>
    <p>Address 1: <?=$charityDetails['Address1']?></p>
    <p>Address 2: <?=$charityDetails['Address2']?></p>
    <?php
        $countySQL = "SELECT County FROM cms_counties WHERE CountyID = {$charityDetails['CountyID']}";
        $countyResult = mysql_query($countySQL);
        echo mysql_error();
        $county = mysql_result($countyResult, 0);
    ?>
    <p>County: <?=$county?></p>
    <p>Phone: <?=$charityDetails['Phone']?></p>
    <p>Charity No: <?=$charityDetails['CharityNo']?></p>
    <p>BIC: <?=$charityDetails['BIC']?></p>
    <p>IBAN: <?=$charityDetails['IBAN']?></p>
    <p>Description:</p>
    <p><?=stripslashes($charityDetails['Description'])?></p>
    <p>Your site is available at: <a href="/<?=$perma?>/home"><?=$perma?>/home</a></p>
</div>
                    <?php
                require 'footer.php';
            } else{
                echo mysql_error();
           }
        }
    } else {
        outputCharityForm($errors, $charityDetails);
    }
       
    function outputCharityForm(&$errors, &$charityDetails, $success = false){
        require 'header.php';
        ?>
            <div class="jumbotron">
                <div class="container">
                  <h1>Register Your Charity</h1>
                  <p>Please enter your Charity's details below.</p>

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
                      <input name="email" id="email" type="email" class="form-control" required autofocus value="<?=isset($charityDetails['Email']) ? $charityDetails['Email'] : ''?>">
                      
          <label for="name">Charity Name</label>
              <input name="name" id="name" type="text" class="form-control" required value="<?=isset($charityDetails['Name']) ? $charityDetails['Name'] : ''?>">

              <label for="address1">Address1</label>
              <input name="address1" id="address1" type="text" class="form-control" required value="<?=isset($charityDetails['Address1']) ? $charityDetails['Address1'] : ''?>">
              <label for="address2">Address2</label>
              <input name="address2" id="address2" type="text" class="form-control" required value="<?=isset($charityDetails['Address2']) ? $charityDetails['Address2'] : ''?>">
              <label for="county">County</label>

             <div class="dropdown">

                 <select name="county" id="county" class="form-control" required>


                <?php
                $countiesSQL = "SELECT * FROM cms_counties ORDER BY County;";
                $countiesResult = mysql_query($countiesSQL);
                while($row = mysql_fetch_assoc($countiesResult)){
                    echo '<option ';
                    if(isset($charityDetails['CountyID']) && $charityDetails['CountyID'] == $row['CountyID']){
                        echo 'selected ';
                    }
                    echo 'value="' . $row['CountyID'] . '">' . $row['County'] . '</option>';
                }
                ?>
               </select> 
           </div>
              <label for="phone">Phone</label>
              <input name="phone" id="phone" type="tel" class="form-control" required value="<?=isset($charityDetails['Phone']) ? $charityDetails['Phone'] : ''?>">
              <label for="charityNo">Charity No.</label>
              <input name="charityNo" id="charityNo" type="text" class="form-control" required value="<?=isset($charityDetails['CharityNo']) ? $charityDetails['CharityNo'] : ''?>">
              <label for="bic">BIC</label>
              <input name="bic" id="bic" type="text" class="form-control" required value="<?=isset($charityDetails['BIC']) ? $charityDetails['BIC'] : ''?>">
              <label for="iban">IBAN</label>
              <input name="iban" id="iban" type="text" class="form-control" required value="<?=isset($charityDetails['IBAN']) ? $charityDetails['IBAN'] : ''?>">
              
              <label for="description">Tell Us About Your Charity</label>
              <div class="well">
                  <textarea id="description-textarea" name="description" placeholder="This will appear on your charity's home page"><?=isset($charityDetails['Description']) ? $charityDetails['Description'] : ''?></textarea>
              </div>
                <script type="text/javascript">
                    $('#description-textarea').wysihtml5({"link": false, "image": false});
                </script>

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
