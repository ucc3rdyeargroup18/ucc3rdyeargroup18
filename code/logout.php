<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
if(isset($_SESSION['lastDomain']) && !empty($_SESSION['lastDomain'])){
    $lastDomain = $_SESSION['lastDomain'];
} else {
    $lastDomain = "home";
}
session_destroy();
header('Refresh: 0; URL=' . $lastDomain . '/home');
echo '<h1>You have been logged out! You will now be redirected</h1>';
