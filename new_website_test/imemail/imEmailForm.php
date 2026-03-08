<?php
if(substr(basename($_SERVER['PHP_SELF']), 0, 11) == "imEmailForm") {
	include '../res/x5engine.php';
	$form = new ImForm();

	$errorMessage = '';
	if(@$_POST['action'] != 'check_answer') {
	$form->setField('Your Name', @$_POST['imObjectForm_20_1'], '', false);
	$form->setField('Your E-mail', @$_POST['imObjectForm_20_2'], '', false);
	$form->setField('Message', @$_POST['imObjectForm_20_3'], '', false);
		if(!isset($_POST['imJsCheck']) || $_POST['imJsCheck'] != '8FEFF3E350FF8888E6A0678607283D55' || (isset($_POST['imSpProt']) && $_POST['imSpProt'] != ""))
			die(imPrintJsError());
		$form->mailToOwner('', '', 'example@example.com', 'New contact', "New data received from my website:", false);
		@header('Location: ../index.html');
		exit();
	} else {
		echo $form->checkAnswer(@$_POST['id'], @$_POST['answer']) ? 1 : 0;
	}
}

// End of file