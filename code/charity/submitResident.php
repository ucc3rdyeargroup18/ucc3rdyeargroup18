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
    //TODO Make datetime standard across browsers
    $petDetails['Description'] = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $petDetails['ContactName'] = trim(filter_input(INPUT_POST, 'contactName', FILTER_SANITIZE_STRING));
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);
    $petDetails['Email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $petDetails['Location'] = trim(filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING));
        
    if($petDetails['Name'] === ""){
        $errors['name'] = "Please enter the animal's name";
    }
    if($petDetails['Description'] === ""){
        $errors['description'] = "Please enter a description of the animal";
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
    $petDetails['Number'] = validate_number($number);
    if(!$petDetails['Number']){
        $errors['Number'] = "Please enter a valid phone number";
        $petDetails['Number'] = $number;
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
    if($_FILES['img1']['size'] <= 0){
        $errors['img1'] = "Image 1 is required";
    } elseif($_FILES['img1']['error'] > 0){
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
    
    $hasImg2 = false;
    
    if($_FILES['img2']['size'] > 0){
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
        output_form($errors, $petDetails);
    } else {
        $insertSQL = "INSERT INTO cms_pets (CharityID, CreatorID, CreatedOn, EditorID, EditedOn, Name, Description, Contact, Number1, Email, Location, isAdoptable)"
                . "VALUES((SELECT CharityID FROM cms_charities WHERE DomainName = '{$_SESSION['lastDomain']}'),"
                . "{$_SESSION['userID']}, NOW(), {$_SESSION['userID']}, NOW(), '{$petDetails['Name']}', '{$petDetails['Description']}', '{$petDetails['ContactName']}', '{$petDetails['Number']}', '{$petDetails['Email']}', '{$petDetails['Location']}', 0);";
        
        $insertResult = mysql_query($insertSQL);
        $petInsertID = mysql_insert_id();
     //attempt to store the file
        $path = $_FILES['img1']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $filename = $petInsertID . "_a." . $ext;
        $file = file_get_contents($_FILES['img1']['tmp_name']);
        file_put_contents("C:\wamp\www\\3rdyearproj\images\pets\\" . $filename, $file);
        //move_uploaded_file seems to create the file, put doesn't moce the contents
        //move_uploaded_file($_FILES['img1']['tmp_name'], "C:\wamp\www\\3rdyearproj\images\lostAndFound\\" . $filename);
        $imgSQL = "UPDATE cms_pets SET Image1 = '{$filename}'";
        $petDetails['Image1'] = $filename;
        if($hasImg2){
            $path = $_FILES['img2']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $filename = $petInsertID . "_b." . $ext;
            $file = file_get_contents($_FILES['img2']['tmp_name']);
            file_put_contents("C:\wamp\www\\3rdyearproj\images\pets\\" . $filename, $file);
            $imgSQL .= ", Image2 = '{$filename}'";
        }
        $imgSQL .= " WHERE PetID = {$petInsertID};";
        $imgResult = mysql_query($imgSQL);
        $petDetails['Image2'] = $filename;
        if($imgResult){
            //output success
    
        //INSERT INTO CMS_Content
        $insertContentSQL = "INSERT INTO CMS_Content VALUES (null, 'CMS_Pets', '" . $petInsertID . "')";
        $insertContentResult = mysql_query($insertContentSQL);
        //GET ContentINsertID
        $insertContentID = mysql_insert_id();
        if($newStory || $existingStory){
            if($newStory){
                //INSERT INTO CMS_STORIES
                $insertStorySQL = "INSERT INTO CMS_Stories VALUES (null, '{$storyName}', '" . $info['CharityID'] . "')";
                $insertStoryResult = mysql_query($insertStorySQL);
                //GET STORYINSERTID
                $insertStoryID = mysql_insert_id();
            } else {
                //GETSTORYID FROM DDL VALUE
                $insertStoryID = $storyID;
            }
            //INSERT INTO CMS_StoryCONTENT
            $insertStoryContentSQL = "INSERT INTO CMS_StoryContent VALUES (null, '". $insertStoryID . "', '" . $insertContentID . "')";
            $insertStoryContentResult = mysql_query($insertStoryContentSQL);
    }
           ?>
<!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>Submit Resident</h1>
        </div>
          <div class="col-md-9">
        <p class="lead">Your resident animal has been added to our database.</p>
        <hr />
        
        <div class="well">
            <img class="img-circle" data-src="holder.js/140x140" alt="140x140" style="width: 140px; height: 140px;" src="/images/pets/<?=$petDetails['Image1']?>" />
              <h2>
            <?=$petDetails['Name']?>
            </h2>
            <p>
                Description:<br />
                <?=$petDetails['Description']?>
            </p>
            Contact Name: <?=$petDetails['ContactName']?> <br />
            Contact Number: <?=$petDetails['Number']?><br />
            Contact Email: <?=$petDetails['Email']?>
            <?php
                if($hasImg2){
                    echo '<img class="img-circle" data-src="holder.js/140x140" alt="140x140" style="width: 140px; height: 140px;" src="/images/pets/' . $petDetails['Image2'] . '" />';
                }
            ?>

        </div>
        
          </div> <!-- /.col-md-9 -->
            <?php
        }
    }
} else {
    output_form($errors, $petDetails);
}

function output_form(&$errors, &$petDetails){
    ?>

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>Submit Resident</h1>
        </div>
          <div class="col-md-9">
        <p class="lead">You can use the form below to create a listing for an animal that is a resident</p>
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
              
              <label for="description">Description</label>
              <textarea name="description" id="description" class="form-control" required><?=isset($petDetails['Description']) ? $petDetails['Description'] : ''?> </textarea>
              
              <label for="contactName">Contact Name</label>
              <input name="contactName" id="contactName" type="text" class="form-control" required value="<?=isset($petDetails['ContactName']) ? $petDetails['ContactName'] : ''?>">
              
              <label for="number">Contact Number</label>
              <input name="number" id="number" type="tel" class="form-control" required value="<?=isset($petDetails['Number']) ? $petDetails['Number'] : ''?>">
              
              <label for="email">Contact Email</label>
              <input name="email" id="email" type="email" class="form-control" value="<?=isset($petDetails['Email']) ? $petDetails['Email'] : ''?>">
              
              <label for="location">Location</label>
              <textarea name="location" id="location" class="form-control" required><?=isset($petDetails['Location']) ? $petDetails['Location'] : ''?></textarea>
              
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
