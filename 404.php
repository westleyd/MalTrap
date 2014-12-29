<html>
<head>
<title>404 - Page not found</title>
</head>
<body>
<h1>Error 404: File not found</h1>
Unfortunately, the file you're looking for was not found on our server.<br>
<? echo $_SERVER[REQUEST_URI] ?><br>
<br>
<?
unset($_SESSION[trap]);
$error = $_SERVER['REDIRECT_STATUS'];
$referring_url = $_SERVER['HTTP_REFERER'];
$requested_url = $_SERVER['REQUEST_URI'];
$referring_ip = $_SERVER['REMOTE_ADDR'];
$server_name = $_SERVER['SERVER_NAME'];

$emailto = 'westleyd@gmail.com';
$site_nick = 'AncillaTech-TechBlog';

$subj404 = "$site_nick: 404 error";

///// Trap people digging around for exploits.
include './btr/btr-mask.php';

if (preg_match($mask,$requested_url)) {
	$_SESSION[trap][] = 'probe_scriptseeking';
	$msgadd= "This matched on script-seeking.";	
	$_SESSION[bot] = 'triggered';
}
if (strpos($referring_url,$requested_url)) {
	//if the requested page (that doesn't exist) has itself as the referrer... it's a trap!
	$_SESSION[trap][] = '404_self-referrer';
	$msgadd= "This matched on self-referrer.";
	$_SESSION[bot] = 'triggered';
}

$datetime = date("l, F dS, Y - H:i:s");
$msg404  = "<big><b>Error 404 - Page Not Found<br><i>$datetime</i></b></big>\n";
$msg404 .= '<br><i>Requested URL:</i> <a href="http://'.$server_name.$requested_url.'">'.$server_name.$requested_url.'</a>';
if ($referring_url) {
	$msg404 .= '<br><i>Referring URL:</i> <a href="'.$referring_url.'">'.$referring_url.'</a>';
} else {
	$msg404 .= '<br><i>Referring URL:</i> NONE';
}
$msg404 .= '<br><br><i>Visitor\'s IP Address:</i> '.$referring_ip;
$msg404 .= '<br><br>';

if ($_SESSION[trap]) {
	$msg404 .= "<big style=\"color:#dd2222;\">This was flagged for reporting.</big><br><br>";
	//include "/mt/index.php";
	include "../btr/index.php";
} else {
if ($msgadd) { $msg404 .= "<big style=\"color:#dd2222;\">$msgadd</big><br><br>";}
$msg404 .= '<a href="http://ancillatech.net/whois/">ancillatech.net/whois/</a>';

$headers = 'From: server@ancillatech.net' . "\r\n" .
   'Reply-To: server@ancillatech.net' . "\r\n" .
   "Content-type: text/html\r\n" .
   'X-Mailer: PHP/' . phpversion();
mail($emailto, $subj404, $msg404, $headers);
}
?>
The problem has been reported and we'll be working to correct it soon.<br>
We recommend starting at our <a href="/">home page</a>.

</body>
</html>