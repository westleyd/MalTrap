<?
////// CONFIGURATION VARS /////
//sysadmin's notification email
$sysadmin_email = "wes@ancillatech.net, westleyd@gmail.com";
//Site or company's nickname (to start email subject)
if ($siteNick == '') {$siteNick = $site_nick;}
if ($siteNick == '') {$siteNick = "AncillaTech";}
//Contact info (html allowed), added into auto-reported abuse emails.
// Including a phone number is recomended to show legitimacy, 
// though there's no reason to expect a call from Korea. They'll email... in Korean :).
$sysadmin_contact  = "<b>Westley Dent, Owner, Ancilla Technologies.</b><br>\r\n";
$sysadmin_contact .= "Kansas City, MO 64111, USA.<br>\r\n";
$sysadmin_contact .= "+1.816.237.8329 &nbsp; &nbsp; http://ancillatech.net/ ";

//Reset vars used in this script.
//$d=""; //interferes with later code.
$abuse_in_whois = '';
$googlebot = '';
//string of server's Document Root to be replaced with '***' in messages to others.
$docroot = $_SERVER['DOCUMENT_ROOT'];	


function dviewArray($arr)
{
   foreach ($arr as $key1 => $elem1) {
       $d .= $key1;
       if (is_array($elem1)) { $d .= dextArray($elem1); }
       else { $d .= '='.$elem1."<br>\r\n"; }
   }
   return $d;
}
function dextArray($arr)
{
   $d .= '&gt;&gt;';
   foreach ($arr as $key => $elem) {
       $d .= $key;
       if (is_array($elem)) { dextArray($elem); }
       else { "<br>\r\n&nbsp; &nbsp; ". $d .= '= '.htmlspecialchars($elem); }
   }
   $d .= "<br>\r\n&lt;&lt;<br>\r\n";
   return $d;
}

if (!$_SESSION[bot]) {
?><!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>BTR Data Collector</title>
</head>
<body>

<?php
}
if ($_REQUEST[d]) {$domain=$_REQUEST[d];} else {$domain = trim($_ENV[REMOTE_ADDR]);}
$whois="whois.arin.net";
$port=43;

$rdns = gethostbyaddr ($domain);
$d="<br>\n";
$d .= "$domain";
$d .= " -- rDNS: $rdns<br>\n";
$d .= "UnixTime:". time() . " = " . date("d-M-Y H:i:s O T") . "<br>\n";
$d .= "\n---------------<br>\n";
$d .= "WHOIS:<br>\n";

