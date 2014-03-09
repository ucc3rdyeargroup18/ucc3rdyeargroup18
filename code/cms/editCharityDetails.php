<?php
    $referer = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
    $refererArray = explode('/', $referer);
    $domain = $refererArray[3];
    $documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
    set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
    require_once 'database_functions.php';
    connect_to_database();
    $errors = array();
    $charityDetails = array();
    if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
        //Sanitize the input
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
        
        //validate the input - all fields are required
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
        
        
        if(count($errors) > 0){
            outputCharityForm($errors, $charityDetails);
        } else {
            $updateSQL = "UPDATE cms_charities SET "
                    . "Name = '{$charityDetails['Name']}', "
                    . "Address1 = '{$charityDetails['Address1']}', "
                    . "Address2 = '{$charityDetails['Address2']}', "
                    . "CountyID = '{$charityDetails['CountyID']}', "
                    . "Phone = '{$charityDetails['Phone']}', "
                    . "CharityNo = '{$charityDetails['CharityNo']}', "
                    . "BIC = '{$charityDetails['BIC']}', "
                    . "IBAN = '{$charityDetails['IBAN']}' "
                    . "WHERE DomainName = '{$domain}'";
            $updateResult = mysql_query($updateSQL);
            if($updateResult){
                outputCharityForm($errors, $charityDetails, true);
            }
        }
    } else {
        $detailsSQL = "SELECT * FROM cms_charities WHERE DomainName = '{$domain}'";
        $detailsResult = mysql_query($detailsSQL);
        while($row = mysql_fetch_assoc($detailsResult)){
            $charityDetails = $row;
        }
        outputCharityForm($errors, $charityDetails);
    }

    
    function outputCharityForm(&$errors, &$charityDetails, $success = false){
        ?>
            <div class="jumbotron">
                <div class="container">
                  <h1>Edit Charity Details</h1>
                  <p>Your charity's details can be changed below.</p>

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
                  <form name="cmsForm" onsubmit="return submitForm()">

          <label for="name">Name</label>
              <input name="name" id="name" type="text" class="form-control" required autofocus value="<?=$charityDetails['Name']?>">

              <label for="address1">Address1</label>
              <input name="address1" id="address1" type="text" class="form-control" required value="<?=$charityDetails['Address1']?>">
              <label for="address2">Address2</label>
              <input name="address2" id="address2" type="text" class="form-control" required value="<?=$charityDetails['Address2']?>">
              <label for="county">County</label>

             <div class="dropdown">

                 <select name="county" id="county" class="form-control" required>


                <?php
                $countiesSQL = "SELECT * FROM cms_counties ORDER BY County;";
                $countiesResult = mysql_query($countiesSQL);
                while($row = mysql_fetch_assoc($countiesResult)){
                    echo '<option ';
                    if($charityDetails['CountyID'] == $row['CountyID']){
                        echo 'selected ';
                    }
                    echo 'value="' . $row['CountyID'] . '">' . $row['County'] . '</option>';
                }
                ?>
               </select> 
           </div>
              <label for="phone">Phone</label>
              <input name="phone" id="phone" type="tel" class="form-control" required value="<?=$charityDetails['Phone']?>">
              <label for="charityNo">Charity No.</label>
              <input name="charityNo" id="charityNo" type="text" class="form-control" required value="<?=$charityDetails['CharityNo']?>">
              <label for="bic">BIC</label>
              <input name="bic" id="bic" type="text" class="form-control" required value="<?=$charityDetails['BIC']?>">
              <label for="iban">IBAN</label>
              <input name="iban" id="iban" type="text" class="form-control" required value="<?=$charityDetails['IBAN']?>">

          </br>
          <button type="submit" class="btn" id="submitButton" name="submission" value="true">Submit Changes</button>
          <button type="reset" class="btn" name="reset">Reset Changes</button>

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
