<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$referer = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
$refererArray = explode('/', $referer);
$domain = $refererArray[3];
$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );
require_once 'database_functions.php';
connect_to_database();

$donationsSQL = "SELECT * FROM cms_donations WHERE CharityID = (SELECT CharityID FROM cms_charities WHERE DomainName = '{$domain}')";
$donationResult = mysql_query($donationsSQL);

$donations = array();
$totalDonations = 0;

while($row = mysql_fetch_assoc($donationResult)){
    $donations[] = $row;
    $totalDonations += $row['Amount'];
}
?>

<div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Number</th>
          <th>Donation ID</th>
          <th>Date</th>
          <th>Time</th>
          <th>Message</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
          <?php
            $rowNumber = 1;
            foreach($donations as $donation) {
                $timestamp = strtotime($donation['Timestamp1']);
                $date = date("d F Y", $timestamp);
                $time = date("H:i", $timestamp);
                echo '<tr>';
                echo "<td>" . $rowNumber++ . "</td>";
                echo "<td>{$donation['DonationID']}</td>";
                echo "<td>{$date}</td>";
                echo "<td>{$time}</td>";
                echo "<td>{$donation['Message']}</td>";
                echo "<td>&euro;{$donation['Amount']}</td>";
                echo '</tr>';
            }
             echo '<tr class="donationsTotal">';
            echo "<td colspan=\"4\"></td>";
            echo "<td>Total: </td>";
            echo "<td>&euro;" . number_format($totalDonations, 2) . "</td>";
            echo '</tr>'; 
           ?>
      </tbody>
    </table>
</div>