if (strpos($rdns,'googlebot.com') > -1) {		// If a trap was triggered on GoogleBot, we'll skip whois.
	$abuse_in_whois .= "abuse@google.com";
	$knownbot = 1;
} else {

$fp = fsockopen($whois, $port, $errno, $errstr, 30);
if (!$fp) {
  $d .= "$errstr ($errno)";
} else {
  fputs($fp, "n + $domain\r\n");
  $dw = "<pre>\r\n";
  while (!feof($fp)) {
    $dw .= fread($fp,128);
  }
  $dw .= "</pre>";
  fclose ($fp);
  $nw = "";
  if (strpos($dw,'whois.ripe.net') != FALSE) {$nw = "whois.ripe.net"; $nic="RIPE"; $domain="-B $domain";}
  if (strpos($dw,'whois.apnic.net') != FALSE) {$nw = "whois.apnic.net"; $nic="APNIC";}
  if (strpos($dw,'whois.lacnic.net') != FALSE) {$nw = "whois.lacnic.net"; $nic="LACNIC";}
  if (strpos($dw,'whois.afrinic.net') != FALSE) {$nw = "whois.afrinic.net"; $nic="AfriNIC";}
  if (!$nw) {$nic="ARIN";}

///// Some common hits that don't have abuse@ addresses in whois /////
  if (strpos($dw,'Comcast Cable Communications') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@comcast.net";}
  if (strpos($dw,'Charter Communications') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@charter.net";}

  if (strpos($dw,'AT&T Internet Services') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@att.net";}
  if (strpos($dw,'.pacbell.net') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@pacbell.net";}
  if (strpos($dw,'.sbcglobal.') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@sbcglobal.net";}

  if (strpos($dw,'Mediacom communications Corp') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@mchsi.com";}

  if (strpos($dw,'Cox Communications Inc.') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@cox.net";}

	
///// END COMMON ABUSE ADDRESSES /////	

  
  if ($nw) {
  	$dw = "---------------";	//Restarting the message, dropping ARIN results.
  	$fp = fsockopen($nw, $port, $errno, $errstr, 30);
	if (!$fp) {
	  $d .= "$errstr ($errno)";
	} else {
	  fputs($fp, "$domain\r\n");
	  $dw .= "<pre>\r\n";
	  while (!feof($fp)) {
	    $dw .= fread($fp,128);
	  }
	  $dw .= "</pre>";
	  fclose ($fp);
  	}
  }
}


// look for an 'abuse@' address in whois
preg_match_all('/([a-z0-9&\.\-_\+])*abuse@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2,4}/i', $dw, $matches);
//<net>abuse@().[a-z]{2,4}
if (count($matches) >0 ) {
	foreach ($matches[0] as $elem1) {
		if (!strstr($abuse_in_whois,$elem1) && ($elem1 != 'abuse@iana.org') && (strpos($elem1,'noc') == FALSE)) {
			if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
			$abuse_in_whois .= $elem1;
		}
	}
}


///// Some common FOREIGN hits that don't have abuse@ addresses in whois /////
if (strpos($dw,'China Unicom') != FALSE || strpos($dw,'@chinaunicom') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@chinaunicom.cn";}
else if (strpos($dw,'VPNTunnel IP Range') != FALSE || strpos($dw,'@netalia.se') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@vpntunnel.se";}
else if (strpos($dw,'BELTELECOM') != FALSE || strpos($dw,'@belpak.by') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@belpak.by, abuse@mgts.by";}
else if (strpos($dw,'@kyivstar.net') != FALSE) {
  	if (strlen($abuse_in_whois) > 0) {$abuse_in_whois .= ", ";}
  	$abuse_in_whois .= "abuse@kyivstar.net";}


	


$d .= $dw;

}		// End GoogleBot skip of WHOIS
$d .= "<br>\r\n---------------<br>\r\n";
$d .= "<b>\$_SERVER=</b><br>\r\n";
$d .= dviewArray($_SERVER);
$d .= "<br>\r\n---------------<br>\r\n";
$d .= "<b>\$_REQUEST=</b><br>\r\n";
if ($_REQUEST) {
	$d .= dviewArray($_REQUEST);
}
$d .= "<br>\r\n---------------<br>\r\n";
$d .= "<b>\$_SESSION=</b><br>\r\n";
if ($_SESSION) {
	$d .= dviewArray($_SESSION);
}
$d = str_replace($docroot,"***",$d);


	$message = "";
	
	if ($abuse_in_whois) {
		$to = $abuse_in_whois;
		//$to = $sysadmin_email;		//For Testing only. Sysadmin is Cc'd when abuse adresses have been scraped.
		
		$message .= "This report is being automatically forwarded to you ";	//<br>\r\n";
		$message .= "on behalf of this hosting space's sysadmin,<br>\r\n";
if ($knownbot !=1) {
//		$message .= "by Ancilla Technology's MalTrap script,";
		$message .= "thanks to the abuse contact in your whois information ";
//		$message .= "with ARIN, LACNIC, RIPE, AfriNIC, or APNIC.<br>\r\n";
		$message .= "with {$nic}.<br>\r\n";
		$message .= "scraped abuse addresses = '$abuse_in_whois'<br>\r\n";		//Debugging
}
		$message .= "<br>\r\n";
		$message .= "$sysadmin_contact<br>\r\n<br>\r\n";
		
	} else {	$to = $sysadmin_email;	}
	$subject = "$siteNick: MalTrap Abuse Log";
	if ($abuse_in_whois) {$subject .= " - AutoReport";}
	
	if (!$_SESSION[bot]) {
		$message .= "The following IP was caught following a link only mentioned in robots.txt<br>\r\n";
	} elseif (count($_SESSION[trap]) > 0) {
		// updated 404 trapping		
		$message .= "The following IP was caught performing suspicious behavior.<br>\r\n";
		foreach ($_SESSION[trap] as $t) {
			$message .= " -- $t<br>\r\n";
		}
		$message .= "<br>\r\n";
	} elseif ($_SESSION[bot] == 4) {
 		$message .= "The following IP was caught trying to abuse our feedback form to send emails to other people.<br>\r\n";
	} elseif ($_SESSION[bot] == 3) {
 		$message .= "The following IP was caught trying to create a new user account having the same first and last name.<br>\r\n";
	} elseif ($_SESSION[bot] == 2) {
 		$message .= "The following IP was caught submitting a form using an email address for a name.<br>\r\n";
	} elseif ($_SESSION[bot] == 1) {
		$message .= "The following IP was caught entering the account creation page with no session (having not been to any other pages).<br>\r\n";
	} else {
		$message .= "The following IP was caught acting in an unspecified suspicious pattern.<br>\r\n";
	}
	
	if ($abuse_in_whois) {
	}
	
	$message .= $d;
	$headers = 'From: server@ancillatech.net' . "\r\n" .
	   "Reply-To: $sysadmin_email\r\n" .
	   'X-Mailer: PHP/' . phpversion();
	   if ($abuse_in_whois) {$headers .= "\r\nCc: $sysadmin_email\r\n";}
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";


	$message = str_replace("\n.", "\n..", $message);
	$mail_sent = mail($to, $subject, $message, $headers);
	

	//echo "<textarea cols=90 rows=20>$to\n$subject\n\n$message\n\n$headers\n</textarea><br>\n";
	//echo "Email is not being sent while in debug mode.<br>\n<br>\n";
	//$mail_sent = 0;

	if ($mail_sent) {
		if ($_SESSION[bot]) {echo "<b><font color='green'>Because we believe you to be a bot or other illegitimate user<br>Your information has been logged and forwarded to our sysadmin.<br>The log may have even been automatically reported to your ISP!<br><br>Thanks for playing!</font></b><br><br>";}
		else {echo "<b><font color='green'>Because you followed a link explicitly disallowed in robots.txt<br>Your information has been logged and forwarded to our sysadmin.<br>The log may have even been automatically reported to your ISP!<br><br>Thanks for playing!</font></b><br><br>";}
	} else {
		// $save to file?
		//echo "<textarea cols=60 rows=6>$message</textarea>";
	}

if (!$_SESSION[bot]) {
?>
</body>
</html>
<?php
}