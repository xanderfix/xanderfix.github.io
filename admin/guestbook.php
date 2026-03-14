<?php

include "includes.php";

Configuration::getControlPanel()->accessOrRedirect();

function showSummary($contentT, $gb, $data) {
    // Show the summary
	$rating = $gb->getRating();
	$ratingT = new Template("templates/comments/summary.php");
	$ratingT->vote = $rating["rating"];
	$ratingT->count = $rating["count"];
	$ratingT->hasRating = $data['rating'];
	$contentT->content .= $ratingT->render();
}

function sortCommentsByDate($a, $b) {
	if ($a["timestamp"] == $b["timestamp"]) return 0;
    return ($a["timestamp"] > $b["timestamp"]) ? -1 : 1;
}

function loadGb($gb, $data, $posturl) {
	$gb->setPostUrl($posturl);
	switch($data['sendmode'])
	{
		case "file":
			$gb->loadXML($data['folder']);
		break;
	}

	// Take care of the actions
	if (isset($_GET['disable'])) {
		$n = (int)$_GET['disable'];
		$c = $gb->comments->get($n);
		if (count($c) != 0) {
			$c['approved'] = "0";
			$gb->comments->edit($n, $c);
			$gb->save();
		}
	}

	if (isset($_GET['enable'])) {
		$n = (int)$_GET['enable'];
		$c = $gb->comments->get($n);
		if (count($c) != 0) {
			$c['approved'] = "1";
			$gb->comments->edit($n, $c);
			$gb->save();
		}
	}

	if (isset($_GET['delete'])) {
		$gb->comments->delete((int)$_GET['delete']);
		$gb->save();
	}

	if (isset($_GET['unabuse'])) {
		$n = (int)$_GET['unabuse'];
		$c = $gb->comments->get($n);
		if (count($c)) {
			$c['abuse'] = "0";
			$gb->comments->edit($n, $c);
			$gb->save();
		}
	}

	if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['delete']) || isset($_GET['unabuse'])) {
		echo "<script>window.top.location.href='" . $posturl . "';</script>\n";
		exit();
	}

	return $gb;
}

// Load the main template
$mainT = Configuration::getControlPanel()->getMainTemplate();

$mainT->pagetitle = l10n("admin_guestbook", "Comments and Ratings");
$mainT->stylesheets = array("css/comments.css");
$mainT->content = "";

$contentT = new Template("templates/common/box.php");
$contentT->cssClass = "guestbook comments";
$contentT->content = "";

$id = isset($_GET['id']) ? $_GET['id'] : "";

// If there's only one guestbook just show it and don't ask for more
if (!strlen($id) && count($imSettings['guestbooks']) < 2) {
	$keys = array_keys($imSettings['guestbooks']);
	$id = $imSettings['guestbooks'][$keys[0]]['id'];
}
// Otherwise show the selectors
else {
	$selectorsT = new Template("templates/guestbook/selectors.php");
	$selectorsT->guestbooks = $imSettings['guestbooks'];
	$selectorsT->id = $id;
	$contentT->content .= $selectorsT->render();
}

//load new template of comments
$commentsT = new Template("templates/guestbook/comments.php");
$commentsT->comments = [];
$commentsT->siteUrl = $imSettings['general']['url'];
$commentsT->baseposturl = 'guestbook.php?id=';
$totalComments = [];

$gb = false;
if (strlen($id)) {
	//show selected object
	$commentsT->showObjectTitle = false;
	$data = $imSettings['guestbooks'][$id];
	$gb = new ImTopic($id, "", "../");
	$posturl = $commentsT->baseposturl . $id;
	$gb = loadGb($gb, $data, $posturl);

	showSummary($contentT, $gb, $data);
	if ($gb->hasComments()) {
		$commentsT->comments = $gb->comments->comments;
		for ($i = 0, $size = count($gb->comments->comments); $i < $size; $i++) {
			$gb->comments->comments[$i]["gbid"] = $id;
			$gb->comments->comments[$i]["generalrating"] = $data['rating'];
		}
		$totalComments = array_merge($totalComments, $gb->comments->comments);

		if(count($totalComments)) {
			usort($totalComments, "sortCommentsByDate");
			$commentsT->comments = $totalComments;
			$contentT->content .= $commentsT->render();
		}
	}
}
else {
	//show all objects
	$commentsT->showObjectTitle = true;
	foreach ($imSettings['guestbooks'] as $gbid => $data) {
		$gb = new ImTopic($gbid, "", "../");
		$posturl = $commentsT->baseposturl . $gbid;
		$gb = loadGb($gb, $data, $posturl);

		if ($gb->hasComments()) {
			for ($i = 0, $size = count($gb->comments->comments); $i < $size; $i++) {
				$gb->comments->comments[$i]["gbid"] = $gbid;
				$gb->comments->comments[$i]["generalrating"] = $data['rating'];
				$gb->comments->comments[$i]["objectnumber"] = $data['objectnumber'];
				$gb->comments->comments[$i]["pagetitle"] = $data['pagetitle'];
			}
			$totalComments = array_merge($totalComments, $gb->comments->comments);
		}
	}
	if(count($totalComments)) {
		usort($totalComments, "sortCommentsByDate");
		$commentsT->comments = $totalComments;
		$contentT->content .= $commentsT->render();
	}
	else {
		$ratingT = new Template("templates/comments/summary.php");
		$ratingT->vote = 0;
		$ratingT->count = 0;
		$ratingT->hasRating = false;
		$contentT->content .= $ratingT->render();
	}
}

$mainT->content = $contentT->render();
echo $mainT->render();
