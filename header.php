<?php
	$addr = ".";
	for ($x = 0; $x < $levels; $x++) {
		$addr = $addr . ".";
	}
	session_start();
	ini_set('display_errors', 'On');   // error checking
	error_reporting(E_ALL);    // error checking

echo '
<!DOCTYPE html>
<html lang="en" id="kahuna">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Doctor Logger </title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="http://www.polyphasic.xyz/header.css">


  <!-- JQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="' . $addr . '/jquery-2.1.4.min.js"></script> <!-- locally sourced -->
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="' . $addr . '/header.js"></script>
  <!-- modal patch -->
 <!-- <script src="' . $addr . '/bootstrap-modal-bs3patch.css"></script>
  <script src="' . $addr . '/bootstrap-modal.css"></script>
  <script src="' . $addr . '/bootstrap-modalmanager.js"></script>
  <script src="' . $addr . '/bootstrap-modal.js"></script> -->
  <script src="' . $addr . '/jstz.min.js"></script>
</head>
<body>
<div id="main_container" class="container-fluid">
<nav class="navbar navbar-inverse row"  id="navbar_main">
  <div class="container-fluid">
    <div class="navbar-header" id="head_nav">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar" id="mobile_navbutton">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="' . $addr . '/index.php" id="home_button">
        <!--<span class="glyphicon glyphicon-home" id="home_icon"></span>-->
    <img src="' . $addr . '/polylogo_grey-03.png" class="img-responsive" alt="Responsive image" id="logo">
      </a>
    </div>  <!-- end head_nav -->
   <!--  <div>  -->
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-left">
     <!--   <li class="active"><a href="#">Home</a></li> -->
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Schedules
            <span class="caret"></span></a>
          <ul class="dropdown-menu">
       <!--     <li><a href="./schedules/monophasic.html" class="sched_items">Monophasic</a></li>
            <li><a href=\"' . $addr . 'schedules/biphasic.html" class="sched_items">Biphasic</a></li>
            <li><a href=\"' . $addr . 'schedules/siesta.html" class="sched_items">Siesta</a></li>
            <li><a href=\"' . $addr . 'schedules/e3.html" class="sched_items">Everyman 3</a></li>
            <li><a href=\"' . $addr . 'schedules/dual_core.html" class="sched_items">Dual Core</a></li>
            <li><a href=\"' . $addr . 'schedules/uberman.html" class="sched_items">Uberman</a></li>
            <li><a href=\"' . $addr . 'schedules/dymaxion.html" class="sched_item">Dymaxion</a></li>  -->
            <li><a href="' . $addr . '/schedules/segmented.php" class="sched_items">Segmented</a></li>
          </ul>
        </li>
   <!--     <li><a href="#">Adaptation</a></li>  -->
        <li><a href="http://napchart.com">Napchart</a></li>
   <!--     <li><a href=\"#\">Research</a></li>
	<li><a href="#">Lucid Dreaming</a></li>
	<li><a href="#">News</a></li>
	<li><a href="#">Archives</a></li>  -->
        <li><a href="' . $addr .'/chat/chat.php">Chatroom</a></li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="' . $addr . '/forum/index.php">Forum
          <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="' . $addr .'/forum/index.php">Forum home</a></li>
            <li class="divider"></li>
            <li><a href="' . $addr .'/forum/index.php?action=help">Forum help</a></li>
            <li><a href="' . $addr . '/forum/index.php?action=search">Search forum</a></li>
            <li><a href="' . $addr . '/forum/index.php?action=mlist">View members</a></li>
            <li><a href="' . $addr .'/forum/index.php?action=mlist;sa=search">Search members</a></li>
          </ul>
        </li> <!-- end dropdown list -->';
	if ($context['user']['is_admin']) {
		echo '
	        <li class="dropdown">
          	  <a class="dropdown-toggle" data-toggle="dropdown" href="' . $addr . '/forum/index.php">Admin
                    <span class="caret"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="' . $addr .'/forum/index.php?action=admin;area=featuresettings">Feature and Options</a></li>
                        <li><a href="' . $addr . '/forum/index.php?action=admin;area=packages">Package Manager</a></li>
                        <li><a href="' . $addr . '/forum/index.php?action=admin;area=logs;sa=errorlog;desc">Error log</a></li>
                        <li><a href="' . $addr .'/forum/index.php?action=admin;area=permissions">Permissions</a></li>
                      </ul>
                </li> <!-- end dropdown list -->';
	}
	if ($context['user']['is_admin'] || $context['user']['is_mod']) {
		echo '
	        <li class="dropdown">
          	  <a class="dropdown-toggle" data-toggle="dropdown" href="' . $addr . '/forum/index.php">Moderate
                    <span class="caret"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="' . $addr .'/forum/index.php?action=moderate;area=reports">Reported posts</a></li>
                      </ul>
                </li> <!-- end dropdown list -->';
	}
echo '
	<li>
          
          <form class="search">
            <input type="text" id="search" size="20" placeholder="Search Polyphasic.xyz">
          </form>
          <span class="glyphicon glyphicon-search" id="navsearch"></span>
        </li>

    <li id="blank_tab"> </li>
  </ul> <!-- end navbar-left -->';
