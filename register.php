<?php

  if ($_POST['action'] == "Register" && $_POST['homepage']) {
	  header("Location: http://acmlm.no-ip.org/board/register.php");
	  die();
  }


  require 'lib/function.php';
  require 'lib/layout.php';
  $ipstart=substr($userip,0,6);
  print $header;

	if ($adminconfig['registrationdisable'])
		die("$tblstart<br>$tccell2>Registration is disabled. Please contact an admin if you have any questions.$tblend$footer");
  

  if (!$_POST[action]){
    $descbr="</b>$smallfont<br></center>&nbsp";
    print "
	<body onload=window.document.REPLIER.username.focus()>
	<form ACTION=register.php NAME=REPLIER METHOD=POST>
	<br>$tblstart
	
	$tccellh colspan=2>Login information</td><tr>
	$tccell1><b>User name:</b>$descbr The name you want to use on the board.</td>
	$tccell2l width=50%>$inpt=name SIZE=25 MAXLENGTH=25><tr>
	$tccell1><b>Password:</b>$descbr Enter any password up to 32 characters in length. It can later be changed by editing your profile.<br><br>Warning: Do <b>not</b> use unsecure passwords such as '123456', 'qwerty', or 'pokemon'. It'll result in an instant IP ban.</td>
	$tccell2l width=50%>$inpp=pass SIZE=13 MAXLENGTH=32><tr>
	$tccellh>&nbsp</td>$tccellh>&nbsp<tr>
	$tccell1>&nbsp</td>$tccell2l>
	$inph=action VALUE=\"Register\">
	$inps=submit VALUE=\"Register account\"></td>
	</table>
		<div style='visibility: hidden;'><b>Homepage:</b><small> DO NOT FILL IN THIS FIELD. DOING SO WILL RESULT IN INSTANT IP-BAN.</small> - $inpt=homepage SIZE=25 MAXLENGTH=255></div>

		</form>

    ";
  }
  if($_POST[action]=='Register') {

	if ($_POST['name'] == "Blaster") {
		$sql -> query("INSERT INTO `ipbans` SET `ip` = '". $_SERVER['REMOTE_ADDR'] ."', `date` = '". ctime() ."', `reason` = 'Idiot'");
		@xk_ircsend("1|". xk(7) ."Auto-IP banned Blaster with IP ". xk(8) . $_SERVER['REMOTE_ADDR'] . xk(7) ." on registration.");
		die("$tccell1>Thank you, $username, for registering your account.<br>".redirect('index.php','the board',0).$footer);
	}

	$users = mysql_query('SELECT name FROM users');
	$username = substr(trim($name),0,25);
	$username2 = str_replace(' ','',$username);
	$username2 = str_replace(' ','',$username2);
	$username2 = preg_replace("'&nbsp;'si",'&nbsp',$username2);
	$username2 = preg_replace("'&nbsp'si",'',$username2);
	$username2 = stripslashes($username2);
    print $tblstart;
    $userid=-1;
    while ($user=mysql_fetch_array($users)) {
		$user[name]=str_replace(' ','',$user[name]);
		$user[name]=str_replace(' ','',$user[name]);
		if (strcasecmp($user[name],$username2)==0) $userid=$u;
	  }
	$nomultis = mysql_query("SELECT * FROM `users` WHERE `lastip` = '$REMOTE_ADDR'");
	$nomultis = mysql_fetch_array($nomultis);
//	$nomultis	= false;

	if ($userid==-1 and $pass and $pass != "123" and $name && ( !$nomultis || $isadmin )) {
	if(!mysql_num_rows($users)) $userlevel=3;
	$currenttime=ctime();
	$ipaddr=getenv("REMOTE_ADDR");
	if (!$x_hacks['host'] && false) {
		$dagagdsf = mysql_query("INSERT INTO `pendingusers` SET `username` = '$name', `password` = '". $pass ."', `ip` = '$ipaddr', `time` = '$currenttime'") or print mysql_error();

//		mysql_query("INSERT INTO `ipbans` SET `ip` = '$ipaddr', `reason` = 'Automagic ban', `banner` = 'Acmlmboard'");

		print "$tccell1>Thank you, $username, for registering your account.<br>".redirect('index.php','the board',0);
	} else {

		$ircout['name']		= stripslashes($name);
		$ircout['ip']		= $ipaddr;
		$ircout['pmatch']	= $sql -> resultq("SELECT COUNT(*) FROM `users` WHERE `password` = '". md5($pass) ."'");

		$dagagdsf = mysql_query("INSERT INTO `users` SET `name` = '$name', `password` = '". md5($pass) ."', `powerlevel` = '0', `postsperpage` = '20', `threadsperpage` = '50', `lastip` = '$ipaddr', `layout` = '1', `scheme` = '0', `lastactivity` = '$currenttime', `regdate` = '$currenttime'") or print mysql_error();

		$newuserid			= mysql_insert_id();
		$ircout['id']		= $newuserid;
		xk_ircout("user", $ircout['name'], $ircout);


		mysql_query("INSERT INTO `users_rpg` (`uid`) VALUES ('". $newuserid ."')") or print mysql_error();
		print "$tccell1>Thank you, $username, for registering your account.<br>".redirect('index.php','the board',0);
	}
    }else{
	
/*	if ($password == "123") {
		echo	"$tccell1>Thank you, $username, for registering your account.<img src=cookieban.php width=1 height=1><br>".redirect('index.php','the board',0);
		mysql_query("INSERT INTO `ipbans` (`ip`, `reason`, `date`) VALUES ('". $_SERVER['REMOTE_ADDR'] ."', 'blocked password of 123', '". ctime() ."')");
		die();
	}
*/

	if ($userid != -1) {
		$reason = "That username is already in use.";
	} elseif ($nomultis) {
		$who = mysql_fetch_array(mysql_query("SELECT id, lastip FROM `users` WHERE `lastip` = '$REMOTE_ADDR'"));
		$reason = "You have already registered! (<a href=profile.php?id=$who[id]>here</a>)";
	} elseif (!$username || !$password) {
		$reason = "You haven't entered a username or password.";
	} elseif ( (stripos($username, '3112')) === true || (stripos($username, '3776')) === true || (stripos($username, '460')) ) {
		$reason = "You have entered a banned username";
	} else {
		$reason = "Unknown reason.";
	}
	
	print "
	 $tccell1>Couldn't register the account. $reason
	 <br>".redirect("index.php","the board",0);
    }
    print $tblend;
  }
  print $footer;
  printtimedif($startingtime);
?>
