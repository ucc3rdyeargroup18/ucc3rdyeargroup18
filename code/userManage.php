<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('SECURE', true);

$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
set_include_path( get_include_path() . PATH_SEPARATOR . $documentRoot );

if (session_status() == PHP_SESSION_NONE) {
    session_start(); //start a session if one does not exist
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    header("Location: /login.php?timeout");
    die();
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if(!isset($_SESSION['validUser']) || !$_SESSION['validUser']){//user is not logged in
    header("Location: /login.php?auth");
    die();
}

require_once 'getDetails.php';

$authSQL = "SELECT * FROM cms_charityusers WHERE UserID = {$_SESSION['userID']} AND CharityID = {$info['CharityID']}";
$authResult = mysql_query($authSQL);
$noAuth = false;
if(mysql_num_rows($authResult) != 1){//the user is not authorised for this charity
    $noAuth = true;
}
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

    <title>User Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">
    
    <!-- Color Picker CSS -->
    <link href="/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    
    <!-- tablesorter CSS -->
    <link href="/css/tablesort.theme.default.css" rel="stylesheet">
    
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
    
    <?php
     if($noAuth){
         echo '</head>';
         echo '<body>';
         cms_top_nav();
         echo '<div class="jumbotron">
                <div class="container">
                  <h1>Authorisation Failed</h1>
                  <p>You are not authorised to manage this charity</p>';
                echo '</div>
              </div>';
                echo '<div class="well">';
                echo '<div class="container">';
                  echo '<p>You are logged in as ' . $_SESSION['email'] . '</p>';
                  echo '<p>You can find a list of charities your have authorisation for below.</p>';
                  echo '<ul>';
                  get_authd_charities();
                  echo '</ul>';
                  echo '</div>';
                  echo '</div>';
         cms_footer();
         die();
     }
    ?>
    
    <script>
        function new_aso(){
                var aso;
                try {// Firefox, Opera 8.0+, Safari
                        aso=new XMLHttpRequest();
                } catch (e) {// Internet Explorer
                        try {
                                aso=new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                                try {
                                        aso=new ActiveXObject("Microsoft.XMLHTTP");
                                } catch (e) {
                                        alert("Your browser does not support AJAX!");
                                }
                        } 	
                }
        return aso;
        }
        
        var pages = new Array();
        pages[0] = "editUserDetails.php";
        pages[1] = "managePosts";
        pages[2] = "userPageManagement.php";
        pages[3] = "changePassword.php";
        var current = -1;
        function content(id){
            current = id;
            var file = "/user/" + pages[id];
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

            document.getElementById("cmsContent").innerHTML="<h4>Loading Content...</h4><img style=\"height: 200px;\" src=\"/img/ajax-loader.gif\" />";
            //document.getElementById("requestTime").innerHTML="";
            ASO.onreadystatechange=function(){
                    if(ASO.readyState===4){
                            if(ASO.status===200){
                                    document.getElementById("cmsContent").innerHTML=ASO.responseText;
                                    document.getElementById("cmsItem" + id).className += " active";
                                    onAJAXLoad();
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
                    if(element.getAttribute("type") === "checkbox"){
                        params += element.getAttribute("name") + "=" + encodeURIComponent(element.checked) + "&";
                    } else {
                        params += element.getAttribute("name") + "=" + encodeURIComponent(element.value) + "&";
                    }
                }
            }
            if(current === 1){
                params += "file=" + logoData;
               // document.body.innerHTML = "<pre>" + params + "</pre>";
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
                                    onAJAXLoad();
                                    return false;
                            } else if (ASO.status === 404){
                                    document.getElementById("cmsContent").innerHTML="The requested file was not found";
                                    return false;
                            } else{
                                    alert(ASO.responseText);
                                    document.getElementById("cmsContent").innerHTML+="An Error occurred while processing your request.";
                                    //document.getElementById("submitButton").className = "btn btn-danger";
                                    return false;
                            }
                    }
            };
            ASO.open("POST", "/user/" + pages[current], false);
            ASO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ASO.send(params);
            return false;
        }
        
        function submitLogo()
        {
            //TODO handle the file upload
            alert("An Unknown Error Occurred!");
        }
            
        function onAJAXLoad(){
            //initialise color pickers
            $('.color').colorpicker();
            
            $('.wysiwyg-textarea').wysihtml5({"link": false, "image": false});
        }
        function removeAccess(accessID){
            var ASO = new_aso();
            var params = "remove=" + encodeURIComponent(accessID);
            ASO.onreadystatechange=function(){
                document.getElementById("remove" + accessID).innerHTML= "Request Pending";
                document.getElementById("remove" + accessID).setAttribute("disabled", "disabled");
                    if(ASO.readyState===4){
                            if(ASO.status===200){
                                if(ASO.responseText === "true"){
                                    document.getElementById("remove" + accessID).innerHTML= "Access Revoked";
                                 } else {
                                     document.getElementById("remove" + accessID).innerHTML= "Error Occured";
                                 }
                                    return false;
                            } else{
                                    document.getElementById("remove" + accessID).innerHTML= "Error Occured";
                                    return false;
                            }
                    }
            };
            ASO.open("POST", "/user/revokeAccess.php", false);
            ASO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ASO.send(params);
            return false;
        }    
        function requestAccess(){
            var ASO = new_aso();
            var e = document.getElementById("selectCharity");
            var params = "charity=" + encodeURIComponent(e.options[e.selectedIndex].value);
            e = document.getElementById("selectPage");
            params += "&page=" + encodeURIComponent(e.options[e.selectedIndex].value);
            ASO.onreadystatechange=function(){
                document.getElementById("requestAccess").innerHTML= "Request Pending";
                document.getElementById("requestAccess").setAttribute("disabled", "disabled");
                    if(ASO.readyState===4){
                            if(ASO.status===200){
                                if(ASO.responseText === "true"){
                                    document.getElementById("requestAccess").innerHTML= "Access Requested";
                                    setTimeout(function () {
                                        document.getElementById("requestAccess").innerHTML= "Request Access";
                                        document.getElementById("requestAccess").removeAttribute("disabled");
                                    }, 2000);
                                 } else if(ASO.responseText === "already"){
                                       document.getElementById("requestAccess").innerHTML= "Already Requested";
                                        setTimeout(function () {
                                            document.getElementById("requestAccess").innerHTML= "Request Access";
                                            document.getElementById("requestAccess").removeAttribute("disabled");
                                        }, 2000);
                                 } else {
                                     document.getElementById("requestAccess").innerHTML= "Error Occured";
                                      setTimeout(function () {
                                            document.getElementById("requestAccess").innerHTML= "Request Access";
                                            document.getElementById("requestAccess").removeAttribute("disabled");
                                        }, 2000);
                                 }
                                    return false;
                            } else{
                                    document.getElementById("requestAccess").innerHTML= "Error Occured";
                                     setTimeout(function () {
                                            document.getElementById("requestAccess").innerHTML= "Request Access";
                                            document.getElementById("requestAccess").removeAttribute("disabled");
                                        }, 2000);
                                    return false;
                            }
                    }
            };
            ASO.open("POST", "/user/requestAccess.php", false);
            ASO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ASO.send(params);
            return false;
        }    
    </script>
  </head>

  <body onload="content(0);">
      
      <?php
      cms_top_nav();
      ?>
      
      <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar"><!-- pages all charities will need -->
            <li class="navbar-section-header">Manage Site</li>
            <li id="cmsItem0"><a href="javascript:content(0);">Edit User Details</a></li>
            <li id="cmsItem3"><a href="javascript:content(3);">Change Your Password</a></li>
            <li id="cmsItem1"><a href="javascript:content(1);">Manage Your Posts</a></li>
            <li id="cmsItem2"><a href="javascript:content(2);">Your Pages</a></li>
          </ul>
          <ul class="nav nav-sidebar">
              <li class="navbar-section-header">Manage Charities</li>
              <?php                    
              get_authd_charities();
              ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          

        <div id="cmsContent">
            
        </div>
        </div>
      </div>
    </div>
      
      <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--script src="https://code.jquery.com/jquery-1.10.2.min.js"></script-->
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/docs.min.js"></script>
    <script src="/js/bootstrap-colorpicker.js"></script>
  </body>
