<?php
if(substr(basename($_SERVER['PHP_SELF']), 0, 11) == "imEmailForm") {
	include '../res/x5engine.php';
	$form = new ImForm();
	$form->setField('Your Name', @$_POST['imObjectForm_20_1'], '', false);
	$form->setField('Your E-mail', @$_POST['imObjectForm_20_2'], '', false);
	$form->setField('Message', @$_POST['imObjectForm_20_3'], '', false);

	$errorMessage = '';
	if(@$_POST['action'] != 'check_answer') {
		if(!isset($_POST['imJsCheck']) || $_POST['imJsCheck'] != 'EDE0630BC4D0E0F92D8E8EAF278B5813' || (isset($_POST['imSpProt']) && $_POST['imSpProt'] != ""))
			$errorMessage = "You must activate JavaScript!";
		$form->mailToOwner('', '', 'zeldarocksllc@gmail.com', 'New message from website', "New data received from my website:", false);
		$form->mailToCustomer('', '', $_POST['imObjectForm_20_2'], 'Thank you for contacting us!', "Hello! \nThank you for contacting us. \n\nWe will get in touch with you as soon as possible. \n\n-Zeldarocks", false);
		if ($errorMessage == '') {
			echo "{\"status\" : true}";
		}

		else {
			echo "{\"status\" : false, \"err\" : \"$errorMessage\"}";
		}
		exit();
	} else {
		echo $form->checkAnswer(@$_POST['id'], @$_POST['answer']) ? 1 : 0;
	}
}

// End of file