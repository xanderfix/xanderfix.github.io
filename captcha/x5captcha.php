<?php
include("../res/x5engine.php");
$nameList = array("n45","pja","tdn","vd7","v7z","74a","zyx","4ec","axn","u3r");
$charList = array("F","J","R","6","F","2","L","H","N","F");
$cpt = new X5Captcha($nameList, $charList);
//Check Captcha
if ($_GET["action"] == "check")
	echo $cpt->check($_GET["code"], $_GET["ans"]);
//Show Captcha chars
else if ($_GET["action"] == "show")
	echo $cpt->show($_GET['code']);
// End of file x5captcha.php