</html>

<?php

function cms_top_nav(){
    global $info, $charityDomain;
    ?>
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
            <li><a href="/<?=$charityDomain?>/home">View Site</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Your Charities <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <?php
                        get_authd_charities();
                    ?>
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
<?php
}

function cms_footer(){
    ?>
      <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--script src="https://code.jquery.com/jquery-1.10.2.min.js"></script-->
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/docs.min.js"></script>
    <script src="/js/bootstrap-colorpicker.js"></script>
  </body>
</html>
<?php
}

function get_authd_charities(){
    $authCharitiesSQL = "SELECT CharityID FROM cms_charityusers WHERE UserID = {$_SESSION['userID']}";
    $authCharitiesResult = mysql_query($authCharitiesSQL);
    $charityDetailsSQL = "SELECT Name, DomainName FROM cms_charities WHERE"; 
    $numRows = mysql_num_rows($authCharitiesResult);
    $i = 1;
    if($numRows > 0){
        while($row = mysql_fetch_assoc($authCharitiesResult)){
            $charityDetailsSQL .= " CharityID = ";
            $charityDetailsSQL .= $row['CharityID'];
            if($numRows != $i){
                $charityDetailsSQL .= " OR";
            }
            $i++;
        }
        $charityDetailsResult = mysql_query($charityDetailsSQL);
        while($row = mysql_fetch_assoc($charityDetailsResult)){
            echo "<li><a href=\"/{$row['DomainName']}/cms\">{$row['Name']}</a></li>";
        }
    } else {
        echo "<li><a href=\"/registerCharity.php\">No Charities Found</a></li>";
        echo "<li><a href=\"/registerCharity.php\">Register your Charity</a>";
    }
}
