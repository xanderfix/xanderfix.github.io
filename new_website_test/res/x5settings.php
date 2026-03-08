<?php

/*
|-------------------------------
|	GENERAL SETTINGS
|-------------------------------
*/

$imSettings['general'] = array(
	'site_id' => 'A09D4C6CECDB84B21C39282B7B300242',
	'url' => 'http://localhost/',
	'homepage_url' => 'http://localhost/index.html',
	'icon' => '',
	'version' => '2025.3.6.0',
	'sitename' => 'New Project',
	'lang_code' => 'en-GB',
	'rtl' => false,
	'public_folder' => '',
	'salt' => '08la9t9j2rd5jssoqs048yr8c3g77cmi9l4p6hnu8nw8o5',
	'common_email_sender_addres' => '',
	'enable_sender_header' => true,
	'date_format' => 'dd MMM yyyy',
	'date_format_ext' => 'dddd dd MMM yyyy',
	'date_format_no_day' => 'MMM yyyy',
	'date_format_no_day_ext' => 'MMM yyyy'
);
/*
|-------------------------------
|	BREAKPOINTS
|-------------------------------
*/

$imSettings['breakpoints'] = array(
	array("name" => "Desktop", "hash" => "ea2f0ee4d5cbb25e1ee6c7c4378fee7b", "start" => "max", "end" => 1150.0, "fluid" => false),
	array("name" => "Breakpoint 1", "hash" => "d2f9bff7f63c0d6b7c7d55510409c19b", "start" => 1149.9, "end" => 720.0, "fluid" => false),
	array("name" => "Mobile", "hash" => "72e5146e7d399bc2f8a12127e43469f1", "start" => 719.9, "end" => 480.0, "fluid" => false),
	array("name" => "Mobile Fluid", "hash" => "5ecdcca63de80fd3d4fbb36295d22b7d", "start" => 479.9, "end" => 0.0, "fluid" => true),
);
/*
|-------------------------------
|	PASSWORD POLICY
|-------------------------------
*/

$imSettings['password_policy'] = array(
	'required_policy' => false,
	'minimum_characters' => '6',
	'include_uppercase' => false,
	'include_numeric' => false,
	'include_special' => false
);
/*
|-------------------------------
|	Captcha
|-------------------------------
*/ImTopic::$captcha_code = "		<div class=\"x5captcha-wrap\">
			<label for=\"gzx04uwr-imCpt\">Check word:</label><br />
			<input type=\"text\" id=\"gzx04uwr-imCpt\" class=\"imCpt\" name=\"imCpt\" maxlength=\"5\" />
		</div>
";


$imSettings['admin'] = array(
	'icon' => 'admin/images/logo_cgi1z4gv.png',
	'theme' => 'orange',
	'extra-dashboard' => array(),
	'extra-links' => array()
);


/*
|--------------------------------------------------------------------------------------
|	DATABASES SETTINGS
|--------------------------------------------------------------------------------------
*/

$imSettings['databases'] = array();

/*
|-------------------------------------------------------------------------------------------
|	GUESTBOOK SETTINGS
|-------------------------------------------------------------------------------------------
*/

$imSettings['guestbooks'] = array();


/*
|-------------------------------
|	EMAIL SETTINGS
|-------------------------------
*/

$ImMailer->emailType = 'phpmailer';
$ImMailer->header = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n" . '<html>' . "\n" . '<head>' . "\n" . '<meta http-equiv="content-type" content="text/html; charset=utf-8">' . "\n" . '<meta name="generator" content="Incomedia WebSite X5 Evolution 2025.3.6 - www.websitex5.com">' . "\n" . '</head>' . "\n" . '<body bgcolor="#2A363B" style="background-color: #2A363B;">' . "\n\t" . '<table border="0" cellpadding="0" align="center" cellspacing="0" style="padding: 0; margin: 0 auto; width: 700px; border-collapse: separate;">' . "\n\t" . '<tr><td id="imEmailContent" style="min-height: 300px; font: normal normal normal 9pt \'Raleway\'; color: #404040; background-color: #FFFFFF; text-decoration: none; text-align: left; width: 700px; border-style: solid; border-color: transparent transparent transparent transparent; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 0; border-bottom: none; border-left-width: 1px;  padding-top: 25px;  padding-bottom: 25px; padding-left: 25px; padding-right: 25px;  background-color: #FFFFFF" width="700px">' . "\n\t\t";
$ImMailer->footer = "\n\t" . '</td></tr>' . "\n\t" . '<tr><td id="imEmailIcons" style="background-color: #FFFFFF;border-left: 1px solid transparent; border-right: 1px solid transparent; border-bottom-style: solid; border-bottom-color: transparent; border-bottom-width: 1px; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;  padding-top: 25px;  padding-bottom: 25px; padding-left: 15px; padding-right: 15px;  text-align: center;  min-height: 300px; " width="700"></td></tr>' . "\n\t" . '</table>' . "\n" . '<table width="100%"><tr><td id="imEmailFooter" style="font: normal normal normal 7pt \'Raleway\'; color: #60BF00; background-color: transparent; text-decoration: none; text-align: center;  margin-top: 5px; padding-top: 25px;  padding-bottom: 25px; padding-left: 25px; padding-right: 25px; background-color: transparent">' . "\n\t\t" . 'This e-mail contains information that is intended solely for the above mentioned addressee.<br>If you have received this e-mail by error, please notify the sender immediately and destroy it, without copying it.' . "\n\t" . '</td></tr></table>' . "\n\t" . '</body>' . "\n" . '</html>';
$ImMailer->bodyBackground = '#FFFFFF';
$ImMailer->bodyBackgroundEven = '#FFFFFF';
$ImMailer->bodyBackgroundOdd = '#F0F0F0';
$ImMailer->bodyBackgroundBorder = '#CDCDCD';
$ImMailer->bodyTextColorOdd = '#404040';
$ImMailer->bodySeparatorBorderColor = '#404040';
$ImMailer->emailBackground = '#2A363B';
$ImMailer->emailContentStyle = 'font: normal normal normal 9pt \'Raleway\'; color: #404040; background-color: #FFFFFF; text-decoration: none; text-align: left; ';
$ImMailer->emailContentFontFamily = 'font-family: Raleway;';

// End of file x5settings.php