<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );

require_once 'header.php';
$errors = array();
$petDetails = array();

if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
    $petDetails['Name'] = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $lastSeen = filter_input(INPUT_POST, 'lastSeen', FILTER_SANITIZE_STRING);
    $timeString = strtotime($lastSeen);
    $petDetails['LastSeen'] = date("Y-m-d H:i:s", $timeString);
    //TODO Make datetime standard across browsers
    $petDetails['Description'] = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $petDetails['ContactName'] = trim(filter_input(INPUT_POST, 'contactName', FILTER_SANITIZE_STRING));
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
    $petDetails['Email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $petDetails['Details'] = trim(filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING));
    
    //TODO handle file uploads
    
    if($petDetails['Name'] === ""){
        $errors['name'] = "Please enter your pets name";
    }
    if($petDetails['Description'] === ""){
        $errors['description'] = "Please enter a description of your pet";
    }
    if($petDetails['ContactName'] === ""){
        $errors['contactName'] = "Please enter your name";
    }
    if($petDetails['Email'] === ""){
        $errors['email'] = "Please enter your email address";
    }
    if(!$petDetails['Email']){
        $errors['email'] = "Please enter a valid email address";
    }
    if($petDetails['Details'] === ""){
        $errors['details'] = "Please enter details of your missing pet";
    }
    $petDetails['Number'] = validate_number($number);
    if(!$petDetails['Number']){
        $errors['Number'] = "Please enter a valid phone number";
        $petDetails['Number'] = $number;
    }
    
    if(count($errors) > 0){
        output_form($errors, $petDetails);
    } else {
        $insertSQL = "INSERT INTO cms_lostfounds (CharityID, CreatorID, CreatedOn, LastSeen, Name, Description, Contact, Number1, Email, Details, isLost)"
                . "VALUES((SELECT CharityID FROM cms_charities WHERE DomainName = '{$_SESSION['lastDomain']}'),"
                . "{$_SESSION['userID']}, NOW(), '{$petDetails['LastSeen']}', '{$petDetails['Name']}', '{$petDetails['Description']}', '{$petDetails['ContactName']}', '{$petDetails['Number']}', '{$petDetails['Email']}', '{$petDetails['Details']}', 0);";
        
        $insertResult = mysql_query($insertSQL);
    }
    
} else {
    output_form($errors, $petDetails);
}

function output_form(&$errors, &$petDetails){
    ?>

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>Submit Lost Animal</h1>
        </div>
          <div class="col-md-9">
        <p class="lead">You can use the form below to file your chicken as missing</p>
        <hr />
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
                }
                  ?>
        <form method="post" enctype="multipart/form-data">

              <label for="name">Name</label>
              <input name="name" id="name" type="text" class="form-control" autofocus required value="<?=isset($petDetails['Name']) ? $petDetails['Name'] : ''?>">
              
              <label for="lastSeen">Last Seen</label>
              <input placeholder="dd/mm/yyyy HH:MM" name="lastSeen" id="lastSeen" type="datetime-local" class="form-control" required value="<?=isset($petDetails['lastSeen']) ? $petDetails['lastSeen'] : ''?>">
              
              <label for="description">Description</label>
              <textarea name="description" id="description" class="form-control" required><?=isset($petDetails['Description']) ? $petDetails['Description'] : ''?> </textarea>
              
              <label for="contactName">Contact Name</label>
              <input name="contactName" id="contactName" type="text" class="form-control" required value="<?=isset($petDetails['ContactName']) ? $petDetails['ContactName'] : ''?>">
              
              <label for="number">Contact Number</label>
              <input name="number" id="number" type="tel" class="form-control" required value="<?=isset($petDetails['Number']) ? $petDetails['Number'] : ''?>">
              
              <label for="email">Contact Email</label>
              <input name="email" id="email" type="email" class="form-control" value="<?=isset($petDetails['Email']) ? $petDetails['Email'] : ''?>">
              
              <label for="details">Details</label>
              <textarea name="details" id="details" class="form-control" required><?=isset($petDetails['Details']) ? $petDetails['Details'] : ''?></textarea>
              
              <label for="img1">Image 1</label>
              <input name="img1" id="img1" class="form-control" type="file" />
              
              <label for="img2">Image 2</label>
              <input name="img2" id="img2" class="form-control" type="file" />
              
              <br/>
              
              <button type="submit" class="btn" id="submitButton" name="submission" value="true">Submit</button>
              <button type="reset" class="btn" name="reset">Reset</button>
              
              
              
        </form>
          </div> <!-- /.col-md-9 -->
      

<?php
}
//end function
require_once 'sidebar.php';
?></div> <!-- /.container --><?php
require_once 'footer.php';

/**
 * @author http://ie1.php.net/checkdate#113205
 */
function validateDate($date, $format = 'd/m/Y H:i')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
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