if (!$context['user']['is_guest']) {
	echo '
	  <ul class="nav navbar navbar-right navbar-justify">
          <li id="set_list" class="dropdown">

	    <a class="dropdown-toggle" data-toggle="dropdown" href="' . $addr . '/forum/index.php">
	      <span id="set_icon" class="glyphicon glyphicon-user headertxt"></span>
	      <span id="u_welcome"><strong>' . $context['user']['name'] . '</strong></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="' . $addr .'/forum/index.php?action=profile">Profile Summary</a></li>
              <li><a href="' . $addr . '/forum/index.php?action=profile;area=account">Account Settings</a></li>
              <li><a href="' . $addr . '/forum/index.php?action=profile;area=forumprofile">Forum Profile</a></li>
            </ul>
	  </li> <!-- end #set_list -->

        <li id="message_list" class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="' . $scripturl . '?action=pm">
         <span id="top_envelope" class="glyphicon glyphicon-envelope"></span><span id="message_counter" class="badge">' . $context['user']['unread_messages'] . '</span></a>
<ul class="dropdown-menu" role="menu">
  <li><a href="' . $addr . '/forum/index.php?action=unread">Unread posts</a></li>
  <li><a href="' . $addr . '/forum/index.php?action=unreadreplies">New replies</a></li>
</ul>
        </li> <!-- end message_list -->
  	  <li id="logout_cell">
	    <a id="logout_link" href="' . $scripturl . '?action=logout;' . $context['session_var'] . '=' . $context['session_id'] . '">' . $txt['logout'] . '</a>
	  </li>

        </ul>';
} else {
echo '

<ul class="nav navbar navbar-right navbar-justify">
<li id="signup_cell">
  <!-- Trigger the Signup  Modal with a button -->
  <button class="btn btn-default btn-sm" id="signupbtn">Sign Up</button>

  <!--Sign Up  Modal -->
  <div class="modal fade" id="signupModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:35px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4><span class="glyphicon glyphicon-plus"></span> Sign Up</h4>
        </div>
        <div class="modal-body" style="padding:40px 50px;">
         <!-- <form onsubmit = "return validateForm()" action ="./signup.php" method ="POST"> -->
            <div class="form-group">
              <label for="usrname"><span class="glyphicon glyphicon-user"></span> Username</label>
              <input type="text" class="form-control" name="uname" id="usname" placeholder="Enter username">
              <span id="uname_field_icon"></span>
            </div>
            <div class="form-group">
              <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Password</label>
              <input type="password" class="form-control" name="passwd" id="pswd" placeholder="Enter password">
              <span id="password_field_icon"></span>
            </div>
	    <div class="form-group">
              <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Confirm Password</label>
              <input type="password" class="form-control" name="repeat" id="repeat" placeholder="Enter password">
              <span id="repeat_field_icon"></span>
            </div>
            <div class="form-group" id="email_div">
              <label for="email"><span class="glyphicon glyphicon-envelope"></span> Email address</label>
              <input type="email" class="form-control" name="email" id="email" placeholder="Email address (optional)">
              <span id="email_field_icon"></span>
             </div>
              <button type="submit" class="btn btn-default btn-block" id="signup_button"><span class="glyphicon glyphicon-off"></span> Sign Up</button>
        <!--  </form>  -->
        </div>
      </div>
    </div>
 </div>
</li> <!-- end signup_cell -->
<li id="login_cell">
  <!-- Trigger the login modal with a button -->
  <button class="btn btn-default btn-sm" id="loginbtn">Login</button>
</li> <!-- end login_cell -->
</ul>';
}

echo '
    </div> <!-- end mynavbar collapse-->
    </div> <!-- end container fluid inside nav that container collapse and regular --> 
</nav> <!-- end #navbar_main -->
<div class="row" id="after_nav">	
  <div class="container-fluid">
<!-- log in modal -->
  <div class="modal fade" id="loginModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:35px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4><span class="glyphicon glyphicon-lock"></span>Log In</h4>
        </div>
        <div class="modal-body" style="padding:40px 50px;">
          <form action ="' . $scripturl . '?action=login2" method ="POST" accept-charset="', $context['character_set'], '">
            <div class="form-group">
              <label for="user"><span class="glyphicon glyphicon-user"></span>', $txt['username'], '</label>
              <input type="text" class="input_text" name="user" id="usrname" placeholder="Enter username" value="', $user_info['username'], '">
            </div>
            <div class="form-group">
              <label for="password"><span class="glyphicon glyphicon-eye-open"></span>', $txt['password'], '</label>
              <input type="password" class="input_password" name="passwrd" id="psw" placeholder="Enter password">
            </div>
              <input type="hidden" name="cookielength" value="-1"/>
              <input type="submit" value="', $txt['login'], '"class="btn btn-default btn-block" id="login_button"/>
          </form>
        </div> <!-- end modal body -->
      </div> <!-- end modal header -->
    </div> <!-- end modal content -->
 </div> 
<!-- end #loginModal -->

';
?>

