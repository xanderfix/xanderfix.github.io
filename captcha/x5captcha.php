<?php
include("../res/x5engine.php");
$nameList = array("g36","myl","8z6","3re","gup","uhl","tcu","36a","6zv","g6f");
$charList = array("4","G","A","P","L","X","G","G","Z","T");
$cpt = new X5Captcha($nameList, $charList);
//Check Captcha
if ($_GET["action"] == "check")
	echo $cpt->check($_GET["code"], $_GET["ans"]);
//Show Captcha chars
else if ($_GET["action"] == "show")
	echo $cpt->show($_GET['code']);
// End of file x5captcha.php
