<?php

/**
 * Header template for charity systems manager
 * Retrieves page info including domain name and page name
 * for use in SQL queries
 * Executes SQL Queries to retrieve page content
 * Outputs beginning of HTML document
 * @author Cathal Denis Toomey (111302591)
 */

require_once 'database_functions.php';
connect_to_database();

require_once 'getDetails.php';
//require_once 'header.include.html';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=$info['Name']?></title>

    <!-- tablesorter CSS -->
    <link href="/css/tablesort.theme.default.css" rel="stylesheet">
    
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/sticky-footer-navbar.css" rel="stylesheet">
    
    <!-- Include CSS & JS for WYSIWIG Editor -->
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-wysihtml5.css" />
    <script src="/js/wysihtml5-0.3.0.js"></script>
    <script src="/js/jquery-1.7.2.min.js"></script>
    <script src="/js/bootstrap3-wysihtml5.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script>
        window.onload = function() {
            document.getElementById("navBarCol").style.backgroundColor = "#<?=$colours['Color1']?>";
            document.getElementById("footer").style.backgroundColor = "#<?=$colours['Color1']?>";
            elements = document.getElementsByClassName("panel");
            for (var i = 0; i < elements.length; i++) {
                elements[i].style.backgroundColor="#<?=$colours['Color2']?>";
            }
            document.body.style.backgroundColor = "#<?=$colours['Color2']?>";
            document.body.style.color = "#<?=$colours['Color3']?>";
        };
    </script>
  </head>

  <body>

    <!-- Wrap all page content here -->
    <div id="wrap">
<?php
require_once 'navBar.php';
