<?php

/**
 * Script to check if user info is valid and to log the user in
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start(); //start a session if one does not exist
}
if(isset($_SESSION['validUser']) && $_SESSION['validUser']){//user is already logged in
    header("Location: /cms.php");
}

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );

require_once 'database_functions.php';
connect_to_database();
$errors = array();

    $submitted = filter_input(INPUT_POST, 'submit', FILTER_SANITIZE_STRING);
    if(!$submitted || $submitted != "Login"){ //if the login form has not been submitted
        $redirectURL = false;
        output_login_form($redirectURL, $errors);
    } //else info has been supplied
        
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password');
    $redirectURL = filter_input(INPUT_POST, 'redirectURL', FILTER_SANITIZE_URL);
    
    if(!$email){
        $errors['email'] = "Please enter a valid email address";
    }
    
    if(empty($password)){
        $errors['password'] = "You must enter a password";
    }
    
    if(count($errors) > 0){ //there are errors to fix
        output_login_form($redirectURL, $errors);
    } //else the info supplied is valid
    
    $passwordHash = md5($password);
    $loginSQL = "SELECT UserID, FirstName, Admini FROM cms_users WHERE EmailAddress = '{$email}' AND EncryptedPassword = '{$passwordHash}' LIMIT 1";
    $loginResult = mysql_query($loginSQL);
    echo mysql_error();
    if(!$loginResult || mysql_num_rows($loginResult) != 1){
        $errors['password'] = "Username/Password incorrect";
        output_login_form($redirectURL, $errors);
    } //else the user was found in the database
    
    $_SESSION['validUser'] = true;
    $_SESSION['email'] = $email;
    $_SESSION['userID'] = mysql_result($loginResult, 0, 'UserID');
    $_SESSION['siteAdmin'] = mysql_result($loginResult, 0, 'Admini') == '1' ? true : false;
    $_SESSION['firstName'] = mysql_result($loginResult, 0, 'FirstName');
    
    header('Refresh: 5; URL=' . $redirectURL);
    echo '<h1>Welcome back ' . $_SESSION['firstName'] . '! You will now be redirected';
    
    /**
     * Outputs the login form
     * 
     * Outputs a login form for the site 
     * and displays any errors that occured on the previous login attempt
     * 
     * @author Cathal Denis Toomey
     */
    function output_login_form($redirectURL, &$errors){
        
        require 'header.php';
        
        //require 'header.include.html';
        //require 'navBar.php';
        
        ?>
<div class="container">
<?php
    if(count($errors) > 0){
        echo '<div class="alert alert-danger alert-dismissable">';
            echo "<strong>We Found ";
            echo count($errors) == 1 ? 'an Error' : 'Some Errors';
            echo ": </strong><br />";
            foreach($errors as $error){
                echo '<span class="glyphicon glyphicon-warning-sign"></span>&nbsp;&nbsp;';
                echo $error . '<br />';
            }
            echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        echo '</div>';
    }
?>
    <form class="form-signin" role="form" method="post" action="/login.php">
            <h2 class="form-signin-heading">Please sign in</h2>
            <input name="email" type="email" class="form-control" placeholder="Email address" required autofocus>
            <input name="password" type="password" class="form-control" placeholder="Password" required>
            <input name="redirectURL" type="hidden" value="<?=$redirectURL?>" />
            <br />
            <button class="btn btn-lg btn-primary btn-block" type="submit"  name="submit" value="Login">Sign in</button>
    </form>
    <br />
    <div class="lead text-center">
        Don't have an account? <a href="/register.php">Register Here!</a>
    </div>
</div>
<?php

    require 'footer.php';
    
    die();
    } //end function output_login_form
?>
