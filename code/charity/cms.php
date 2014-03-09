<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('SECURE', true);

//TODO check if the user is logged in and what priveleges they have
//TODO redirect the user to the login form if they are not logged in

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );

if (session_status() == PHP_SESSION_NONE) {
    session_start(); //start a session if one does not exist
}
if(!isset($_SESSION['validUser']) || !$_SESSION['validUser']){//user is not logged in
    header("Location: /login.php?auth");
}

require_once 'getDetails.php';
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>Charity Hosting Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script>
        function new_aso(){
                var aso;
                try {// Firefox, Opera 8.0+, Safari
                        aso=new XMLHttpRequest();
                } catch (e) {// Internet Explorer
                        try {
                                aso=new ActiveXObject("Msxml2.XMLHTTP")
                        } catch (e) {
                                try {
                                        aso=new ActiveXObject("Microsoft.XMLHTTP")
                                } catch (e) {
                                        alert("Your browser does not support AJAX!");
                                }
                        } 	
                }
        return aso;
        }
        
        var pages = new Array();
        pages[0] = "editCharityDetails.php";
        pages[1] = "editTemplate.php";
        var current = -1;
        function content(id){
            current = id;
            var file = "/cms/" + pages[id];
            var ASO = new_aso();
            var elems = document.getElementsByTagName('ul'), i;
            for (i in elems) {
                if((' ' + elems[i].className + ' ').indexOf(' ' + 'nav-sidebar' + ' ')
                        > -1) {
                    var nodes = elems[i].childNodes;
                    for(j=0; j<nodes.length; j++) {
                        if(nodes[j].nodeName === "LI"){
                            //nodes[j].className = "active";
                            if (nodes[j].className.match(/active/) ){
                                var className = nodes[j].className;
                                className = className.replace( /active/gi , '' );
                                nodes[j].className = className;
                            }
                        }
                    }
                }
            }

            document.getElementById("cmsContent").innerHTML="<h4>Loading Content...</h4><img style=\"height: 200px;\" src=\"ajax-loader.gif\" />";
            //document.getElementById("requestTime").innerHTML="";
            ASO.onreadystatechange=function(){
                    if(ASO.readyState===4){
                            if(ASO.status===200){
                                    document.getElementById("cmsContent").innerHTML=ASO.responseText;
                                    document.getElementById("cmsItem" + id).className += " active";
                            } else if (ASO.status === 404){
                                    document.getElementById("cmsContent").innerHTML="The requested file was not found";
                            } else{
                                    document.getElementById("cmsContent").innerHTML="An Error occurred while retrieving the file.";
                            }
                    }
            };
            ASO.open("GET", file, true);
            ASO.send(null);
        }
        
        function submitForm(){
            //document.getElementById("submitButton").className = "btn btn-info";
            var requirementsMet = true;
            var form = document.forms.cmsForm;
            var params = "";
            for( i=0; i<form.length; i++){
                var element = form.elements[i];
                if(element.hasAttribute("required") && element.value === ""){
                    requirementsMet = false;
                } else {
                    params += element.getAttribute("name") + "=" + encodeURIComponent(element.value) + "&";
                }
            }
            if(!requirementsMet){
                //document.getElementById("submitButton").className = "btn btn-warning";
                return false;
            }
            
            var ASO = new_aso();
            ASO.onreadystatechange=function(){
                    if(ASO.readyState===4){
                            if(ASO.status===200){
                                    document.getElementById("cmsContent").innerHTML=ASO.responseText;
                                    //document.getElementById("submitButton").className = "btn btn-success";
                                    return false;
                            } else if (ASO.status === 404){
                                    document.getElementById("cmsContent").innerHTML="The requested file was not found";
                                    return false;
                            } else{
                                    document.getElementById("cmsContent").innerHTML+="An Error occurred while processing your request.";
                                    //document.getElementById("submitButton").className = "btn btn-danger";
                                    return false;
                            }
                    }
            };
            ASO.open("POST", "/cms/" + pages[current], false);
            ASO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ASO.send(params);
            return false;
        }
    </script>
  </head>

  <body>
      
      <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><?=$info['Name']?></a>
        </div>
          
          <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">View Site</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Your Charities <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Find A Hen</a></li>
                  <li><a href="#">Find A Cat</a></li>
                </ul>
              </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Help <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Tutorials</a></li>
                  <li><a href="#">FAQ</a></li>
                </ul>
              </li>
            <li><a href="/logout.php">Logout</a></li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>
        </div>
          </div>
    </div>
      
      <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar"><!-- pages all charities will need -->
            <li id="cmsItem0"><a href="javascript:content(0);">Edit Charity Details</a></li>
            <li id="cmsItem1"><a href="javascript:content(1);">Edit Theme</a></li>
            <li id="cmsItem2"><a href="javascript:content(2);">Edit Charity Pages</a></li>
            <li id="cmsItem3"><a href="javascript:content(3);">Edit Home Page</a></li>
          </ul>
          <ul class="nav nav-sidebar">
              <li><a href="">Manage Pages</a></li>
              <li><a href="">Manage Users</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="#">Overview</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="#">Analytics</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          

        <div id="cmsContent">
            Loading...
            <? //TODO change initial content
            ?>
        </div>
        </div>
      </div>
    </div>
      
      <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/docs.min.js"></script>
  </body>
</html>
