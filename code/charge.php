<?php
  require_once(dirname(__FILE__) . '/config.php');
  require_once 'database_functions.php';
  connect_to_database();
  
  $token  = $_POST['token'];
  $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
  $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
  $charityID = filter_input(INPUT_POST, 'charityID', FILTER_VALIDATE_INT);
  $charityNameSQL = "SELECT Name from cms_charities WHERE CharityID = {$charityID};";
  $charityNameResult = mysql_query($charityNameSQL);
  $charityName = mysql_result($charityNameResult, 0);
 
  $customer = Stripe_Customer::create(array(
      'email' => "Donation - {$charityName}",
      'card'  => $token
  ));
 
      $statementDescription = substr($charityName . ' Donation', 0, 15);
      
  $charge = Stripe_Charge::create(array(
      'customer' => $customer->id,
      'amount'   => $amount,
      'currency' => 'eur',
      'statement_description' => $statementDescription
  ));
  
  $amount /= 100;
  
  //TODO insert pageID & ContantID
  
  $donationSQL = "INSERT INTO cms_donations (CharityID, Message, Amount, Timestamp1) "
               . "VALUES ({$charityID}, '{$message}', {$amount}, NOW());";
  $donationResult = mysql_query($donationSQL);
               
  if(!$donationResult){
      echo $donationSQL;
      die();
  }
 
  echo 'true';
  
?>
