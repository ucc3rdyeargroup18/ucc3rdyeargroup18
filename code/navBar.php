<?php
/* 
 * @Author: Cathal Denis Toomey (1113025911) <111302591@umail.ucc.ie>
 * Script to create dynamic navigation bar 
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start(); //start a session if one does not exist
}
require 'getDetails.php';
?>

<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" id="navBarCol" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/<?=$charityDomain?>/home"><?=$info['Name']?></a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
          <?php
            $currentURLRaw = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
            $currentURLArray = explode('/', $currentURLRaw);
            $arrayLength = count($currentURLArray);
            $currentURL = $currentURLArray[$arrayLength-1];
            //loop through the array of pages to be added to the nav bar
            /*foreach($info['nav'] as $title => $url){
                if(!is_array($url)){
                    echo "<li";
                    echo $url == $currentURL ? ' class="active"' : ''; //if this is the current page, apply active class
                    echo "><a href=\"{$url}\">{$title}</a>";
                } else { //the nav item is a dropdown
                    echo '<li class="dropdown';
                    echo in_array($currentURL, $url) ? ' active' : ''; //if current page is in dropdown, apply active class
                    echo '">';
                    echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
                    echo $title;
                    echo '<b class="caret"></b></a>';
                    echo '<ul class="dropdown-menu">';
                    foreach($url as $innerTitle => $innerUrl){
                        echo "<li><a href=\"{$innerUrl}\">{$innerTitle}</a></li>";
                    }
                    echo '</ul></li>'; //close the dropdown ul & li
                }
            }*/
            
            foreach($info['nav'] as $page){
                $explode = explode('.', $page['FileName']);
                $explodeLen = count($explode);
                echo "<li";
                echo $page['FileName'] == $currentURL ? ' class="active"' : ''; //if this is the current page, apply active class
                if($explode[$explodeLen-1] == "php"){
                    echo  "><a href=\"{$page['FileName']}\">";
                } else {
                    echo "><a href=\"/{$charityDomain}/{$page['FileName']}\">";
                }
                echo $page['CustomTitle'] == '' ? $page['Name'] : $page['CustomTitle'];
                echo"</a>";
            }
          ?>
      </ul>
        
            <?php
              if($currentURL != "login.php"):
                if(isset($_SESSION['validUser']) && $_SESSION['validUser']):
                    echo '<div class="col-sm-3 col-md-4 pull-right">';
                    echo '<ul class="nav navbar-nav pull-right">';
                    echo '<li>';
                    echo '<a href="/' . $charityDomain . '/cms">Go to Admin Panel</a>';
                    echo '</li>';
                    echo '<li>';
                    echo '<a href="/logout.php">Logout</a>';
                    echo '</li></ul>';
                else:
           ?>
            <div class="col-sm-3 col-md-4 pull-right" style="padding-top: 8px; padding-bottom: 8px;">
                <form class="navbar-search pull-right" action="/login.php" method="post">
                    <div class="input-group">
                        <input type="email" class="form-control login-form-control" style="width:49%;" placeholder="Email Address" name="email">
                        <input type="password" class="form-control login-form-control" style="width:49%;" placeholder="Password" name="password">
                        <input type="hidden" name="redirectURL" value="<?=$currentURLRaw?>">
                        <div class="input-group-btn login-btn">
                            <input type="submit" name="submit" value="Login" class="btn"/>
                        </div>
                    </div>

                </form>
            <?php endif;
            echo '</div>';
                endif; ?>
    </div><!--/.nav-collapse -->
  </div>
</div>
