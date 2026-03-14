<?php
include("../res/x5engine.php");
$nameList = array("ugp","cws","72c","esm","lmr","mmj","3xw","fv8","srd","px2");
$charList = array("E","7","P","K","P","W","S","7","7","A");
$cpt = new X5Captcha($nameList, $charList);
//Check Captcha
if ($_GET["action"] == "check")
	echo $cpt->check($_GET["code"], $_GET["ans"]);
//Show Captcha chars
else if ($_GET["action"] == "show")
	echo $cpt->show($_GET['code']);
// End of file x5captcha.php
