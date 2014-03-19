<?php

    $referer = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
    $refererArray = explode('/', $referer);
    $domain = $refererArray[3];
    $documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
    set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
    require_once 'database_functions.php';
    connect_to_database();
    $errors = array();
    $colours = array();
    if(isset($_POST['submission']) && $_POST['submission'] === "true"){ // the form has been submitted, process it
        $colour1 = filter_input(INPUT_POST, 'headerColour', FILTER_SANITIZE_STRING);
        if (!preg_match('/^#[a-f0-9]{6}$/i', $colour1)) {
            $errors['Color1'] = "Header Colour is not valid";
        } else {
            $colours['Color1'] = str_replace('#', '', $colour1);
        }
        $colour2 = filter_input(INPUT_POST, 'backgroundColour', FILTER_SANITIZE_STRING);
        if (!preg_match('/^#[a-f0-9]{6}$/i', $colour2)) {
            $errors['Color2'] = "Background Colour is not valid";
        } else {
            $colours['Color2'] = str_replace('#', '', $colour2);
        }
        $colour3 = filter_input(INPUT_POST, 'textColour', FILTER_SANITIZE_STRING);
        if (!preg_match('/^#[a-f0-9]{6}$/i', $colour3)) {
            $errors['Color3'] = "Text Colour is not valid";
        } else {
            $colours['Color3'] = str_replace('#', '', $colour3);
        }
        
        //TODO Handle File Upload
        
        if(count($errors) > 0){
            outputTemplateForm($errors, $colours);
        } else {
            $updateSQL = "UPDATE cms_charitylayout SET "
                    . "Color1 = '{$colours['Color1']}', "
                    . "Color2 = '{$colours['Color2']}', "
                    . "Color3 = '{$colours['Color3']}' "
                    . "WHERE CharityID = "
                        . "(SELECT CharityID FROM cms_charities WHERE DomainName = '{$domain}');";
            $updateResult = mysql_query($updateSQL);
            if($updateResult){
                outputTemplateForm($errors, $colours, true);
            } else{
                //TODO Output Error
            }
        }        
        
    } else {
        $coloursSQL = "SELECT * FROM cms_charityLayout WHERE CharityID = "
                . "(SELECT CharityID FROM cms_charities WHERE DomainName = '{$domain}');";
        $coloursResult = mysql_query($coloursSQL);
        while($row = mysql_fetch_assoc($coloursResult)){
            $colours = $row;
        }
        outputTemplateForm($errors, $colours);
    }


function outputTemplateForm(&$errors, &$colours, $success = false){
    ?>
        <div class="jumbotron">
      <div class="container">
        <h1>Change Site Template</h1>
        <p>You can change how your site looks with the form below.</p>
        
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
                        echo '<span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Your sites colour scheme has been updated!';
                    echo '</div>';
                }
                  ?>

<form name="cmsForm" enctype="multipart/form-data" onsubmit="return submitForm()">
    
    <label for="headerColour">Select your Header colour: </label>
    <div class="input-group color">
        <input name="headerColour" id="headerColour" type="text" value="#<?=$colours['Color1']?>" class="form-control" required/>
        <span class="input-group-addon"><i></i></span>
    </div>

    <label for="backgroundColour">Select your Background colour: </label>
    <div class="input-group color">
        <input name="backgroundColour" id="backgroundColour" type="text" value="#<?=$colours['Color2']?>" class="form-control" required/>
        <span class="input-group-addon"><i></i></span>
    </div>
    
    <label for="textColour">Select your Text colour: </label>
    <div class="input-group color">
        <input name="textColour" id="textColour" type="text" value="#<?=$colours['Color3']?>" class="form-control" required/>
        <span class="input-group-addon"><i></i></span>
    </div>

<label for="file">Upload your logo: </label>

<input type="file" name="file" id="file"  /> 
<br />


<br/>
<button type="submit" name="submission" value="true" class="btn">Submit</button>
<button type="reset" name="reset" class="btn">Reset</button>


</form>

</div> <!-- /.container -->

<script>
    // Check for the various File API support.
    if (window.File && window.FileReader && window.FileList && window.Blob) {
      // Great success! All the File APIs are supported.
      alert("yes");
    } else {
      alert('The File APIs are not fully supported in this browser.');
    }    
</script>
    <?php
}
