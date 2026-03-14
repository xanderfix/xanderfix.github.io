<?php

include "includes.php";

Configuration::getControlPanel()->accessOrRedirect();

function showSummary($contentT, $topic, $data) {
    // Show the summary
	$rating = $topic->getRating();
	$ratingT = new Template("templates/comments/summary.php");
	$ratingT->vote = $rating["rating"];
	$ratingT->count = $rating["count"];
	$ratingT->hasRating = $data['comment_type'] != "comment";
	$contentT->content .= $ratingT->render();
}

function loadTopic($topic, $data, $posturl) {
    $topic->setPostUrl($posturl);
	switch($data['sendmode']) {
		case "file":
			$topic->loadXML($data['folder']);
		break;
	}

	// Take care of the actions
	if (isset($_GET['disable'])) {
        $n = (int)$_GET['disable'];
        $c = $topic->comments->get($n);
        if (count($c) != 0) {
            $c['approved'] = "0";
            $topic->comments->edit($n, $c);
            $topic->save();
        }
    }

    if (isset($_GET['enable'])) {
        $n = (int)$_GET['enable'];
        $c = $topic->comments->get($n);
        if (count($c) != 0) {
            $c['approved'] = "1";
            $topic->comments->edit($n, $c);
            $topic->save();
        }
    }

    if (isset($_GET['delete'])) {
        $topic->comments->delete((int)$_GET['delete']);
        $topic->save();
    }

    if (isset($_GET['unabuse'])) {
        $n = (int)$_GET['unabuse'];
        $c = $topic->comments->get($n);
        if (count($c)) {
            $c['abuse'] = "0";
            $topic->comments->edit($n, $c);
            $topic->save();
        }
    }

    if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['delete']) || isset($_GET['unabuse'])) {
        echo "<script>window.location.href='" . $posturl . "';</script>\n";
        exit();
    }
    return $topic;
}

function sortCommentsByDate($a, $b) {
	if ($a["timestamp"] == $b["timestamp"]) return 0;
    return ($a["timestamp"] > $b["timestamp"]) ? -1 : 1;
}


// Load the main template
$mainT = Configuration::getControlPanel()->getMainTemplate();
$mainT->pagetitle = l10n("blog_title", "Blog");
$mainT->stylesheets = array("css/comments.css");
$mainT->content = "";
$contentT = new Template("templates/common/box.php");
$contentT->cssClass = "blog comments";
$contentT->content = "";

// Show the category and post selectors
$selectorsT = new Template("templates/blog/selectors.php");
$selectorsT->categories = $imSettings['blog']['posts_cat'];
$selectorsT->selectedCategory = @$_GET['category'];
$selectorsT->posts = $imSettings['blog']['posts'];
if (isset($_GET['category'])) {
    foreach ($imSettings['blog']['posts_cat'] as $category => $posts) {
        if (str_replace(' ', '_', $category) === $_GET['category']) {
            $selectorsT->categoryPosts = $posts;
        }
    }
	$selectorsT->selectedPost = @$_GET['post'];
}
$contentT->content .= $selectorsT->render();

$category = isset($_GET['category']) ? $_GET['category'] : null;
$post = isset($_GET['post']) ? $_GET['post'] : null;
$data = $imSettings['blog'];

//load new template of comments
$commentsT = new Template("templates/blog/comments.php");
$commentsT->comments = [];
$commentsT->siteUrl = $imSettings['general']['url'];
$commentsT->baseposturlcategory = 'blog.php?category=';
$commentsT->baseposturlpost = '&post=';
$commentsT->showObjectTitle = false;
$commentsT->rating = $data['comment_type'] != "comment";
$totalComments = [];

$topic = false;

if ($category !== null && $post !== null) {
    $category = urlencode($category);
	$topic = new ImTopic($data['file_prefix'] . 'pc' . $post, "", "../");
	$posturl = $commentsT->baseposturlcategory . $category . $commentsT->baseposturlpost . $post;
    $topic = loadTopic($topic, $data, $posturl);

	showSummary($contentT, $topic, $data);
	if ($topic->hasComments()) {
		// Show the comments
		$commentsT->comments = $topic->comments->comments;
        for ($i = 0, $size = count($topic->comments->comments); $i < $size; $i++) {
			$topic->comments->comments[$i]["category"] = $category;
			$topic->comments->comments[$i]["post"] = $post;
		}
		$totalComments = array_merge($totalComments, $topic->comments->comments);
        
        if (count($totalComments)) {
            usort($totalComments, "sortCommentsByDate");
            $commentsT->comments = $totalComments;
            $contentT->content .= $commentsT->render();
        }
	}
}
else {
	//show all objects
	$commentsT->showObjectTitle = true;
	foreach ($data["posts"] as $postid => $currentpost) {
        $currentcategory = $currentpost["category"];
        if (($post === null && $category === null) || $currentcategory == $category) {
            $title = $currentpost["title"];
            $topic = new ImTopic($data['file_prefix'] . 'pc' . $postid, "", "../");
            $posturl = $commentsT->baseposturlcategory . $currentcategory . $commentsT->baseposturlpost . $postid;
            $topic = loadTopic($topic, $data, $posturl);
            if ($topic->hasComments()) {
                foreach ($topic->comments->comments as $cm) {
                    $cm["title"] = $title;
                    $cm["category"] = $currentcategory;
                    $cm["post"] = $postid;
                    $totalComments[] = $cm;
                }
            }
        }
	}
	if (count($totalComments)) {
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
