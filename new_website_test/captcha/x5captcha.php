<?php
include("../res/x5engine.php");
$nameList = array("2mp","z5n","rzg","m53","tpe","e7e","tfd","a4u","2el","xt2");
$charList = array("N","E","A","8","7","7","Z","L","U","V");
$cpt = new X5Captcha($nameList, $charList);
//Check Captcha
if ($_GET["action"] == "check")
	echo $cpt->check($_GET["code"], $_GET["ans"]);
//Show Captcha chars
else if ($_GET["action"] == "show")
	echo $cpt->show($_GET['code']);
// End of file x5captcha.php
