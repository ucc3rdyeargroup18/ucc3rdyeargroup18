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
    $eventDetails = array();
    $eventID = filter_input(INPUT_GET, 'eventID', FILTER_VALIDATE_INT);
    //TODO check if right charity for this event / if user is allowed edit
    if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
        $eventDetails['Name'] = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $start = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING);
    if(validateDate($start)){
        $date = date_parse_from_format("d/m/Y H:i", $start);
        $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
        $eventDetails['Start1'] = date("Y-m-d H:i:s", $time);
    } else{
        $errors['Start'] = "Start Date must be in the format dd/mm/yyyy HH:MM (eg. " . date("d/m/Y H:i") . ")";
    }
    //$timeString = strtotime($start);
    //$eventDetails['Start1'] = date("Y-m-d H:i:s", $timeString);
    $end = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING);
    if(validateDate($end)){
        $date = date_parse_from_format("d/m/Y H:i", $end);
        $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
        $eventDetails['End1'] = date("Y-m-d H:i:s", $time);
    } else{
        $errors['End'] = "End Date must be in the format dd/mm/yyyy HH:MM (eg. " . date("d/m/Y H:i") . ")";
    }
    //$timeString = strtotime($end);
    //$eventDetails['End1'] = date("Y-m-d H:i:s", $timeString);
    //TODO Make datetime standard across browsers
    $eventDetails['Description'] = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $eventDetails['Contact'] = trim(filter_input(INPUT_POST, 'contactName', FILTER_SANITIZE_STRING));
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
    $eventDetails['Email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $eventDetails['Location'] = trim(filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING));
    
    //TODO handle file uploads
    
    if($eventDetails['Name'] === ""){
        $errors['name'] = "Please enter your event's name";
    }
    if($eventDetails['Description'] === ""){
        $errors['description'] = "Please enter a description of your event";
    }
    if($eventDetails['Contact'] === ""){
        $errors['contactName'] = "Please enter your name";
    }
    if($eventDetails['Email'] === ""){
        $errors['email'] = "Please enter your email address";
    }
    if(!$eventDetails['Email']){
        $errors['email'] = "Please enter a valid email address";
    }
    $eventDetails['Number1'] = validate_number($number);
    if(!$eventDetails['Number1']){
        $errors['Number'] = "Please enter a valid phone number";
        $eventDetails['Number1'] = $number;
    }
    
    //process the story
    $newStory = filter_input(INPUT_POST, 'newStory', FILTER_VALIDATE_BOOLEAN);
    $existingStory = filter_input(INPUT_POST, 'existingStory', FILTER_VALIDATE_BOOLEAN);
    
    if($newStory){
        $storyName = filter_input(INPUT_POST, 'storyName', FILTER_SANITIZE_STRING);
    } elseif($existingStory){
        $storiesSQL = "SELECT * FROM cms_stories WHERE CharityID = (SELECT CharityID FROM cms_charities WHERE DomainName = '{$_SESSION['lastDomain']}');";
        $storiesResult = mysql_query($storiesSQL);
        $options = array();
        while($row = mysql_fetch_assoc($storiesResult)){
            $options[] = $row['StoryID'];
        }
        $storyID = filter_input(INPUT_POST, 'existingStoryID', FILTER_VALIDATE_INT, $options);
        if(!$storyID){
            $errors['story'] = "Please choose a valid story";
        }
    } //else not using story
    
    $allowedTypes = array(
        "image/gif",
        "image/jpeg",
        "image/jpg",
        "image/pjpeg",
        "image/x-png",
        "image/png"
    );
    
    $hasImg1 = false;
    
    if(isset($_FILES['img1']) && $_FILES['img1']['size'] > 0){
        $hasImg1 = true;
    
        if($_FILES['img1']['error'] > 0){
            //there is an error
            $errCode = $_FILES['img1']['error'];
            if($errCode == 1 || $errCode == 2){
                $errors['img1'] = "The file uploaded for Image 1 is too large.";
            } elseif($errCode == 3){
                $errors['img1'] = "There was an error uploading Image 1";
            } elseif ($errCode == 4){
                $errors['img1'] = "No file was supplied for Image 1";
            } elseif ($errCode == 5 || $errCode == 6 || $errCode == 7){
                $errors['img1'] = "An internal error occured while uploading Image 1";
            }
        } elseif(!in_array($_FILES['img1']['type'], $allowedTypes)){
            //file type not allowed
            $errors['img1'] = "Incorrect file type for Image 1.";
        } elseif($_FILES['img1']['size'] > 1048576){ //file over 1MB
            //file too big
            $errors['img1'] = "Image 1 must be under 1MB";
        } 
    }
    
    $hasImg2 = false;
    
    if(isset($_FILES['img2']) && $_FILES['img2']['size'] > 0){
        $hasImg2 = true;
    
        if($_FILES['img2']['error'] > 0){
            //there is an error
            $errCode = $_FILES['img2']['error'];
            if($errCode == 1 || $errCode == 2){
                $errors['img2'] = "The file uploaded for Image 2 is too large.";
            } elseif($errCode == 3){
                $errors['img2'] = "There was an error uploading Image 2";
            } elseif ($errCode == 4){
                $errors['img2'] = "No file was supplied for Image 2";
            } elseif ($errCode == 5 || $errCode == 6 || $errCode == 7){
                $errors['img2'] = "An internal error occured while uploading Image 2";
            }
        } elseif(!in_array($_FILES['img2']['type'], $allowedTypes)){
            //file type not allowed
            $errors['img2'] = "Incorrect file type for Image 2.";
        } elseif($_FILES['img2']['size'] > 1048576){ //file over 1MB
            //file too big
            $errors['img2'] = "Image 2 must be under 1MB";
        }
    
    }
        
        
        if(count($errors) > 0){
            outputCharityForm($errors, $eventDetails);
        } else {
            $updateSQL = "UPDATE cms_events SET "
                    . "EditorID = '{$_SESSION['userID']}', "
                    . "EditedOn = NOW(), "
                    . "Start1 = '{$eventDetails['Start1']}', "
                    . "End1 = '{$eventDetails['End1']}', "
                    . "Name = '{$eventDetails['Name']}', "
                    . "Description = '{$eventDetails['Description']}', "
                    . "Contact = '{$eventDetails['Contact']}', "
                    . "Number1 = '{$eventDetails['Number1']}', "
                    . "Email = '{$eventDetails['Email']}', "
                    . "Location = '{$eventDetails['Location']}' "
                    . "WHERE EventID = {$eventID};";
            $updateResult = mysql_query($updateSQL);
            if($updateResult){
                outputCharityForm($errors, $eventDetails, true);
            } else{
                //TODO Output Error 
           }
        }
    } else {
        $detailsSQL = "SELECT * FROM cms_events WHERE EventID = {$eventID}";
        $detailsResult = mysql_query($detailsSQL);
        while($row = mysql_fetch_assoc($detailsResult)){
            $eventDetails = $row;
        }
        outputCharityForm($errors, $eventDetails);
    }

    
    function outputCharityForm(&$errors, &$eventDetails, $success = false){
        ?>
            <div class="jumbotron">
                <div class="container">
                  <h1>Edit Event Details</h1>
                  <p>Your event's details can be changed below.</p>

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
              <input name="name" id="name" type="text" class="form-control" autofocus required value="<?=isset($eventDetails['Name']) ? $eventDetails['Name'] : ''?>">
              
              <?php
                if(isset($eventDetails['Start1'])){
                    $date = date_parse_from_format("Y-m-d H:i:s", $eventDetails['Start1']);
                    $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
                    $eventDetails['Start1'] = date("d/m/Y H:i", $time);
                }
              ?>
              
              <label for="start">Start</label>
              <input placeholder="dd/mm/yyyy HH:MM" name="start" id="start" type="text" class="form-control" required value="<?=isset($eventDetails['Start1']) ? $eventDetails['Start1'] : ''?>">
              
              <?php
                if(isset($eventDetails['End1'])){
                    $date = date_parse_from_format("Y-m-d H:i:s", $eventDetails['End1']);
                    $time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
                    $eventDetails['End1'] = date("d/m/Y H:i", $time);
                }
              ?>
              
              <label for="end">End</label>
              <input placeholder="dd/mm/yyyy HH:MM" name="end" id="end" type="text" class="form-control" required value="<?=isset($eventDetails['End1']) ? $eventDetails['End1'] : ''?>">
              
              <label for="description">Description</label>
              <textarea name="description" id="description" class="form-control" required><?=isset($eventDetails['Description']) ? $eventDetails['Description'] : ''?> </textarea>
              
              <label for="contactName">Contact Name</label>
              <input name="contactName" id="contactName" type="text" class="form-control" required value="<?=isset($eventDetails['Contact']) ? $eventDetails['Contact'] : ''?>">
              
              <label for="number">Contact Number</label>
              <input name="number" id="number" type="tel" class="form-control" required value="<?=isset($eventDetails['Number1']) ? $eventDetails['Number1'] : ''?>">
              
              <label for="email">Contact Email</label>
              <input name="email" id="email" type="email" class="form-control" value="<?=isset($eventDetails['Email']) ? $eventDetails['Email'] : ''?>">
              
              <label for="location">Location</label>
              <textarea name="location" id="location" class="form-control" required><?=isset($eventDetails['Location']) ? $eventDetails['Location'] : ''?></textarea>
              
              <label for="img1">Image 1</label>
              <input name="img1" id="img1" class="form-control" type="file" />
              
              <label for="img2">Image 2</label>
              <input name="img2" id="img2" class="form-control" type="file" />
              
              <script>
                function story(type){
                    if(type === 0){ //add to existing
                        if(document.getElementById("existingStory").checked){
                            document.getElementById("newStory").checked = false;
                        }
                        <?php
                            $storiesSQL = "SELECT * FROM cms_stories WHERE CharityID = (SELECT CharityID FROM cms_charities WHERE DomainName = '{$_SESSION['lastDomain']}');";
                            $storiesResult = mysql_query($storiesSQL);
                            $options = "";
                            while($row = mysql_fetch_assoc($storiesResult)){
                                $options .= "<option value=\"{$row['StoryID']}\">{$row['title']}</option>";
                            }
                            echo "var options = '{$options}';";
                        ?>
                        var innerHTML = '<div class="dropdown"><select name="existingStoryID" id="existingStoryID" class="form-control" required>' + options + '</select></div>';
                        document.getElementById("storyEdit").innerHTML = innerHTML;
                    } else{ //type is 1 - add to new
                        if(document.getElementById("newStory").checked){
                            document.getElementById("existingStory").checked = false;
                        }
                        document.getElementById("storyEdit").innerHTML = '<label for="storyName">Story Name</label><input type="text" name="storyName" id="storyName" class="form-control" required />';
                    }
                    
                    if(!document.getElementById("existingStory").checked && !document.getElementById("newStory").checked){ //neither boxes checked
                        document.getElementById("storyEdit").innerHTML = '';
                    }
                }    
            </script>
              
              <div class="panel panel-default">
                  <div class="panel-heading">Optional</div>
                  <div class="panel-body">
                    <span class="pull-left">
                      <label for="existingStory">Add to existing story</label>
                      <input type="checkbox" name="existingStory" id="existingStory" onChange="story(0)"/>
                    </span>

                    <span class="pull-right">
                      <label for="newStory">Create new story</label>
                      <input type="checkbox" name="newStory" id="newStory"  onChange="story(1)"/>
                    </span>

                    <div id="storyEdit" class="clear-both">

                    </div>
                  </div>
              </div>
              
              <br/>
              
              <button type="submit" class="btn" id="submitButton" name="submission" value="true">Submit</button>
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

/**
 * @author http://ie1.php.net/checkdate#113205
 */
function validateDate($date, $format = 'd/m/Y H:i')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
