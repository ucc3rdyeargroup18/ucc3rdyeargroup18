<?php
$con = mysql_connect("localhost","name","password");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("my_db", $con);

mysql_query("INSERT INTO CMS_CharityPages (CharityID, PageID,CustomTitle) 
VALUES ( '$_POST[CharityID]', '$_POST[PageID]','$_POST[CustomTitle]')");
mysql_close($con);
?>
