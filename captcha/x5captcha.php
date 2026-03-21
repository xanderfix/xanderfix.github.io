<?php
include("../res/x5engine.php");
$nameList = array("2gk","yxl","zse","ufy","pea","gpl","jtd","esr","j3r","55d");
$charList = array("7","P","D","A","7","D","4","5","5","N");
$cpt = new X5Captcha($nameList, $charList);
//Check Captcha
if ($_GET["action"] == "check")
	echo $cpt->check($_GET["code"], $_GET["ans"]);
//Show Captcha chars
else if ($_GET["action"] == "show")
	echo $cpt->show($_GET['code']);
// End of file x5captcha.php
