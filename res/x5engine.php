<?php

/**
 * This file contains all the classes used by the PHP code created by WebSite X5
 *
 * @category  X5engine
 * @package   X5engine
 * @copyright 2013 - Incomedia Srl
 * @license   Copyright by Incomedia Srl http://incomedia.eu
 * @version   WebSite X5 Evolution 13.0.0
 * @link      http://websitex5.com
 */

@session_start();


/**
 * Blog class
 * @access public
 */
class L10n
{
	private $l10n = array();

	/**
	 * Create a new localization object
	 * @param array $l10n The localizations array
	 */
	public function __construct($l10n)
	{
		$this->l10n = $l10n;
	}

	/**
	 * Get a localization string.
	 * The string is taken from the ones specified at step 1 of WSX5
	 *
	 * @method l10n
	 * 
	 * @param {string} $id      The localization key
	 * @param {string} $default The default string
	 * 
	 * @return {string}         The localization
	 */
	public function get($id, $default = "")
	{
	    if (!isset($this->l10n[$id]))
	        return $default;

	    return $this->l10n[$id];
	}
}

require_once('phar://' . __DIR__ . '/handlebars.phar/autoload.php');
use Handlebars\Handlebars;
use Handlebars\Loader\FilesystemLoader;


/**
 * Blog class
 * @access public
 */
class imBlog
{
    private $comments;
    private $targetTopic = "blog-topic";
    private $comPerPage = 10;

    /**
     * Set the number of comments to show in each page
     * @param integer $n
     */
    function setCommentsPerPage($n) {
        $this->comPerPage = $n;
    }

    /**
     * Format a timestamp
     *
     * @param string $ts The timestamp
     *
     * @return string
     */
    function formatTimestamp($ts)
    {
        return date("d/m/Y H:i:s", strtotime($ts));
    }

    /**
     * Parse the given array and extract the URL data for this post
     * 
     * @param Array $array The URL array ($_GET)
     * @return Array An associative array containing the post data
     */
    function parseUrlArray($array)
    {
        global $imSettings;
        
        $data = array('valid' => true);
        $keys = array_keys(@$_GET);

        if (isset($_GET['start']) && isset($_GET['length'])) {
            $data['start'] = @$_GET['start'];
            $data['length'] = @$_GET['length'];
        }
        
        if(isset($_GET['id'])) {
            $data['id'] = @$_GET['id'];
            $data['valid'] = isset($imSettings['blog']['posts'][$data['id']]) && $imSettings['blog']['posts'][$data['id']]['utc_time'] < time();
        }
        else if(isset($_GET['category'])) {
            $category = $this->getUnescapedCategory(@$_GET['category']);
            if ($category !== NULL) {
                $data['category'] = $category;
            }
        }
        else if(isset($_GET['author'])) {
            $author = $this->getUnescapedAuthor(@$_GET['author']);
            if ($author !== NULL) {
                $data['author'] = $author;
            }
        }
        else if(isset($_GET['tag'])) {
            $data['tag'] = @$_GET['tag'];
        }
        else if(isset($_GET['month'])) {
            $data['month'] = @$_GET['month'];
        }
        else if(isset($_GET['search'])) {
            $data['search'] = @$_GET['search'];
        }
        
        if (count($data) == 1 && count($keys) > 0) {
            if ($this->slugExists($keys[0])) {
                $id = $this->getSlugId($keys[0]);
                if ($imSettings['blog']['posts'][$id]['utc_time'] < time()) {
                    $data['slug'] = $keys[0];
                    $data['id'] = $id;
                } else {
                    $data['valid'] = false;
                }
            } else {
                $data['valid'] = false;
            }
        }
        return $data;
    }

    /**
     * Show the pagination links
     *
     * @param string  $baseurl  The base url of the pagination
     * @param integer $start    Start from this page
     * @param integer $length   For this length
     * @param integer $count    Count of the current objects to show
     *
     * @return void
     */
    function paginate($baseurl, $start, $length, $count)
    {
        $pages = ceil($count / $length);
        if ($pages < 2) {
            return;
        }

        $current = $start / $length + 1;
        echo "<div class=\"imBlogPagination pagination-container\">";
        if ($start > 0) {
            echo "<a href=\"" . $baseurl . "start=" . ($start - $length) . "&length=" . $length . "\" class=\"page\">" . l10n("cmn_pagination_prev") . "</a>";
        }
        $leading_dots = false;
        $trailing_dots = false;
        for ($i = 1; $i <= $pages; $i++) {
            if ($pages < 7 || $i == 1 || $i == $pages || ($i >= $current - 1 && $i <= $current + 1)) {
                echo "<a class=\"page" . ($i == $current ? " current" : "") . "\" href=\"" . $baseurl . "start=" . ($length * ($i - 1)) . "&length=" . $length . "\">" .
                     "<span class=\"screen-reader-only-even-focused\">" . ($i == $current ? l10n("cmn_pagination_current_page", "Current page:") : l10n("cmn_pagination_go_to_page", "Go to page:")) . "</span>" . $i . "</a>";
            }
            else if ($i < $current - 1 && !$leading_dots) {
                echo "<span class=\"dots-page\">...</span>";
                $leading_dots = true;
            }
            else if ($i > $current + 1 && !$trailing_dots) {
                echo "<span class=\"dots-page\">...</span>";
                $trailing_dots = true;
            }
        }
        if ($count > $start + $length) {
            echo "<a href=\"" . $baseurl . "start=" . ($start + $length) . "&length=" . $length . "\" class=\"page\">" . l10n("cmn_pagination_next") . "</a>";
        }
        echo "</div>";
    }

    /**
     * Provide the page title tag to be shown in the header
     * Keep track of the page using the $_GET vars provided:
     *     - id
     *     - category
     *     - author
     *     - tag
     *     - month
     *     - search
     *
     * @param string $basetitle The base title of the blog, to be appended after the specific page title
     * @param string $separator The separator char, default "-"
     *
     * @return string The page title
     */
    function pageTitle($basetitle, $separator = "-") {
        global $imSettings;

        $urlData = $this->parseUrlArray(@$_GET);

        if (isset($urlData['id']) && isset($imSettings['blog']['posts'][$urlData['id']])) {
            // Post
            return htmlspecialchars($imSettings['blog']['posts'][$urlData['id']]['tag_title']);
        } else if (isset($urlData['category']) && isset($imSettings['blog']['posts_cat'][$urlData['category']])) {
            // Category
            return htmlspecialchars($urlData['category'] . $separator . $basetitle);
        } else if (isset($urlData['author']) && isset($imSettings['blog']['posts_author'][$urlData['author']])) {
            // Author
            return htmlspecialchars($urlData['author'] . $separator . $basetitle);
        } else if (isset($urlData['tag'])) {
            // Tag
            return htmlspecialchars(strip_tags($urlData['tag']) . $separator . $basetitle);
        } else if (isset($urlData['month']) && is_numeric($urlData['month']) && strlen($urlData['month']) == 6) {
            // Month
            return htmlspecialchars(substr($urlData['month'], 4, 2) . "/" . substr($urlData['month'], 0, 4) . $separator . $basetitle);
        } else if (isset($urlData['search'])) {
            // Search
            return htmlspecialchars(strip_tags(urldecode($urlData['search'])) . $separator . $basetitle);
        }

        // Default (Home page): Show the blog description
        return htmlspecialchars($basetitle);
    }

    /**
     * Provide the page header title tag to be shown in the h1 header
     * Keep track of the page using the $_GET vars provided:
     *     - id
     *     - category
     *     - author
     *     - tag
     *     - month
     *     - search
     *
     * @param string $basetitle The base title of the blog, to be appended after the specific page title
     * @param string $separator The separator char, default "-"
     *
     * @return string The page title
     */
    function pageHeaderTitle($basetitle, $separator = "-") {
        global $imSettings;

        $urlData = $this->parseUrlArray(@$_GET);

        if (isset($urlData['id']) && isset($imSettings['blog']['posts'][$urlData['id']])) {
            // Post
            return htmlspecialchars($imSettings['blog']['posts'][$urlData['id']]['title']) ;
        } else if (isset($urlData['category']) && isset($imSettings['blog']['posts_cat'][$urlData['category']])) {
            // Category
            return htmlspecialchars($urlData['category']);
        } else if (isset($urlData['author']) && isset($imSettings['blog']['posts_author'][$urlData['author']])) {
            // Author
            return htmlspecialchars($urlData['author']);
        } else if (isset($urlData['tag'])) {
            // Tag
            return htmlspecialchars(strip_tags($urlData['tag']));
        } else if (isset($urlData['month']) && is_numeric($urlData['month']) && strlen($urlData['month']) == 6) {
            // Month
            return htmlspecialchars(substr($urlData['month'], 4, 2) . "/" . substr($urlData['month'], 0, 4));
        } else if (isset($urlData['search'])) {
            // Search
            return htmlspecialchars(strip_tags(urldecode($urlData['search'])));
        }

        // Default (Home page): Show the blog description
        return htmlspecialchars($basetitle);
    }

    /**
     * Get the open graph tags for a post
     * @param  $id   The post id
     * @param  $tabs The tabs (String) to prepend to each tag
     * @return string      The HTML tags
     */
    function getOpengraphTags($id, $tabs = "") {
        global $imSettings;
        $html = "";

        if (!isset($imSettings['blog']['posts'][$id]) || !isset($imSettings['blog']['posts'][$id]['opengraph'])) {
            return $html;
        }
        $og = $imSettings['blog']['posts'][$id]['opengraph'];
        if (isset($og['url'])) $html .= $tabs . '<meta property="og:url" content="' . htmlspecialchars($og['url']) . '" />' . "\n";
        if (isset($og['type'])) $html .= $tabs . '<meta property="og:type" content="' . $og['type'] . '" />' . "\n";
        if (isset($og['title'])) $html .= $tabs . '<meta property="og:title" content="' . htmlspecialchars($og['title']) . '" />' . "\n";
        if (isset($og['description'])) $html .= $tabs . '<meta property="og:description" content="' . htmlspecialchars($og['description']) . '" />' . "\n";
        if (isset($og['updated_time'])) $html .= $tabs . '<meta property="og:updated_time" content="' . htmlspecialchars($og['updated_time']) . '" />' . "\n";
        if (isset($og['video'])) $html .= $tabs . '<meta property="og:video" content="' . htmlspecialchars($og['video']) . '" />' . "\n";
        if (isset($og['video:type'])) $html .= $tabs . '<meta property="og:video:type" content="' . htmlspecialchars($og['video:type']) . '" />' . "\n";
        if (isset($og['audio'])) $html .= $tabs . '<meta property="og:audio" content="' . htmlspecialchars($og['audio']) . '" />' . "\n";
        if (isset($og['images']) && is_array($og['images'])) {
            foreach ($og['images'] as $image) {
                $html .= $tabs . '<meta property="og:image" content="' . htmlspecialchars($image) . '" />' . "\n";
            }
			if (count($og['images']) > 0) $html .= $tabs . '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        }
        return $html;
    }

    /**
     * Get the count of valid posts
     * @return integer
     */
    function getPostsCount() {
        global $imSettings;
        $count = 0;
        $utcTime = time();
        foreach ($imSettings['blog']['posts'] as $id => $post) {
            if ($post['utc_time'] <= $utcTime) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the comments sent in the specified period that are already validated
     *
     * @param  String $from
     * @param  String $to
     *
     * @return Array
     */
    function getComments($from = "", $to = "")
    {
        global $imSettings;
        $bs = $imSettings['blog'];
        $commentsArray = array();

        foreach ($bs['posts'] as $post) {
            $comments = new ImTopic($bs['file_prefix'] . 'pc' . $post['id'], $this->targetTopic, "../", $post['rel_url']);
                $comments->loadXML($bs['folder']);
            foreach ($comments->getComments($from, $to) as $comment) {
                $comment["title"] = $post['title'];
                $comment["category"] = $post['category'];
                $comment["postid"] = $post['id'];
                $commentsArray[] = $comment;
            }
        }
        // Order the array in descending order
        usort($commentsArray, array("ImTopic", "compareCommentsArray"));
        return $commentsArray;
    }

    /**
     * Get the comments sent in the specified period that are still to be validated
     *
     * @param  String $from
     * @param  String $to
     *
     * @return Array
     */
    function getCommentsToValidate($from = "", $to = "")
    {
        $settings = Configuration::getSettings();
        $bs = $settings['blog'];
        $toApprove = array();

        foreach ($bs['posts'] as $post) {
            $comments = new ImTopic($bs['file_prefix'] . 'pc' . $post['id'], $this->targetTopic, "../", $post['rel_url']);
                $comments->loadXML($bs['folder']);
            foreach ($comments->getComments($from, $to, false) as $comment) {
                $comment["title"] = $post['title'];
                $toApprove[] = $comment;
            }
        }
        // Order the array in descending order
        usort($toApprove, array("ImTopic", "compareCommentsArray"));
        return $toApprove;
    }

    /**
     * Get the posts enabled for visualization
     * @return array
     */
    function getPosts() {
        global $imSettings;
        $posts = array();
        $utcTime = time();
        foreach ($imSettings['blog']['posts'] as $id => $post) {
            if ($post['utc_time'] <= $utcTime) {
                $posts[$id] = $post;
            }
        }
        return $posts;
    }

    /**
     * Get the posts enabled for visualization filtered by category, author, tag, etc.
     * Possible filter names: posts_author, posts_cat, posts_month, posts_tag
     * @param array $filters associative array $filter_name => $filter_value. es: array('posts_author' => 'the author name').
     * @return array
     */
    function getFilteredPosts($filters)
    {
        global $imSettings;
        $utcTime = time();
        $posts = array();
        if (is_array($filters)) {
            foreach ($filters as $filter_name => $filter_value) {
                if($filter_value == '|All|'){
                    return $this->getPosts();
                }
                if (isset($imSettings['blog'][$filter_name][$filter_value])) {
                    foreach ($imSettings['blog'][$filter_name][$filter_value] as $id) {
                        if (isset($imSettings['blog']['posts'][$id]) && $imSettings['blog']['posts'][$id]['utc_time'] <= $utcTime) {
                            $posts[$id] = $imSettings['blog']['posts'][$id];
                        }
                    }
                }
            }
        }
        return $posts;
    }

    function getPostsFromUrlData()
    {
        global $imSettings;
        $data = $this->parseUrlArray(@$_GET);
        if (isset($data['id'])) {
            return isset($imSettings['blog']['posts'][$data['id']]) ? array($data['id'] => $imSettings['blog']['posts'][$data['id']]) : array();
        } else {
            $posts = array();
            if (isset($data['category'])) {
                $posts = $this->getCategoryPosts($data['category']);
            } else if (isset($data['author'])) {
                $posts = $this->getAuthorPosts($data['author']);
            } else if (isset($data['tag'])) {
                $posts = $this->getTagPosts($data['tag']);
            } else if (isset($data['month'])) {
                $posts = $this->getMonthPosts($data['month']);
            } else if (isset($data['search'])) {
                $posts = $this->getSearchPosts($data['search']);
            } else {
                $posts = $this->getPosts();
            }
            $start = isset($data['start']) ? max(0, (int) $data['start']) : 0;
            $length = isset($data['length']) ? (int) $data['length'] : $imSettings['blog']['home_posts_number'];
            return array_slice($posts, $start, $length);
        }
    }

    /**
     * Get Unescaped Category
     * @return boolean
     */
    function getUnescapedCategory($category) {
        if ($category == "|All|")
            return "|All|";
        global $imSettings;
        foreach ($imSettings['blog']['posts_cat'] as $cat => $posts) {
            if (str_replace(' ', '_', $cat) === str_replace(' ', '_', $category)) {
                return $cat;
            }
        }
        return NULL;
    }

    /**
     * Get Unescaped Author
     * @return boolean
     */
    function getUnescapedAuthor($author) {
        if ($author == "|All|")
            return "|All|";
        global $imSettings;
        foreach ($imSettings['blog']['posts_author'] as $aut => $posts) {
            if (str_replace(' ', '_', $aut) === str_replace(' ', '_', $author)) {
                return $aut;
            }
        }
        return NULL;
    }

    /**
     * Get the count of valid posts in a category
     * @return integer
     */
    function getCategoryPostCount($category)
    {
        return count($this->getCategoryPosts($category));
    }

    /**
     * Get the count of valid posts by an author
     * @return integer
     */
    function getAuthorPostCount($author)
    {
        return count($this->getAuthorPosts($author));
    }

    /**
     * Get the posts enabled for visualization in a category
     * @return array
     */
    function getCategoryPosts($category)
    {
        return $this->getFilteredPosts(array('posts_cat' => $category));
    }

    /**
     * Get the posts by an author
     * @return array
     */
    function getAuthorPosts($author)
    {
        return $this->getFilteredPosts(array('posts_author' => $author));
    }

    /**
     * Get the count of valid posts in a Tag
     * @return integer
     */
    function getTagPostCount($tag)
    {
        return count($this->getTagPosts($tag));
    }

    /**
     * Get the posts enabled for visualization in a tag
     * @return array
     */
    function getTagPosts($tag)
    {
        return $this->getFilteredPosts(array('posts_tag' => $tag));
    }

    /**
     * Get the posts of a month
     * @param  string $month
     * @return integer
     */
    function getMonthPostsCount($month)
    {
        return count($this->getMonthPosts($month));
    }

    /**
     * Get the posts of a month
     * @param  string $month
     * @return array
     */
    function getMonthPosts($month)
    {
        return $this->getFilteredPosts(array('posts_month' => $month));
    }

    /**
     * Show the page description to be echoed in the metatag description tag.
     * Keep track of the page using the $_GET vars provided:
     *     - id
     *     - category
     *     - author
     *     - tag
     *     - month
     *     - search
     *
     * @return string The required description
     */
    function pageDescription()
    {
        global $imSettings;

        $data = $this->parseUrlArray(@$_GET);

        if (isset($data['id']) && isset($imSettings['blog']['posts'][$data['id']])) {
            // Post
            return htmlspecialchars(str_replace("\n", " ", $imSettings['blog']['posts'][$data['id']]['tag_description']));
        } else if (isset($data['category'])) {
            // Category
            return htmlspecialchars(strip_tags($data['category']));
        } else if (isset($data['author'])) {
            // Author
            return htmlspecialchars(strip_tags($data['author']));
        } else if (isset($data['tag'])) {
            // Tag
            return htmlspecialchars(strip_tags($data['tag']));
        } else if (isset($data['month'])) {
            // Month
            return htmlspecialchars(substr($data['month'], 4, 2) . "/" . substr($data['month'], 0, 4));
        } else if (isset($data['search'])) {
            // Search
            return htmlspecialchars(strip_tags(urldecode($data['search'])));
        }

        // Default (Home page): Show the blog description
        return htmlspecialchars(str_replace("\n", " ", $imSettings['blog']['description']));
    }

    /**
     * Show the page keywords to be echoed in the metatag keywords tag.
     *
     * @return string The required keywords
     */
    function pageKeywords()
    {
        global $imSettings;
        $data = $this->parseUrlArray(@$_GET);
        if (isset($data['id']) && isset($imSettings['blog']['posts'][$data['id']])) {
            // Post
            return htmlspecialchars(str_replace("\n", " ", $imSettings['blog']['posts'][$data['id']]['keywords']));
        } 
        // Default (Home page): Show the blog keywords
        return htmlspecialchars(str_replace("\n", " ", $imSettings['blog']['keywords']));
    }

    /**
     * Get the last update date
     *
     * @return string
     */
    function getLastModified()
    {
        global $imSettings;
        $c = $this->comments->getComments($_GET['id']);
        if ($_GET['id'] != "" && $c != -1) {
            return $this->formatTimestamp($c[count($c)-1]['timestamp']);
        } else {
            $utcTime = time();
            foreach ($imSettings['blog']['posts'] as $id => $post) {
                if ($post['utc_time'] < $utcTime) {

                }
            }
            $last_post = $imSettings['blog']['posts'];
            $last_post = array_shift($last_post);
            return $last_post['timestamp'];
        }
    }

    /**
     * Get the slug URL of the given post id
     * @param  string $id
     * @return string Empty if the slug does not exist
     */
    function getSlugUrl($id)
    {
        global $imSettings;

        $bs = $imSettings['blog'];
        if (isset($bs['posts'][$id]) && $this->slugExists($bs['posts'][$id]['slug'])) {
            return "?" . $bs['posts'][$id]['slug'];
        }
        return "";
    }

    /**
     * Get the slug ID given the slug itself
     * 
     * @param string $slug
     * @return string Empty if the slug was not found
     */
    function getSlugId($slug)
    {
        global $imSettings;
        
        $bs = $imSettings['blog'];
        foreach ($bs['posts'] as $id => $post) {
            if ($post['slug'] == $slug) {
                return $id;
            }
        }
        return "";
    }

    /**
     * Check if a slug exists in this blog
     * @param  string $slug The slug you're looking for
     * @return bool         True if the slug exists
     */
    function slugExists($slug)
    {
        global $imSettings;
        return isset($imSettings['blog']['posts_slug'][$slug]);
    }

    /**
     * Show a post
     *
     * @param string  $slug  the post slug
     * @param inetger $ext   Set 1 to show as extended
     *
     * @return void
     */
    function showSlug($slug, $ext=0)
    {
        global $imSettings;

        if ($this->slugExists($slug)) {
            $this->showPost($imSettings['blog']['posts_slug'][$slug], $ext);
        }
    }

    
    /**
     * Show a post
     *
     * @param string  $id    the post id
     * @param integer $ext   Set 1 to show as extended
     *
     * @return void
     */
    function showPost($id, $ext=0, $isHighlighted = false)
    {
        global $imSettings;

        $bs = $imSettings['blog'];
        $bp = isset($bs['posts'][$id]) ? $bs['posts'][$id] : false;
        $utcTime = time();

        if (is_bool($bp) || $bp['utc_time'] > $utcTime)
            return;

        if ($ext) {
            $cover_html = ($bp['cover'] !== '' ? "<img id=\"imBlogPostCover_" . $id . "\" class=\"imBlogPostCover\" src=\"../" . $bp['cover'] . "\" alt=\"" . $bp['coverAlt'] . "\" title=\"" . $bp['coverTitle'] . "\"/>\n" : '');

            if (isset($bs['article_type']) && $bs['article_type'] == 'covertitlecontents') {
                echo $cover_html;
            }

            echo "<header>\n";
            echo "  <" . $bp['title_heading_tag'] . " class=\"imPgTitle\" style=\"display: block;\">" . $bp['title'] . "</" . $bp['title_heading_tag'] . ">\n";
            echo "</header>\n";

            // Publisher Microdata
            echo "<span style=\"display: none\">";
            echo "<span >" . $imSettings['general']['sitename'] . "</span>";
            if (strlen($imSettings['general']['icon'])) {
                echo "<img src=\"" . $imSettings['general']['icon'] . "\" alt=\"\" />";
            }
            echo "</span>";

            echo "<div class=\"imBreadcrumb\" style=\"display: block;\"><span>";
            if ($bp['author'] != "" || $bp['category'] != "") {
                echo l10n('blog_published') . " ";
                if ($bp['author'] != "") {
                    echo l10n('blog_by') . " <a href=\"?author=" . urlencode(str_replace(' ', '_', $bp['author'])) . "\" target=\"_blank\"><span><strong>" . $bp['author'] . "</strong></span></a> ";
                }
                if ($bp['category'] != "") {
                    echo l10n('blog_in') . " <a href=\"?category=" . urlencode(str_replace(' ', '_', $bp['category'])) . "\" target=\"_blank\"><span>" . $bp['category'] . "</span></a> ";
                }
                echo "&middot; ";
            }
            echo $bp['timestampExt'];
            if ($bp['word_count'] > 0) {
                echo " &middot; <svg aria-labelledby=\"blog_post_" . $id . "_read_time\" role=\"img\" fill=\"none\" stroke=\"currentColor\" stroke-linecap=\"square\" stroke-width=\"2px\" color=\"currentColor\" viewBox=\"0 0 24 24\" width=\"1.2em\" height=\"1.2em\" style=\"vertical-align: text-top;\">";
                echo "<title id=\"blog_post_" . $id . "_read_time\">" . l10n("blog_icon_read_time") . "</title>";
                echo "<circle cx=\"12\" cy=\"12\" r=\"10\"></circle><path d=\"M12 5v7l4 4\"></path></svg>&nbsp;";
                $seconds = ceil($bp['word_count'] * 60 / $bs['article_read_speed']);
                $seconds = floor(($seconds + 14) / 15) * 15; // round up seconds at multiple of 15 seconds
                if ($seconds <= 60)
                    echo "1:00";
                else
                    echo floor($seconds / 60) . ":" . ($seconds % 60 < 10 ? "0" : "") . ($seconds % 60);
            }
            echo "</span>";

            if (count($bp['tag']) > 0) {
                echo "<br />Tags: ";
                for ($i = 0; $i < count($bp['tag']); $i++) {
                    if ($i > 0)
                        echo ",&nbsp;";
                    echo "<a href=\"?tag=" . $bp['tag'][$i] . "\">" . $bp['tag'][$i] . "</a>";
                }
            }
            echo "</div>\n";

            if (isset($bs['article_type']) && $bs['article_type'] == 'titlecovercontents') {
                echo $cover_html;
            }

            echo "<div class=\"imBlogPostBody\">\n";

            // Check if post's body contains PHP code: in this case evaluate it
            if (strpos($bp['body'], '<?php') !== false && strpos($bp['body'], '?>') !== false) {
                $body = $bp['body'];
                eval("?> $body <?php ;");
            }
            else {
                echo $bp['body'];
            }

            if (isset($bp['mediahtml']) || isset($bp['slideshow'])) {
                echo "<div class=\"imBlogMedia\">\n";
                // Audio/video
                if (isset($bp['mediahtml'])) {
                    echo $bp['mediahtml'] . "\n";
                }
                // Slideshow
                if (isset($bp['slideshow'])) {
                    echo $bp['slideshow'];
                }
                echo "</div>\n";
            }

            if (count($bp['sources']) > 0) {
                echo "\t<div class=\"imBlogSources\">\n";
                echo "\t\t<b>" . l10n('blog_sources') . "</b>:<br />\n";
                echo "\t\t<ul>\n";

                foreach ($bp['sources'] as $source) {
                    echo "\t\t\t<li>" . $source . "</li>\n";
                }

                echo "\t\t</ul>\n\t</div>\n";
            }
            echo "<br /><br /></div>\n";
            if (isset($bp['foo_html'])) {
                echo "<div class=\"imBlogPostFooHTML\">" . $bp['foo_html'] . "</div>\n";
            }

            // Schema.org Image
            if (isset($bp['opengraph']['postimage'])) {
                echo "<img src=\"../" . $bp['opengraph']['postimage'] . "\" style=\"display: none\" alt=\"\">";
            }

            if ($bp['comments']) {
                if ($imSettings['blog']['comments_source'] == 'wsx5') {
                    echo "<div id=\"" . $this->targetTopic . "\">\n";
                    $this->comments = new ImTopic($imSettings['blog']['file_prefix'] . 'pc' . $id,  $this->targetTopic , "../", $bp['rel_url']);
                    $this->comments->setCommentsPerPage($this->comPerPage);
                    // Show the comments
                        $this->comments->loadXML($bs['folder']);
                    $this->comments->setPostUrl($bp['rel_url']);
                    if ($imSettings['blog']['comment_type'] != "stars") {
                        $this->comments->showSummary($bs['comment_type'] != "comment");
                        $this->comments->showForm($bs['comment_type'] != "comment", $bs['captcha'], $bs['moderate'], $bs['email'], "blog", $imSettings['general']['url'] . "/admin/blog.php?category=" . str_replace(" ", "_", $imSettings['blog']['posts'][$id]['category']) . "&post=" . $id);
                        $this->comments->showComments($bs['comment_type'] != "comment", $bs["comments_order"], $bs["abuse"], $bs["comments_on_multiple_columns"]);
                        $newMessage = $this->comments->checkNewMessages($bs['moderate'], $bs['email'], "blog", $imSettings['general']['url'] . "admin/blog.php?category=" . str_replace(" ", "_", $imSettings['blog']['posts'][$id]['category']) . "&post=" . $id);
                    } else {
                        $this->comments->showRating();
                    }
                    echo "</div>";
                    echo "<script>x5engine.boot.push('x5engine.topic({ target: \\'#blog-topic\\', scrollbar: false})', false, 6);</script>\n";
                } else {
                    echo $imSettings['blog']['comments_code'];
                }
            }
        }
        else {
            echo "<article class=\"imBlogPostCard" . ($bp['cardCover'] !== "" ? " imBlogPostCardWithCover" : "") . "\">";
            if ($bp['cardCover'] !== "") {
                echo "<div class=\"imBlogPostWrapperCardCover\"><img id=\"imBlogPostCardCover_" . $id . "\" class=\"imBlogPostCardCover\" src=\"../" . ($isHighlighted ? $bp['cover'] : $bp['cardCover']) . "\" alt=\"" . $bp['coverAlt'] . "\" title=\"" . $bp['coverTitle'] . "\" onclick=\"window.location='" . $bp['rel_url'] . "';\"/></div>";
            }
            if ($imSettings['blog']['show_card_title']) {
                echo "<header class=\"imBlogPostCardTitle\"><h1><a href=\"" . $bp['rel_url'] . "\">" . $bp['title'] . "</a></h1></header>";
            }
            if ($imSettings['blog']['show_card_category']) {
                echo "<div class=\"imBlogPostCardCategory\"><a href=\"?category=" . urlencode(str_replace(' ', '_', $bp['category'])) . "\">" . $bp['category'] . "</a></div>";
            }
            if ($imSettings['blog']['show_card_description']) {
                echo "<div class=\"imBlogPostCardDescription\">" . $bp['summary'] . "<div class=\"imBlogPostCardDescriptionFade\"></div></div>";
            }
            if ($imSettings['blog']['show_card_author'] || $imSettings['blog']['show_card_date']) {
                echo "<div class=\"imBlogPostCardDetails\">";
                echo "<div class=\"imBlogSeparator\"></div>";
                if ($imSettings['blog']['show_card_author']) {
                    echo "<span class=\"imBlogPostCardAuthor\"><a href=\"?author=" . urlencode(str_replace(' ', '_', $bp['author'])) . "\">" . $bp['author'] . "</a></span>";
                }
                if ($imSettings['blog']['show_card_date']) {
                    if ($imSettings['blog']['show_card_author'] && $bp['author'] != "") {
                        echo " | ";
                    }
                    echo "<span class=\"imBlogPostCardDate\">" . $bp['timestamp'] . "</span>";
                }
                echo "</div>";
            }
            if ($imSettings['blog']['show_card_button']) {
                echo "<div class=\"imBlogPostCardButton\"><a href=\"" . $bp['rel_url'] . "\">" . l10n('blog_read_all') . "</a></div>";
            }
            echo "</article>";
        }
    }

    /**
     * Prints the scripts to be included
     *
     * @param string $id   The post id
     * @param string $tabs The script tag prefix
     *
     * @return void
     */
    function printAMPIncludes($id, $tabs) {
        global $imSettings;

        $bs = $imSettings['blog'];
        $bp = isset($bs['posts'][$id]) ? $bs['posts'][$id] : false;
        $utcTime = time();

        if (is_bool($bp) || $bp['utc_time'] > $utcTime)
            return "";

        if (isset($bp['slideshow_amp']) && stristr($bp['slideshow_amp'], "amp-carousel") !== false) {
            echo $tabs . "<script async custom-element=\"amp-carousel\" src=\"https://cdn.ampproject.org/v0/amp-carousel-0.1.js\"></script>\n";
        }
        if (isset($bp['mediahtml_amp']) && stristr($bp['mediahtml_amp'], "amp-video") !== false) {
            echo $tabs . "<script async custom-element=\"amp-video\" src=\"https://cdn.ampproject.org/v0/amp-video-0.1.js\"></script>\n";
        }
        if (isset($bp['mediahtml_amp']) && stristr($bp['mediahtml_amp'], "amp-audio") !== false) {
            echo $tabs . "<script async custom-element=\"amp-audio\" src=\"https://cdn.ampproject.org/v0/amp-audio-0.1.js\"></script>\n";
        }
        if (isset($bp['mediahtml_amp']) && stristr($bp['mediahtml_amp'], "amp-youtube") !== false) {
            echo $tabs . "<script async custom-element=\"amp-youtube\" src=\"https://cdn.ampproject.org/v0/amp-youtube-0.1.js\"></script>\n";
        }
    }

    /**
     * Get the AMP CSS for a post
     *
     * @param  string $id The post id
     *
     * @return string     The CSS Code
     */
    function getAMPCSS($id) {
        global $imSettings;

        $bs = $imSettings['blog'];
        $bp = isset($bs['posts'][$id]) ? $bs['posts'][$id] : false;
        $utcTime = time();

        if (is_bool($bp) || $bp['utc_time'] > $utcTime)
            return "";

        $cover = "";
        if ($bp['cover'] !== '') {
            $cover = "\t\t\t.post-cover { background-image: url('../../" . $bp['cover'] . "'); background-position: center center; background-repeat: no-repeat; background-size: " . ($bs['cover_adapted'] ? "cover" : "contain") . "; height: " . $bs['cover_height'] . "px; margin: 10px 0; }\n";
        }

        return $cover . $bs['amp_css'] . $bp['body_css'];
    }

    /**
     * Prints WebFonts Link tags for AMP
     *
     * @param string $id   The post id
     * @param string $tabs The link tag prefix
     *
     * @return void
     */
    function printAMPWebFontsLinks($id, $tabs) {
        global $imSettings;

        $bs = $imSettings['blog'];
        $bp = isset($bs['posts'][$id]) ? $bs['posts'][$id] : false;
        $utcTime = time();

        if (is_bool($bp) || $bp['utc_time'] > $utcTime)
            return "";

        if (isset($bs['amp_webfonts_links']) && is_array($bs['amp_webfonts_links'])) {
            foreach ($bs['amp_webfonts_links'] as $link) {
                echo $tabs . $link;
            }
        }
        if (isset($bp['body_webfonts_links']) && is_array($bp['body_webfonts_links'])) {
            foreach ($bp['body_webfonts_links'] as $link) {
                echo $tabs . $link;
            }
        }
    }

    /**
     * Get the AMP CSS for a post
     *
     * @param  string $id The post id
     *
     * @return string     The CSS Code
     */
    function getAMPHeaderHTML() {
        global $imSettings;

        $bs = $imSettings['blog'];

        return $bs['amp_header'];
    }

    /**
     * Show the AMP version of a Post
     *
     * @param  string $id The post id
     *
     * @return void
     */
    function showAMPPost($id)
    {
        global $imSettings;

        // Post Data
        $bs = $imSettings['blog'];
        $bp = isset($bs['posts'][$id]) ? $bs['posts'][$id] : false;
        $utcTime = time();
        if (is_bool($bp) || $bp['utc_time'] > $utcTime) {
            return;
        }

        // Build Timestamp
        $text = l10n('date_full_months');
        $timestamp = explode('/', $bp['timestamp']);
        $timestamp[1] = $text[$timestamp[1] - 1];
        $timestamp = implode(' ', $timestamp);

        // Prepare Cover
        $cover = "";
        if ($bp['cover'] !== '') {
            $cover = "\t\t\t<div class=\"post-cover\"></div>\n";
        }

        // Prepare Details
        $details = "";
        if ($bp['author'] != "" || $bp['category'] != "") {
            $details .= l10n('blog_published') . " ";
            if ($bp['author'] != "") {
                $details .= l10n('blog_by') . " <strong>" . $bp['author'] . "</strong> ";
            }
            if ($bp['category'] != "") {
                $details .= l10n('blog_in') . " <a href=\"../?category=" . urlencode(str_replace(' ', '_', $bp['category'])) . "\">" . $bp['category'] . "</a> ";
            }
            $details .= "&middot; ";
        }
        $details .= $timestamp;
        if (count($bp['tag']) > 0) {
            $details .= "<br />Tags: ";
            for ($i = 0; $i < count($bp['tag']); $i++) {
                if ($i > 0)
                    $details .= ",&nbsp;";
                $details .= "<a href=\"../?tag=" . $bp['tag'][$i] . "\">" . $bp['tag'][$i] . "</a>";
            }
        }

        if (isset($bs['article_type']) && $bs['article_type'] == 'covertitlecontents') {
            echo $cover;
        }
        echo "\t\t\t<h1 class=\"post-title\">" . $bp['title'] . "</h1>\n";
        echo "\t\t\t<div class=\"post-details\">" . $details . "</div>\n";
        if (isset($bs['article_type']) && $bs['article_type'] == 'titlecovercontents') {
            echo $cover;
        }
        echo "\t\t\t<div class=\"post-body\">" . $bp['body_amp'] . "</div>\n";
        if (isset($bp['mediahtml_amp'])) {
            echo $bp['mediahtml_amp'];
        }
        if (isset($bp['slideshow_amp'])) {
            echo $bp['slideshow_amp'];
        }
    }



	function customCardL10nStrings(){

		$strings = array(
			"blog_read_all" => l10n("blog_read_all"),
			"blog_icon_category" => l10n("blog_icon_category"),
			"blog_icon_author" => l10n("blog_icon_author"),
			"blog_icon_date" => l10n("blog_icon_date"),
			"blog_icon_read_time" => l10n("blog_icon_read_time")
		);

		return $strings;

	}



	function customCardBreakpointString( $cardStyle ){

		$breakpointString = "";
		$cardsperrowString = "";
        $layoutVertMediaQuery = "";
		$card = $cardStyle["card"];

        global $imSettings;
		$breakPoints = $imSettings['breakpoints'];

		if ( isset( $breakPoints ) && count( $breakPoints ) > 0 ) {

			$minContentW = 200;
			$minCardW = $minContentW;
			$minCardW = $minCardW + $card["margin"] + $card["margin"];
			if ( $card["type"] == "leftcoverrightcontents" || $card["type"] == "leftcontentsrightcover" ) {
				$minCardW = floor( $minCardW * 100.0 / ( 100 - $card["image"]["percentSize"] ) );
				$minCardW = $minCardW + $card["image"]["margins"]["left"] + $card["image"]["margins"]["right"];
			}

			$strPieceBPdesktop = "";
			$strPieceCPRdesktop = "";
			for ( $i = 0 ; $i < count( $breakPoints ) ; $i++ ) {

				$maxAvailW = $breakPoints[$i]["end"];
				if ( $breakPoints[$i]["end"] == 0 ) {
					$maxAvailW = $breakPoints[$i]["start"];
                    if ( $maxAvailW == "max" )
					    $maxAvailW = $breakPoints[$i]["width"];
				}

				$cpr = $cardStyle["cardsPerRow"];
				$BPEnd = $breakPoints[$i]["end"];
                if ( $breakPoints[$i]["end"] == 0 && $breakPoints[$i]["start"] == "max" )
					$BPEnd = $breakPoints[$i]["width"];
				$w = max( floor( min( $BPEnd , $maxAvailW ) / $cpr ) , $minCardW );
				$cpr = max( floor( min( $BPEnd , $maxAvailW ) / $w ) , 1 );
				$w = max( floor( min( $BPEnd , $maxAvailW ) / $cpr ) , $minCardW );

				$strPieceBP = " (max-width: START) END,";
				$strPieceCPR = " (max-width: START) END,";

				if ( $breakPoints[$i]["start"] == "max" ) {
					$strPieceBP = " END";
					$strPieceCPR = " END";
				} else {
					$strPieceBP = str_replace( "START" , $breakPoints[$i]["start"] . "px" , $strPieceBP );
					$strPieceCPR = str_replace( "START" , $breakPoints[$i]["start"] . "px" , $strPieceCPR );
				}

				if ( $breakPoints[$i]["end"] == 0 ) {
					$strPieceBP = str_replace( "END" , "100%" , $strPieceBP );
				} else {
					$strPieceBP = str_replace( "END" , $w . "px" , $strPieceBP );
				}
				$strPieceCPR = str_replace( "END" , "" . $cpr , $strPieceCPR );

				if ( $breakPoints[$i]["start"] == "max" ) {
					$strPieceBPdesktop = $strPieceBP;
					$strPieceCPRdesktop = $strPieceCPR;
				} else {
					$breakpointString .= $strPieceBP;
					$cardsperrowString .= $strPieceCPR;
				}

                if ( $maxAvailW < $minCardW ) {
                    if ( strlen($layoutVertMediaQuery) > 0 )
                        $layoutVertMediaQuery .= " or ";
                    if ( $breakPoints[$i]["start"] == "max" ) {
                        $layoutVertMediaQuery .= "(min-width: " . $breakPoints[$i]["end"] . "px)";
                    } else {
                        if ( $breakPoints[$i]["end"] > 0 )
                            $layoutVertMediaQuery .= "((min-width: " . $breakPoints[$i]["end"] . "px) and ";
                        $layoutVertMediaQuery .= "(max-width: " . $breakPoints[$i]["start"] . "px)";
                        if ( $breakPoints[$i]["end"] > 0 )
                            $layoutVertMediaQuery .= ")";
                    }
                }
			}
			$breakpointString .= $strPieceBPdesktop;
			$cardsperrowString .= $strPieceCPRdesktop;

		}

        if (count( $breakPoints ) == 1){
            $layoutVertMediaQuery = null;
        }

        $result = array(
            "breakpointString" => $breakpointString,
            "cardsperrowString" => $cardsperrowString,
            "layoutVertMediaQuery" => $layoutVertMediaQuery
        );

		return $result;

	}



	function customCardContentLayout( $card ){

                if ( $card["type"] == "leftcoverrightcontents" ) {
                    return "horizontal-cover-left"; // same as default
                } else if ( $card["type"] == "leftcontentsrightcover" ) {
                    return "horizontal-cover-right";
                } else if ( $card["type"] == "topcoverbottomcontents" ) {
                    return "vertical-cover-top";
                } else if ( $card["type"] == "topcontentsbottomcover" ) {
                    return "vertical-title-top";
                } else if ( $card["type"] == "coverasbackground" ) {
                    return "cover-as-background";
                }

                return "horizontal-cover-left";//default

	}



	function customCardLayoutArrangement( $cardStyle ){

                if ( $cardStyle["layout"] == "slideshow" ) {
                    return "slideshow";
                } else if ( $cardStyle["layout"] == "masonry" ) {
                    return "masonry";
                } else if ( $cardStyle["layout"] == "variableheight" ) {
                    return "changing-height";
                } else {
                    // if $cardStyle["layout"] == "fixedheight"
                    return "same-height";
                }

	}



	function customCardMisc( $rootSelector, $cardStyle ){

		$misc = array();

		if ( isset( $rootSelector ) ) {
			$misc["rootSelector"] = $rootSelector;
		}

		if ( isset( $cardStyle ) ) {

			$BPValues = $this->customCardBreakpointString( $cardStyle );
			$misc["cardBreakpoint"] = $BPValues["breakpointString"];            
			$misc["cardContentLayout"] = $this->customCardContentLayout( $cardStyle["card"] );
			$misc["cardLayoutCardArrangement"] = $this->customCardLayoutArrangement( $cardStyle );
			$misc["cardLayoutCardHeight"] = $cardStyle["card"]["height"];
			$misc["cardLayoutCardsPerRow"] = $BPValues["cardsperrowString"];
			$misc["layoutVertMediaQuery"] = $BPValues["layoutVertMediaQuery"];

		}

		return $misc;

	}



	function getCalculatedGlobalData( $card, $misc ){

        $mobileBPMaxWidth = 479.9;
        if ( isset( $imSettings['breakpoints'] ) && count( $imSettings['breakpoints'] ) > 0 )
            $mobileBPMaxWidth = $imSettings['breakpoints'][count( $imSettings['breakpoints'] ) - 1]["start"];
    
		$calculated = array();

		// Calculated properties for Cover image

		if ( isset( $card["image"]["mouseovereffect"] ) ) {
			if ( $card["image"]["mouseovereffect"] == "grow" ) {
				$calculated["imageOverRule"] = "transform: scale(1.1); ";
			} else if ( $card["image"]["mouseovereffect"] == "shrink" ) {
				$calculated["imageOverRule"] = "transform: scale(0.9); ";
			} else if ( $card["image"]["mouseovereffect"] == "rotate" ) {
				$calculated["imageOverRule"] = "transform: rotate(4deg); ";
			}
		}

		if ( isset( $misc["cardBreakpoint"] ) ) {
			$calculated["cardBreakpoint"] = $misc["cardBreakpoint"];
		}

		if ( isset( $misc["cardContentDimension"] ) ) {
			$calculated["cardContentDimension"] = $misc["cardContentDimension"];
		}

		$calculated["cardContentLayout"] = "horizontal-cover-left";
		if ( $card["type"] == "leftcoverrightcontents" ) {
			$calculated["cardContentLayout"] = "horizontal-cover-left"; // same as default
		} else if ( $card["type"] == "leftcontentsrightcover" ) {
			$calculated["cardContentLayout"] = "horizontal-cover-right";
		} else if ( $card["type"] == "topcoverbottomcontents" ) {
			$calculated["cardContentLayout"] = "vertical-cover-top";
		} else if ( $card["type"] == "topcontentsbottomcover" ) {
			$calculated["cardContentLayout"] = "vertical-title-top";
		}
		if ( isset( $misc["cardContentLayout"] ) && (
			$misc["cardContentLayout"] == "horizontal-cover-left" || 
			$misc["cardContentLayout"] == "horizontal-cover-right" || 
			$misc["cardContentLayout"] == "vertical-cover-top" || 
			$misc["cardContentLayout"] == "vertical-title-top" || 
			$misc["cardContentLayout"] == "cover-as-background"
		) ) {
			// $misc["cardContentLayout"] overrides default and card.type
			$calculated["cardContentLayout"] = $misc["cardContentLayout"];
		}

        $calculated["cardFixedHeight"] = false;
        if ( 
            isset( $misc["cardLayoutCardArrangement"] ) && 
            ( $misc["cardLayoutCardArrangement"] == "same-height" || $misc["cardLayoutCardArrangement"] == "slideshow" )
        ) {
            $calculated["cardFixedHeight"] = true;
        }

        $calculated["isArrangeSameHeight"] = ( isset($misc["cardLayoutCardArrangement"]) && $misc["cardLayoutCardArrangement"] == "same-height" );
        $calculated["isArrangeChangingHeight"] = ( isset($misc["cardLayoutCardArrangement"]) && $misc["cardLayoutCardArrangement"] == "changing-height" );
        $calculated["isArrangeSlideshow"] = ( isset($misc["cardLayoutCardArrangement"]) && $misc["cardLayoutCardArrangement"] == "slideshow" );
        $calculated["isArrangeMasonry"] = ( isset($misc["cardLayoutCardArrangement"]) && $misc["cardLayoutCardArrangement"] == "masonry" );

        $calculated["isArrangeSameHeightOrChangingHeight"] = ( $calculated["isArrangeSameHeight"] || $calculated["isArrangeChangingHeight"] );
        $calculated["isArrangeChangingHeightOrMasonry"] = ( $calculated["isArrangeChangingHeight"] || $calculated["isArrangeMasonry"] );

        $calculated["isLayoutHorizontalCoverLeft"] = ( $calculated["cardContentLayout"] == "horizontal-cover-left" );
        $calculated["isLayoutHorizontalCoverRight"] = ( $calculated["cardContentLayout"] == "horizontal-cover-right" );
        if ( $misc["layoutVertMediaQuery"] && ($calculated["isLayoutHorizontalCoverLeft"] || $calculated["isLayoutHorizontalCoverRight"]) )
            $calculated["layoutHorizontalMediaQueryStart"] = ( "@media not (" . $misc["layoutVertMediaQuery"] . ") {" );
        $calculated["isLayoutVerticalCoverTop"] = ( $calculated["cardContentLayout"] == "vertical-cover-top" || $calculated["isLayoutHorizontalCoverLeft"] || $calculated["isLayoutHorizontalCoverRight"] );
        $calculated["isLayoutVerticalTitleTop"] = ( $calculated["cardContentLayout"] == "vertical-title-top" );
        if ( $calculated["cardContentLayout"] != "vertical-cover-top" )
            $calculated["layoutVerticalMediaQueryStart"] = ( "@media " . ($misc["layoutVertMediaQuery"] ? $misc["layoutVertMediaQuery"] : "(max-width: 1px)") . " {" );
        $calculated["isLayoutCoverAsBackground"] = ( $calculated["cardContentLayout"] == "cover-as-background" );

        $calculated["isLayoutVertical"] = ( $calculated["isLayoutVerticalCoverTop"] || $calculated["isLayoutVerticalTitleTop"] );
        $calculated["isLayoutHorizontal"] = ( $calculated["isLayoutHorizontalCoverLeft"] || $calculated["isLayoutHorizontalCoverRight"] );

		// Calculated properties for vertical layout

		$calculated["verticalImageHeight"] = $card["height"] * $card["image"]["percentSize"] / 100.0;
        if ( 
            ( 
                isset( $misc["cardLayoutCardArrangement"] ) && 
                ( $misc["cardLayoutCardArrangement"] == "same-height" || $misc["cardLayoutCardArrangement"] == "slideshow" ) 
            ) &&
            ( $calculated["cardContentLayout"] == "vertical-cover-top" || $calculated["cardContentLayout"] == "vertical-title-top" )
        ) {
            $calculated["verticalImageHeight"] = $card["height"] - $card["txtBlock"]["height"] - $card["txtBlock"]["margins"]["top"] - $card["txtBlock"]["margins"]["bottom"];
            if ( $calculated["verticalImageHeight"] < 0 ) {
                $calculated["verticalImageHeight"] = 0;
            }
        }
        
		$calculated["verticalTitleHeight"] = $card["height"] * 0.3 * ( 100 - $card["image"]["percentSize"] ) / 100.0;

		$calculated["verticalContentsHeight"] = $card["height"] * 0.7 * ( 100 - $card["image"]["percentSize"] ) / 100.0;

		$result = array_merge( $misc, $calculated );
		return $result;

	}



	function getCalculatedData( $bp ) {

		$calculated = array();

		$calculated["currentPath"] = "../";
		$calculated["id"] = $bp["id"];
		$calculated["isHighlighted"] = false;
        if ( $bp["isHighlighted"] != null ) {
            $calculated["isHighlighted"] = $bp["isHighlighted"];
        }

		// Calculated values for the cover

		$calculated["cover"] = $bp["cover"];
		$calculated["coverAlt"] = $bp["coverAlt"];
		$calculated["coverTitle"] = $bp["coverTitle"];
		$calculated["cardCover"] = $bp["cardCover"];
		$calculated["cardCoverSpecified"] = $bp["cardCover"] !== "";

		// Calculated values for the title

		$calculated["title"] = $bp["title"];
		$calculated["summary"] = $bp["summary"];
		$calculated["relativeUrl"] = $bp["rel_url"];

		// Calculated values for the contents

		$calculated["category"] = $bp["category"];
		$calculated["escapedCategory"] = str_replace(" ", "_", $bp["category"]);
		$calculated["author"] = $bp["author"];
		$calculated["escapedAuthor"] = str_replace(" ", "_", $bp["author"]);
		$calculated["timestamp"] = $bp["timestamp"];
        $calculated["readTime"] = $bp["readTime"];

		return $calculated;

	}



    function getCalculatedDataMulti( $card, $bps, $strings, $misc ){

		$calculated = array();

		$bpsKeys = array_keys( $bps );
		for ( $i=0 ; $i<count($bpsKeys) ; $i++ ) {

			$bp = $bps[$bpsKeys[$i]];

			array_push( $calculated , $this->getCalculatedData( $bp ) );

		}

		return $calculated;

	}



	function blogAllInOne( $templateElaborationData, $specifiedDir ){

		$templatesDir = "./../Templates/";
		if ( isset( $specifiedDir ) ) {
			$templatesDir = $specifiedDir;
		}

        $partialsDir = $templatesDir;
        $partialsLoader = new FilesystemLoader($partialsDir,
            [
                "extension" => ".hbs"
            ]
        );
        $handlebars = new Handlebars([
            "loader" => $partialsLoader,
            "partials_loader" => $partialsLoader
        ]);
        
        $compiledTemplateStyle = $handlebars->render( "all-style", $templateElaborationData);

        $compiledTemplateElem = $handlebars->render( "all", $templateElaborationData);

        return "<style>" . $compiledTemplateStyle . "</style>" . $compiledTemplateElem;
        
	}



	function customCardGetHtmlStartEnd( $postIds ) {

		$l10nStrings = $this->customCardL10nStrings();

        global $imSettings;
		$cardStyle = $imSettings['blog']['card_style'];
		$card = $cardStyle["card"];

		// conversion of alpha from (0-255) to (0-1)
		$card["backgroundColor"]["a"] /= 255.0;
		$card["txtBlock"]["button"]["style"]["backgroundColor"]["a"] /= 255.0;

        // conversion of style-only to (style,weight) font properties
        // if unset set it to "normal"
        if ( !isset( $card["txtBlock"]["name"]["style"]["font"]["weight"] ) ) {
            $card["txtBlock"]["name"]["style"]["font"]["weight"] = "normal";
        }
        if ( !isset( $card["txtBlock"]["description"]["style"]["font"]["weight"] ) ) {
            $card["txtBlock"]["description"]["style"]["font"]["weight"] = "normal";
        }
        if ( !isset( $card["txtBlock"]["details"]["style"]["font"]["weight"] ) ) {
            $card["txtBlock"]["details"]["style"]["font"]["weight"] = "normal";
        }
        if ( !isset( $card["txtBlock"]["button"]["style"]["font"]["weight"] ) ) {
            $card["txtBlock"]["button"]["style"]["font"]["weight"] = "normal";
        }
        // if set to "regular" use "normal" instead
        if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "regular" ) {
            $card["txtBlock"]["name"]["style"]["font"]["style"] = "normal";
        }
        if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "regular" ) {
            $card["txtBlock"]["description"]["style"]["font"]["style"] = "normal";
        }
        if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "regular" ) {
            $card["txtBlock"]["details"]["style"]["font"]["style"] = "normal";
        }
        if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "regular" ) {
            $card["txtBlock"]["button"]["style"]["font"]["style"] = "normal";
        }
        // "style" cannot be "bold", use "bold" for "weight" and set "style" to "normal"
        if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "bold" ) {
            $card["txtBlock"]["name"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["name"]["style"]["font"]["style"] = "normal";
        }
        if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "bold" ) {
            $card["txtBlock"]["description"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["description"]["style"]["font"]["style"] = "normal";
        }
        if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "bold" ) {
            $card["txtBlock"]["details"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["details"]["style"]["font"]["style"] = "normal";
        }
        if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "bold" ) {
            $card["txtBlock"]["button"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["button"]["style"]["font"]["style"] = "normal";
        }
        // "style" "bold, italic" need to be splitted in "weight" "bold" and "style" "italic"
        if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "bold, italic" ) {
            $card["txtBlock"]["name"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["name"]["style"]["font"]["style"] = "italic";
        }
        if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "bold, italic" ) {
            $card["txtBlock"]["description"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["description"]["style"]["font"]["style"] = "italic";
        }
        if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "bold, italic" ) {
            $card["txtBlock"]["details"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["details"]["style"]["font"]["style"] = "italic";
        }
        if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "bold, italic" ) {
            $card["txtBlock"]["button"]["style"]["font"]["weight"] = "bold";
            $card["txtBlock"]["button"]["style"]["font"]["style"] = "italic";
        }

		$misc = $this->customCardMisc( "#imBlogContent .blog-cardlayout-wrapper", $cardStyle );

		$miscGlobal = $this->getCalculatedGlobalData( $card, $misc );

		$blogPostsData = array();
        $highlightEnabled = $imSettings['blog']['card_style']['highlight']['mode'] != "none";
		for ( $i = 0 ; $i < count($postIds) ; $i++) {
			$blogPostItemId = $postIds[$i];
			$blogPostItem = $imSettings['blog']['posts'][$blogPostItemId];
            if ( $highlightEnabled && $i < $imSettings['blog']['card_style']['highlight']['count'] ){
                $blogPostItem['isHighlighted'] = true;
            }
			array_push( $blogPostsData , $blogPostItem );
		}

		$postsCalculated = $this->getCalculatedDataMulti(
			$card,
			$blogPostsData,
			$l10nStrings,
			$misc
		);

		$templateElaborationData = array(
			"l10n" => $l10nStrings,
			"card" => $card,
			"misc" => $miscGlobal,
			"bps" => $postsCalculated
		);

		$customCardTemplatesDir = "../res/cardtemplates/blog/";

		$resultHtml = $this->blogAllInOne( 
			$templateElaborationData,
			$customCardTemplatesDir
		);

		return "<div class=\"blog-cardlayout-wrapper\">" . $resultHtml . "</div>";

	}



    /**
     * Show a list of posts
     *
     * @param string  $posts  Array of posts
     * @param integer $start  First post to show
     * @param integer $length Total posts to be shown
     * @param integer $count  Total posts into array
     *
     * @return void
     */
    function showPosts($posts, $start, $length, $count)
    {
        global $imSettings;
        $end = ($count < $start + $length ? $count: $start + $length);
        if ($start >= $count || $end <= $start) return;

        $urlData = $this->parseUrlArray(@$_GET);
        $isBlogHome = !isset($urlData['category']) && !isset($urlData['author']) && !isset($urlData['tag']) && !isset($urlData['month']) && !isset($urlData['search']) ? true : false;

        //if ($isBlogHome && isset($imSettings['blog']['highlight_mode'] ) && $imSettings['blog']['highlight_mode'] == 'card') {
        if ( 
            $isBlogHome && 
            isset($imSettings['blog']['card_style']['highlight'] ) && 
            isset($imSettings['blog']['card_style']['highlight']['mode'] ) && 
            $imSettings['blog']['card_style']['highlight']['mode'] == 'card'
        ) {
            // Highlighted posts as card - if more than max posts per page, they'll appears also in next pages...
            echo "<div class=\"imBlogHighlightedCards\">";

            $l10nStrings = $this->customCardL10nStrings();
            global $imSettings;
            $cardStyle = $imSettings['blog']['card_style'];
            $card = $cardStyle["card"];
            $cardStyle['cardsPerRow'] = 1;
            $card["height"] = $imSettings['blog']['card_style']['highlight']['height'];
            // conversion of alpha from (0-255) to (0-1)
            $card["backgroundColor"]["a"] /= 255.0;
            $card["txtBlock"]["button"]["style"]["backgroundColor"]["a"] /= 255.0;
            // conversion of style-only to (style,weight) font properties
            // if unset set it to "normal"
            if ( !isset( $card["txtBlock"]["name"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["name"]["style"]["font"]["weight"] = "normal";
            }
            if ( !isset( $card["txtBlock"]["description"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["description"]["style"]["font"]["weight"] = "normal";
            }
            if ( !isset( $card["txtBlock"]["details"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["details"]["style"]["font"]["weight"] = "normal";
            }
            if ( !isset( $card["txtBlock"]["button"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["button"]["style"]["font"]["weight"] = "normal";
            }
            // if set to "regular" use "normal" instead
            if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["name"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["description"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["details"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["button"]["style"]["font"]["style"] = "normal";
            }
            // "style" cannot be "bold", use "bold" for "weight" and set "style" to "normal"
            if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["name"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["name"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["description"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["description"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["details"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["details"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["button"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["button"]["style"]["font"]["style"] = "normal";
            }
            // "style" "bold, italic" need to be splitted in "weight" "bold" and "style" "italic"
            if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["name"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["name"]["style"]["font"]["style"] = "italic";
            }
            if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["description"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["description"]["style"]["font"]["style"] = "italic";
            }
            if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["details"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["details"]["style"]["font"]["style"] = "italic";
            }
            if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["button"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["button"]["style"]["font"]["style"] = "italic";
            }
            $misc = $this->customCardMisc( "#imBlogContent .imBlogHighlightedCards", $cardStyle );
            $misc['cardLayoutCardArrangement'] = "same-height";
            $miscGlobal = $this->getCalculatedGlobalData( $card, $misc );    
            $blogPostsData = array();
            for ($i = $start; $i < min($imSettings['blog']['card_style']['highlight']['count'], $end); $i++) {                
                $blogPostItem = $imSettings['blog']['posts'][$posts[$i]['id']];
                global $imSettings;
                if ( $i < $imSettings['blog']['card_style']['highlight']['count'] ){
                    $blogPostItem['isHighlighted'] = true;
                }
                array_push( $blogPostsData , $blogPostItem );
                $start += 1;
            }
            $postsCalculated = $this->getCalculatedDataMulti(
                $card,
                $blogPostsData,
                $l10nStrings,
                $misc
            );
            $templateElaborationData = array(
                "l10n" => $l10nStrings,
                "card" => $card,
                "misc" => $miscGlobal,
                "bps" => $postsCalculated
            );
            $customCardTemplatesDir = "../res/cardtemplates/blog/";
            $resultHtml = $this->blogAllInOne( 
                $templateElaborationData,
                $customCardTemplatesDir
            );
            echo $resultHtml;

            echo "</div>";
        }
        else if (
            $isBlogHome && 
            isset($imSettings['blog']['card_style']['highlight'] ) && 
            isset($imSettings['blog']['card_style']['highlight']['mode'] ) && 
            $imSettings['blog']['card_style']['highlight']['mode'] == 'slideshow' && 
            $start == 0
        ) {
            // Highlighted posts as slideshow - they are repeated also as cards
            echo "<div class=\"imBlogHighlightedCards slideshow\">";

            $l10nStrings = $this->customCardL10nStrings();
            global $imSettings;
            $cardStyle = $imSettings['blog']['card_style'];
            $card = $cardStyle["card"];
            $cardStyle['cardsPerRow'] = 1;
            $card["type"] = "coverasbackground";
            $card["image"]["mouseovereffect"] = NULL;
            $card["height"] = $imSettings['blog']['card_style']['highlight']['height'];
            $card["txtBlock"]["margins"]["bottom"] = 0;
            $card["txtBlock"]["margins"]["left"] = 0;
            $card["txtBlock"]["margins"]["right"] = 0;
            $card["txtBlock"]["margins"]["top"] = 0;
            $card["txtBlock"]["name"]["show"] = true;
            $card["txtBlock"]["button"]["show"] = false;
            $card["txtBlock"]["description"]["show"] = false;
            $card["txtBlock"]["details"]["showAuthor"] = false;
            $card["txtBlock"]["details"]["showCategory"] = false;
            $card["txtBlock"]["details"]["showDate"] = false;
            $card["txtBlock"]["details"]["showReadTime"] = false;
            $card["backgroundColor"]["r"] = 0;
            $card["backgroundColor"]["g"] = 0;
            $card["backgroundColor"]["b"] = 0;
            $card["backgroundColor"]["a"] = 150;
            $card["txtBlock"]["name"]["style"]["textColor"]["r"] = 255;
            $card["txtBlock"]["name"]["style"]["textColor"]["g"] = 255;
            $card["txtBlock"]["name"]["style"]["textColor"]["b"] = 255;
            $card["txtBlock"]["height"] = $card["txtBlock"]["name"]["style"]["font"]["size"] * 1.4;
            // conversion of alpha from (0-255) to (0-1)
            $card["backgroundColor"]["a"] /= 255.0;
            $card["txtBlock"]["button"]["style"]["backgroundColor"]["a"] /= 255.0;
            // conversion of style-only to (style,weight) font properties
            // if unset set it to "normal"
            if ( !isset( $card["txtBlock"]["name"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["name"]["style"]["font"]["weight"] = "normal";
            }
            if ( !isset( $card["txtBlock"]["description"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["description"]["style"]["font"]["weight"] = "normal";
            }
            if ( !isset( $card["txtBlock"]["details"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["details"]["style"]["font"]["weight"] = "normal";
            }
            if ( !isset( $card["txtBlock"]["button"]["style"]["font"]["weight"] ) ) {
                $card["txtBlock"]["button"]["style"]["font"]["weight"] = "normal";
            }
            // if set to "regular" use "normal" instead
            if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["name"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["description"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["details"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "regular" ) {
                $card["txtBlock"]["button"]["style"]["font"]["style"] = "normal";
            }
            // "style" cannot be "bold", use "bold" for "weight" and set "style" to "normal"
            if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["name"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["name"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["description"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["description"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["details"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["details"]["style"]["font"]["style"] = "normal";
            }
            if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "bold" ) {
                $card["txtBlock"]["button"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["button"]["style"]["font"]["style"] = "normal";
            }
            // "style" "bold, italic" need to be splitted in "weight" "bold" and "style" "italic"
            if ( $card["txtBlock"]["name"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["name"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["name"]["style"]["font"]["style"] = "italic";
            }
            if ( $card["txtBlock"]["description"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["description"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["description"]["style"]["font"]["style"] = "italic";
            }
            if ( $card["txtBlock"]["details"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["details"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["details"]["style"]["font"]["style"] = "italic";
            }
            if ( $card["txtBlock"]["button"]["style"]["font"]["style"] == "bold, italic" ) {
                $card["txtBlock"]["button"]["style"]["font"]["weight"] = "bold";
                $card["txtBlock"]["button"]["style"]["font"]["style"] = "italic";
            }
            $misc = $this->customCardMisc( "#imBlogContent .imBlogHighlightedCards", $cardStyle );
            $misc['cardLayoutCardArrangement'] = "slideshow";
            $misc['cardContentLayout'] = "cover-as-background";
            $miscGlobal = $this->getCalculatedGlobalData( $card, $misc );    
            $blogPostsData = array();
            for ($i = 0; $i < min($imSettings['blog']['card_style']['highlight']['count'], $count); $i++) {
                $blogPostItem = $imSettings['blog']['posts'][$posts[$i]['id']];
                global $imSettings;
                if ( $i < $imSettings['blog']['card_style']['highlight']['count'] ){
                    $blogPostItem['isHighlighted'] = true;
                }
                array_push( $blogPostsData , $blogPostItem );
            }
            $postsCalculated = $this->getCalculatedDataMulti(
                $card,
                $blogPostsData,
                $l10nStrings,
                $misc
            );
            $templateElaborationData = array(
                "l10n" => $l10nStrings,
                "card" => $card,
                "misc" => $miscGlobal,
                "bps" => $postsCalculated
            );
            $customCardTemplatesDir = "../res/cardtemplates/blog/";
            $resultHtml = $this->blogAllInOne( 
                $templateElaborationData,
                $customCardTemplatesDir
            );
            echo $resultHtml;
            if ($imSettings['blog']['card_style']['highlight']['count'] > 1) {
                echo <<<END
                    <script>
                        x5engine.boot.push(
                            function () { 
                                const swiper = new Swiper( '#imBlogContent .imBlogHighlightedCards .swiper', {
                                    a11y: {
                                        prevSlideMessage: x5engine.l10n.get('blog_post_prev', 'Previous blog post'),
                                        nextSlideMessage: x5engine.l10n.get('blog_post_next', 'Next blog post')
                                    },
                                    direction: 'horizontal',
                                    loop: true,
                                    autoplay: true,
                                    slidesPerView: 1,
                                    pagination: {
                                        el: '#imBlogContent .imBlogHighlightedCards .swiper-pagination',
                                    },
                                    navigation: {
                                        nextEl: '#imBlogContent .imBlogHighlightedCards .swiper-button-next',
                                        prevEl: '#imBlogContent .imBlogHighlightedCards .swiper-button-prev',
                                    },
                                    scrollbar: {
                                        el: '#imBlogContent .imBlogHighlightedCards .swiper-scrollbar',
                                    },
                                });
                            }
                        );
                    </script>
END;
            }

            echo "</div>";
        }

        //Loading posts 
        $postIds = array();
        for ($i = $start; $i < $end; $i++) {
            array_push( $postIds , $posts[$i]['id'] );
        }
        $resultHtml = $this->customCardGetHtmlStartEnd( $postIds );
        echo( $resultHtml );



        echo "<script>\n";
        echo "\tx5engine.boot.push(function() {\n";
        echo "\t\tif (!x5engine.responsive.isMobileDevice()) {\n";
        echo "\t\t\tvar currentBrowser = x5engine.utils.getCurrentBrowser();\n";
        echo "\t\t\t$('.imBlogPostCardDescription').css({'overflow': 'hidden'});\n";
        echo "\t\t\t$('.imBlogPostCardDescription').hover(function() {\n";
        echo "\t\t\t\t$(this).css(\"overflow\", \"auto\");\n";
        echo "\t\t\t\t$(this).children('.imBlogPostCardDescriptionFade').hide();\n";
        echo "\t\t\t}, function() {\n";
        echo "\t\t\t\t$(this).css(\"overflow\", \"hidden\");\n";
        echo "\t\t\t\t$(this).children('.imBlogPostCardDescriptionFade').show();\n";
        echo "\t\t\t});\n";
        echo "\t\t\tif (currentBrowser == \"Microsoft Edge\" || currentBrowser == \"Microsoft Internet Explorer\") {\n";
        echo "\t\t\t\t$('.imBlogPostCardDescription').hover(function() {\n";
        echo "\t\t\t\t\t$(this).css(\"overflow-y\", \"auto\");\n";
        echo "\t\t\t\t}, function() {\n";
        echo "\t\t\t\t\t$(this).css(\"overflow-y\", \"hidden\");\n";
        echo "\t\t\t\t});\n";
        echo "\t\t\t\t$('.imBlogPostCardDescription .imBlogPostCardDescriptionFade').remove();\n";
        echo "\t\t\t}\n";
        echo "\t\t}\n";
        echo "\t\telse {\n";
        echo "\t\t\t$('.imBlogPostCardDescription .imBlogPostCardDescriptionFade').remove();\n";
        echo "\t\t}";
        echo "\t});\n";
        echo "</script>\n";
    }

    /**
     * Find the posts tagged with tag
     *
     * @param string $tag The searched tag
     *
     * @return void
     */
    function showTag($tag)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $imSettings['blog']['home_posts_number'];
        $count = $this->getTagPostCount($tag);

        if ($count == 0)
            return;
        $bps = array_values($this->getTagPosts($tag));
        $this->showPosts($bps, $start, $length, $count);
        $this->paginate("?tag=" . $tag . "&", $start, $length, $count);
    }

    /**
     * Find the post in a category
     *
     * @param string $category the category ID
     *
     * @return void
     */
    function showCategory($category)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $imSettings['blog']['home_posts_number'];
        $bps = array_values($this->getCategoryPosts($category));
        $count = count($bps);

        $this->showPosts($bps, $start, $length, $count);
        $this->paginate("?category=" . urlencode(str_replace(' ', '_', $category)) . "&", $start, $length, $count);
    }

    /**
     * Find the posts by an author
     *
     * @param string $author the author ID
     *
     * @return void
     */
    function showAuthor($author)
    {
        global $imSettings;
        $isAllAuthorsPage = $author == "|All|" ? true : false;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $imSettings['blog']['home_posts_number'];
        $count = $isAllAuthorsPage ? $this->getPostsCount() : $this->getAuthorPostCount($author);

        $bps = $isAllAuthorsPage ? array_values($this->getPosts()) : array_values($this->getAuthorPosts($author));
        $this->showPosts($bps, $start, $length, $count);
        $this->paginate("?author=" . urlencode(str_replace(' ', '_', $author)) . "&", $start, $length, $count);
    }

    /**
     * Find the posts of the month
     *
     * @param string $month The mont
     *
     * @return void
     */
    function showMonth($month)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $imSettings['blog']['home_posts_number'];
        $count = $this->getMonthPostsCount($month);

        $bps = array_values($this->getMonthPosts($month));
        $this->showPosts($bps, $start, $length, $count);
        $this->paginate("?month=" . $month . "&", $start, $length, $count);
    }

    /**
     * Show the last n posts
     *
     * @param integer $count the number of posts to show
     *
     * @return void
     */
    function showLast($count)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $imSettings['blog']['home_posts_number'];
        $bpsc = $this->getPostsCount();

        $bps = array_values($this->getPosts());
        $this->showPosts($bps, $start, $length, $bpsc);
        $this->paginate("?", $start, $length, $bpsc);
    }

    function getSearchPosts($search)
    {
        $queries = preg_split("/\s+/", trim(imstrtolower($search)));
        $weights = array();
        foreach ($this->getPosts() as $id => $value) {
            $weight = 0;
            foreach ($queries as $query) {
                $queryRegex = '/' . preg_quote($query, '/') . '/';
                // Conto il numero di match nei titoli
                if (($t_count = preg_match_all($queryRegex, imstrtolower($value['title']), $matches))) {
                    $weight += ($t_count * 3);
                }
                // tag_description
                if (preg_match($queryRegex, $value['summary']) === 1) {
                    $weight += 2;
                }
                // keywords
                if (preg_match($queryRegex, $value['keywords']) === 1) {
                    $weight += 2;
                }
                // Conto il numero di match nei tag
                if (in_array($query, $value['tag'])) {
                    $weight += 4;
                }
                // Conto occorrenze nel contenuto
                if (($t_count = preg_match_all($queryRegex, imstrtolower(strip_tags($value['body'])), $matches))) {
                    $weight += $t_count;
                }
            }
            if ($weight > 0) {
                if (!isset($weights[$weight])) {
                    $weights[$weight] = array();
                }
                $weights[$weight][$id] = $value;
            }
        }
        krsort($weights);
        $posts = array();
        foreach ($weights as $p) {
            $posts = array_merge($posts, $p);
        }
        return $posts;
    }

    /**
     * Show the search results
     *
     * @param string  $search the search query
     *
     * @return void
     */
    function showSearch($search)
    {
        global $imSettings;
        $start = isset($_GET['start']) ? max(0, (int)$_GET['start']) : 0;
        $length = isset($_GET['length']) ? (int)$_GET['length'] : $imSettings['blog']['home_posts_number'];

        $results = array_values($this->getSearchPosts($search));
        $count = count($results);
        if ($count > 0) {
            $this->showPosts($results, $start, $length, $count);
            $this->paginate("?search=" . $search . "&", $start, $length, $count);
        } else {
            echo "<div class=\"imBlogEmpty\">" . l10n('search_empty') . "</div>";
        }
    }

    /**
     * Show the categories sideblock
     *
     * @param integer $n The number of categories to show
     *
     * @return void
     */
    function showBlockCategories($n)
    {
        global $imSettings;

        if (is_array($imSettings['blog']['posts_cat'])) {
            $categories = array();
            foreach ($this->getPosts() as $id => $post) {
                if (!in_array($post['category'], $categories)) {
                    $categories[] = $post['category'];
                }
            }
            sort($categories);
            echo "<ul>";
            for ($i = 0; $i < count($categories) && $i < $n; $i++) {
                if ($categories[$i] != "") {
                    echo "<li><a href=\"?category=" . urlencode(str_replace(' ', '_', $categories[$i])) . "\">" . $categories[$i] . "</a></li>";
                }
            }
            echo "<li><a href=\"?category=|All|\">" . l10n("blog_all_categories", "All categories") . "</a></li>";
            echo "</ul>";
        }
    }

    /**
     * Show the authors sideblock
     *
     * @param integer $n The number of authors to show
     *
     * @return void
     */
    function showBlockAuthors($n)
    {
        global $imSettings;

        if (is_array($imSettings['blog']['posts_author'])) {
            $authors = array();
            foreach ($this->getPosts() as $id => $post) {
                if (!in_array($post['author'], $authors)) {
                    $authors[] = $post['author'];
                }
            }
            sort($authors);
            echo "<ul>";
            for ($i = 0; $i < count($authors) && $i < $n; $i++) {
                if ($authors[$i] != "") {
                    echo "<li><a href=\"?author=" . urlencode(str_replace(' ', '_', $authors[$i])) . "\">" . $authors[$i] . "</a></li>";
                }
            }
            echo "<li><a href=\"?author=|All|\">" . l10n("blog_all_authors", "All authors") . "</a></li>";
            echo "</ul>";
        }
    }

    /**
     * Show the cloud sideblock
     *
     * @param string $type TAGS or CATEGORY
     *
     * @return void;
     */
    function showBlockCloud($type)
    {
        global $imSettings;

        $max = 0;
        $min_em = 0.95;
        $max_em = 1.25;
        if ($type == "tags") {
            $tags = array();
            foreach ($this->getPosts() as $id => $post) {
                foreach ($post['tag'] as $tag) {
                    if (!isset($tags[$tag]))
                        $tags[$tag] = 1;
                    else
                        $tags[$tag] = $tags[$tag] + 1;
                    if ($tags[$tag] > $max)
                        $max = $tags[$tag];
                }
            }
            if (count($tags) == 0)
                return;

            $tags = shuffleAssoc($tags);

            foreach ($tags as $name => $number) {
                $size = number_format(($number/$max * ($max_em - $min_em)) + $min_em, 2, '.', '');
                echo "\t\t\t<span class=\"imBlogCloudItem\" style=\"font-size: " . $size . "em;\">\n";
                echo "\t\t\t\t<a href=\"?tag=" . urlencode($name) . "\" style=\"font-size: " . $size . "em;\">" . $name . "</a>\n";
                echo "\t\t\t</span>\n";
            }
        } else if ($type == "categories") {
            $categories = array();
            foreach ($this->getPosts() as $id => $post) {
                if ($post['category'] == "") {
                    continue;
                }
                if (!isset($categories[$post['category']]))
                    $categories[$post['category']] = 1;
                else
                    $categories[$post['category']] = $categories[$post['category']] + 1;
                if ($categories[$post['category']] > $max)
                    $max = $categories[$post['category']];
            }
            if (count($categories) == 0)
                return;

            $categories = shuffleAssoc($categories);

            foreach ($categories as $name => $number) {
                $size = number_format(($number/$max * ($max_em - $min_em)) + $min_em, 2, '.', '');
                echo "\t\t\t<span class=\"imBlogCloudItem\" style=\"font-size: " . $size . "em;\">\n";
                echo "\t\t\t\t<a href=\"?category=" . urlencode(str_replace(' ', '_', $name)) . "\" style=\"font-size: " . $size . "em;\">" . $name . "</a>\n";
                echo "\t\t\t</span>\n";
            }
        }
    }

    /**
     * Show the month sideblock
     *
     * @param integer $n Number of entries
     *
     * @return void
     */
    function showBlockMonths($n)
    {
        global $imSettings;

        if (is_array($imSettings['blog']['posts_month'])) {
            $months = array();
            foreach ($this->getPosts() as $id => $post) {
                if (!in_array($post['month'], $months)) {
                    $months[] = $post['month'];
                }
            }
            rsort($months);
            echo "<ul>";
            for ($i = 0; $i < count($months) && $i < $n; $i++) {
                $formattedDate = strlen($months[$i]) == 6 ? formatDate(DateTimeImmutable::createFromFormat("d/m/Y", "01/" . substr($months[$i], 4, 2) . "/" . substr($months[$i], 0, 4)), false, false) : $months[$i];
                echo "<li><a href=\"?month=" . urlencode($months[$i]) . "\">" . $formattedDate . "</a></li>";
            }
            echo "</ul>";
        }
    }

    /**
     * Show the last posts block
     *
     * @param integer $n The number of post to show
     *
     * @return void
     */
    function showBlockLast($n)
    {
        global $imSettings;

        $posts = array_values($this->getPosts());
        if (is_array($posts)) {
            echo "<ul>";
            for ($i = 0; $i < count($posts) && $i < $n; $i++) {
                echo "<li><a href=\"" . $posts[$i]['rel_url'] . "\">" . $posts[$i]['title'] . "</a></li>";
            }
            echo "</ul>";
        }
    }
}




/**
 * Create the required instanced basing on the configuration setup by the user
 */
class Configuration
{

    static private $analytics = false;
    static private $blog = false;
    static private $cart = false;
    static private $controlPanel = false;
    static private $privateArea = false;
    static private $l10n = false;

    static public function getAnalytics()
    {
        global $imSettings;

        if (!isset($imSettings['analytics']) || $imSettings['analytics']['type'] != 'wsx5analytics') {
            return null;
        }

        if (self::$analytics == false) {
            $prefix = $imSettings['analytics']['database']['table'];
            $dbconf = getDbData($imSettings['analytics']['database']['id']);
            self::$analytics =  new Analytics(ImDb::from_db_data($dbconf), $prefix);
        }

        return self::$analytics;
    }

    static public function getBlog()
    {
        if (self::$blog == false) {
            self::$blog = new imBlog();
        }
        return self::$blog;
    }

    static public function getCart()
    {
        global $imSettings;

        if (self::$cart == false) {
            self::$cart = new ImCart();
        }

        return self::$cart;
    }

    static public function getControlPanel()
    {
        global $imSettings;

        $icon = "";
        if (isset($imSettings['admin']['icon'])) {
            $icon = $imSettings['admin']['icon'];
        }
        else if (isset($imSettings['general']['icon'])) {
            $icon = $imSettings['general']['icon'];
        }
        // Try to transform the url to a relative one
        $icon = str_replace($imSettings['general']['url'], "", $icon);
        // Prepend the logo icon with the correct path to root if it's not absolute
        if (strlen($icon) && substr($icon, 0, 7) != "http://" && substr($icon, 0, 8) != "https://") {
            $icon = "../" . trim($icon, "/");
        }

        if (self::$controlPanel == false) {
            self::$controlPanel = new ControlPanel(
                isset($imSettings['general']['sitename']) ? $imSettings['general']['sitename'] : "",
                $imSettings['general']['url'],
                $icon,
                isset($imSettings['admin']['theme']) ? $imSettings['admin']['theme'] : "orange"
            );
        }

        return self::$controlPanel;
    }

    static public function getPrivateArea()
    {
        global $imSettings;

        if (!self::$privateArea) {
            self::$privateArea = new imPrivateArea();
            if (isset($imSettings['access']['dbid'])) {
                $db = getDbData($imSettings['access']['dbid']);
                self::$privateArea->setDbData(ImDb::from_db_data($db), $imSettings['access']['dbtable'], $imSettings['access']['datadbtable']);
            }
        }
        return self::$privateArea;
    }

    /**
     * Load a dynamic object starting from its ID
     * @param  String $id        The dynamic object id
     * @return DynamicObject     The dynamic object
     */
    static public function getDynamicObject($id)
    {
        global $imSettings;

        $data = false;

        if (isset($imSettings['dynamicobjects']['pages'][$id])) {
            $data = $imSettings['dynamicobjects']['pages'][$id];
        } else if (isset($imSettings['dynamicobjects']['template'][$id])) {
            $data = $imSettings['dynamicobjects']['template'][$id];
        }

        if (!is_array($data)) {
            return null;
        }

        $dynObj = new DynamicObject($id);
        $dynObj->setDefaultText(str_replace(array("\n", "\r"), array("<br />", ""), $data['defaultContent']));

        if (isset($data['dbid'])) {
            $db = getDbData($data['dbid']);
            $dynObj->loadFromDb(ImDb::from_db_data($db), $data['dbtable']);
        } else if (isset($data['subfolder'])) {
            $dynObj->loadFromFile(pathCombine(array($imSettings['general']['public_folder'], $data['subfolder'])));
        }

        return $dynObj;
    }


    /**
     * Get the localization object
     *
     * @return L10n
     */
    static public function getLocalizations()
    {
        global $l10n;

        if (self::$l10n === false) {
            self::$l10n = new L10n($l10n);
        }

        return self::$l10n;
    }

    /**
     * Get the configuration array
     */
    static public function getSettings()
    {
        global $imSettings;

        return $imSettings;
    }
}


function im_cookie_name($key)
{
    $settings = Configuration::getSettings()['general'];
    return isset($settings['site_id']) ? $settings['site_id'] . '_' . $key : $key;
}

function im_set_cookie($key, $value = '', $expire = 0, $path = '')
{
    setcookie(im_cookie_name($key), $value, $expire, $path);
}

function im_get_cookie($key)
{
    $cookie_name = im_cookie_name($key);
    return isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : null;
}

function im_check_cookie($key, $value_to_check)
{
    $cookie_name = im_cookie_name($key);
    return isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == $value_to_check;
}



/**
 * x5Captcha handling class
 * @access public
 */
class X5Captcha {

    private $nameList;
    private $charList;

    /**
     * Build a new captcha class
     * @param {Array} $nameList
     * @param {Array} $charList
     */
    function __construct($nameList, $charList) {
        $this->nameList = $nameList;
        $this->charList = $charList;
    }

    /**
     * Show the captcha chars
     */
    function show($sCode)
    {
        $text = "<!DOCTYPE HTML>
            <html>
          <head>
          <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
          <meta http-equiv=\"pragma\" content=\"no-cache\">
          <meta http-equiv=\"cache-control\" content=\"no-cache, must-revalidate\">
          <meta http-equiv=\"expires\" content=\"0\">
          <meta http-equiv=\"last-modified\" content=\"\">
          </head>
          <body style=\"margin: 0; padding: 0; border-collapse: collapse;\">";

        for ($i = 0; $i < strlen($sCode); $i++) {
            $text .= "<img style=\"margin:0; padding:0; border: 0; border-collapse: collapse; width: 32px; height: 32px; position: absolute; top: 0; left: " . (32 * $i) . "px;\" src=\"imcpa_".$this->nameList[substr($sCode, $i, 1)].".gif\" alt=\"\" width=\"32\" height=\"32\">";
        }

        $text .= "</body></html>";

        return $text;
    }

    /**
     * Check the sent data
     * @param {String} code The correct code
     * @param {String} ans  The user's answer
     */
    function check($code, $ans)
    {
        if ($ans == "") {
            return '-1';
        }
        for ($i = 0; $i < strlen($code); $i++) {
            if ($this->charList[substr(strtoupper($code), $i, 1)] != substr(strtoupper($ans), $i, 1)) {
                return '-1';
            }
        }
        return '0';
    }
}


/**
 * reCaptcha handling class
 * @access public
 */
class ReCaptcha {

    private $secretKey;

    /**
     * Build a new captcha class
     * @param {String} $secretKey
     */
    function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }

    /**
     * Check the response
     * @param $response The response to be checked
     */
    function check($response)
    {
        // Create the POST data
        $post = "secret=" . urlencode($this->secretKey) . "&response=" . urlencode($response);
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $post .= "&remoteip=" . urldecode($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        else if (isset($_SERVER['REMOTE_ADDR'])) {
            $post .= "&remoteip=" . urldecode($_SERVER['REMOTE_ADDR']);   
        }

        // Use curl instead of file_get_contents (which can be blocked)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}


class Router
{
    private $routes = array();

    public function addRoute($check, $callback)
    {
        $this->routes[] = array(
            'check' => $check,
            'callback' => $callback
        );
    }

    public function handleRoute($data)
    {
        foreach ($this->routes as $route) {
            if ($route['check']($data)) {
                $route['callback']($data);
                return;
            }
        }
    }
}






/**
 * @summary
 * Manage the comment structure of a topic. It can load and save the comments from/to a file or a database.
 * To use it, you must include __x5engine.php__ in your code.
 * 
 * This class is available only in the **Professional** and **Evolution** editions.
 *
 * @description
 * Build a new ImComment object.
 * 
 * @constructor
 */
class ImComment
{

    var $comments = array();
    var $error = 0;

    /**
     * Load the comments from an xml file
     * 
     * @param {string} $file The source file path
     *
     * @return {Void}
     */
    function loadFromXML($file)
    {
        if (!file_exists($file)) {
            $this->comments = array();
            return;
        }

        $xmlstring = @file_get_contents($file); 
        if (strpos($xmlstring, "<?xml") !== false) {
            $xml = new imXML();
            $id = 0;

            // Remove the garbage (needed to avoid loosing comments when the xml string is malformed)
            $xmlstring = preg_replace('/<([0-9]+)>.*<\/\1>/i', '', $xmlstring);
            $xmlstring = preg_replace('/<comment>\s*<\/comment>/i', '', $xmlstring);
            
            $comments = $xml->parse_string($xmlstring);
            if ($comments !== false && is_array($comments)) {
                $tc = array();
                if (!isset($comments['comment'][0]) || !is_array($comments['comment'][0]))
                    $comments['comment'] = array($comments['comment']);
                for ($i = 0; $i < count($comments['comment']); $i++) {
                    foreach ($comments['comment'][$i] as $key => $value) {
                        if ($key == "timestamp" && strpos($value, "-") == 2) {
                            // The v8 and v9 timestamp was inverted. For compatibility, let's convert it to the correct format.
                            // The v10 format is yyyy-mm-dd hh:ii:ss
                            // The v8 and v9 format is dd-mm-yyyy hh:ii:ss
                            $value = preg_replace("/([0-9]{2})\-([0-9]{2})\-([0-9]{4}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/", "$3-$2-$1 $4:$5:$6", $value);
                        }
                        $tc[$i][$key] = str_replace(array("\\'", '\\"'), array("'", '"'), htmlspecialchars_decode($value, ENT_COMPAT));
                        if ($key == "rating" && is_numeric($value) && intval($value) > 5) {
                            $tc[$i][$key] = "5";
                        }
                    }
                    $tc[$i]['id'] = $id++;
                }
                $this->comments = $tc;
            } else {
                // The comments cannot be retrieved. The XML is jammed.
                // Do a backup copy of the file and then reset the xml.
                // Hashed names ensure that a file is not copied more than once
                $n = $file . "_version_" . md5($xmlstring);
                if (!@file_exists($n))
                    @copy($file, $n);
                $this->comments = array();
            }
        } else {
            $this->loadFromOldFile($file);
        }
    }

    /**
     * Get the comments from a v8 comments file.
     * Use loadFromXML instead
     *
     * @see [loadFromXML](##imcommentloadfromxmlfile)
     * @deprecated
     * 
     * @param {string} $file The source file path
     *
     * @return {Void}
     */
    function loadFromOldFile($file)
    {
        if (!@file_exists($file)) {
            $this->comments = array();
            return;
        }
        $f = @file_get_contents($file);
        $f = explode("\n", $f);
        for ($i = 0;$i < count($f)-1; $i += 6) {
            $c[$i/6]['id'] = $i / 6;
            $c[$i/6]['name'] = stripslashes($f[$i]);
            $c[$i/6]['email'] = $f[$i+1];
            $c[$i/6]['url'] = $f[$i+2];
            $c[$i/6]['body'] = stripslashes($f[$i+3]);
            $c[$i/6]['timestamp'] = preg_replace("/([0-9]{2})\-([0-9]{2})\-([0-9]{4}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/", "$3-$2-$1 $4:$5:$6", $f[$i+4]);
            $c[$i/6]['approved'] = $f[$i+5];
            $c[$i/6]['rating'] = 0;
        }
        $this->comments = $c;
    }

    /**
     * Save the comments in a xml file
     * 
     * @param {string} $file The destination file path
     *
     * @return {boolean} True if the file was saved correctly
     */
    function saveToXML($file)
    {
        // If the count is 0, delete the file and exit
        if (count($this->comments) === 0) {
            if (@file_exists($file))
                @unlink($file);
            return true;
        }

        // If the folder doesn't exists, try to create it
        $dir = @dirname($file);
        if ($dir != "" && $dir != "/" && $dir != "." && $dir != "./" && !file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<comments>\n";
        $i = 0;
        foreach ($this->comments as $comment) {
            $txml = "";
            foreach ($comment as $key => $value) {
                // Well formed content only
                if (!preg_match('/[0-9]+/', $key) && in_array(gettype($value), array('string', 'integer', 'double'))) {
                    $code = str_replace(array("\\'", '\\"', "\\\""), array("'", '"', "\""), preg_replace('/[\n\r\t]*/', '', nl2br($value)));
                    $txml .= "\t\t<" . $key . "><![CDATA[" . htmlspecialchars($code) . "]]></" . $key . ">\n";
                }
            }
            if ($txml != "")
                $xml .= "\t<comment>\n" . $txml . "\t</comment>\n";
        }
        $xml .= "</comments>";

        if ((is_writable($file) || !file_exists($file))) {
            if (!$f = @fopen($file, 'w+')) {
                $this->error = -3;
                return false;
            } else {
                if (flock($f, LOCK_EX)) {
                    $locked = 1;
                }

                if (fwrite($f, $xml) === false) {
                    $this->error = -4;
                    return false;
                } else {
                    if($locked)
                        flock($f, LOCK_UN);
                    fclose($f);
                    $this->error = 0;
                    return true;
                }
            }
        } else {
            $this->error = -2;
            return false;
        }
    }


    /**
     * Add a comment to a file
     * 
     * @param {array} $comment the array of data to store
     *
     * @return {Void}
     */
    function add($comment)
    {
        foreach ($comment as $key => $value) {
            $comment[$key] = $this->filterCode($value, true);
        }
        $this->comments[] = $comment;
    }

    /**
     * Sort the array
     * 
     * @param string $orderby Field to compare when ordering the array
     * @param string $sort    Sort by ascending (asc) or descending (desc) order
     * 
     * @return void         
     */
    function sort($orderby = "", $sort = "desc")
    {
        if (count($this->comments) === 0)
            return;

        // Find where the comments has this field
        // This is useful to order using a field which is not present in every comment (like the ts field, which is missing in the stars-only vote type)
        $comment = null;
        for ($i=0; $i < count($this->comments) && $comment == null; $i++) { 
            if (isset($this->comments[$i][$orderby]))
                $comment = $this->comments[$i];
        }
        if ($comment === null)
            return;
        
        // Order the array
        $desc = strtolower($sort) == "desc";
        $compare = function ($a, $b) use ($comment, $orderby, $desc) {
            
            if (!isset($a[$orderby]) || !isset($b[$orderby]) || $a[$orderby] == $b[$orderby]) {
                return 0;
            }

            if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}/", $comment[$orderby])) {
                // The orderable field is a timestamp
                $aIsGrater = strtotime($a[$orderby]) > strtotime($b[$orderby]);
            } else if (is_numeric($comment[$orderby])) {
                // The orderable field is a number
                $aIsGrater = $a[$orderby] > $b[$orderby];
            } else if (is_string($comment[$orderby])) {
                // The orderable field is a string
                $aIsGrater = strcmp($a[$orderby], $b[$orderby]) > 0;
            } else {
                return 0;
            }

            return $desc ^ $aIsGrater ? -1 : 1;
        };

        // Sort and return
        usort($this->comments, $compare);
    }

    /**
     * Get all the comments loaded in this class
     * 
     * @param {string}  $orderby      Field to compare when ordering the array
     * @param {string}  $sort         Sort by ascending (asc) or descending (desc) order
     * @param {boolean} $approvedOnly Show only approved comments
     * 
     * @return array An array of associative arrays containing the comments data
     */
    function getAll($orderby = "", $sort = "desc", $approvedOnly = true)
    {
        if ($orderby == "" || count($this->comments) === 0)
            return $this->comments;
        
        $this->sort($orderby, $sort);
        
        if (!$approvedOnly)
            return $this->comments;

        $comments = array();
        foreach ($this->comments as $comment) {
            if (isset($comment['approved']) && $comment['approved'] == "1") {
                $comments[] = $comment;
            }
        }
        return $comments;
    }

    /**
     * Get the comments in the specified page when there is the specified number of comments in every page.
     * This is useful for pagination.
     * 
     * @param  {integer} $pageNumber      The number of page to show (0 based)
     * @param  {integer} $commentsPerPage Number of comments shown in each page
     * @param  {string}  $orderby         Field to compare when ordering the array
     * @param  {string}  $sort            Sort by ascending (asc) or descending (desc) order
     * @param  {boolean} $approvedOnly    Show only approved comments
     * 
     * @return array The list of comments in the page
     */
    function getPage($pageNumber, $commentsPerPage, $orderby = "", $sort = "desc", $approvedOnly = true) {
        $all = $this->getAll($orderby, $sort, $approvedOnly);
        // If the page number is wrong, return an empty array
        if ($pageNumber < 0 || $pageNumber > $this->getPagesNumber($commentsPerPage))
            return array();
        return array_slice($all, $pageNumber * $commentsPerPage, $commentsPerPage, false);
    }

    /**
     * Get the comment number n
     * 
     * @param {integer} $n The comment's number
     * 
     * @return {array} The comment's data or an empty array if the comment is not found
     */
    function get($n)
    {
        if (isset($this->comments[$n]))
            return $this->comments[$n];
        return array();
    }

    /**
     * Get the pages number given the number of comments per page
     * 
     * @param  {integer} $commentsPerPage Number of comments in every page
     * @param  {boolean} $approvedOnly    Show only approved comments
     * 
     * @return {integer} The number of pages
     */
    function getPagesNumber($commentsPerPage, $approvedOnly = true) {
        if (!is_array($this->comments) || !count($this->comments))
            return 0;
        if (!$approvedOnly) {
            $count = count($this->comments);
        } else {
            $count = 0;
            foreach ($this->comments as $comment) {
                if ($comment['approved'] == "1") {
                    $count++;
                }
            }
        }
        return ceil($count / $commentsPerPage);
    }

    /**
     * Edit the comment number $n with the data contained in the parameter $comment
     * 
     * @param {integer} $n       Comment number
     * @param {array}   $comment Comment data
     * 
     * @return {boolean} True if the comment was correctly edited. False instead.
     */
    function edit($n, $comment)
    {
        if (isset($this->comments[$n])) {
            $this->comments[$n] = $comment;
            return true;
        }
        return false;
    }

    /**
     * Delete the comment at $n
     * 
     * @param {integer} $n The index of the comment
     *
     * @return {Void}
     */
    function delete($n)
    {
        // Delete an element from the array and reset the indexes
        if (isset($this->comments[$n])) {
            $comments = $this->comments;
            $this->comments = array();
            for ($i = 0; $i < count($comments); $i++)
                if ($i != $n)
                    $this->comments[] = $comments[$i];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Clean the data from XSS
     * 
     * @param string  $str         The string to parse
     * @param boolean $allow_links true to allow links
     * 
     * @return string
     */
    function filterCode($str, $allow_links = false)
    {
        global $imSettings;

        if (gettype($str) != 'string')
            return "";

        // Remove javascript
        while (($start = imstripos($str, "<script")) !== false) {
            $end = imstripos($str, "</script>") + strlen("</script>");
            $str = substr($str, 0, $start) . substr($str, $end);
        }

        // Remove PHP Code
        while (($start = imstripos($str, "<?")) !== false) {
            $end = imstripos($str, "?>") + strlen("?>");
            $str = substr($str, 0, $start) . substr($str, $end);
        }

        // Remove ASP code
        while (($start = imstripos($str, "<%")) !== false) {
            $end = imstripos($str, "%>") + strlen("<%");
            $str = substr($str, 0, $start) . substr($str, $end);
        }

        // Allow only few tags
        $str = strip_tags($str, '<b><i><u>' . ($allow_links ? '<a>' : ''));
        
        // Remove XML injection code
        while (($start = imstripos($str, "<![CDATA[")) !== false) {    
            // Remove the entire XML block when possible
            if (imstripos($str, "]]>") !== false) {
                $end = imstripos($str, "]]>") + strlen("]]>");
                $str = substr($str, 0, $start) . substr($str, $end);
            } else {        
                $str = str_replace("<![CDATA[", "", str_replace("<![cdata[", "", $str));
            }
        }
        while (($start = imstripos($str, "]]>")) !== false) {
            $str = str_replace("]]>", "", $str);
        }

        // Remove all the onmouseover, onclick etc attributes
        while (($res = preg_replace("/(<[\\s\\S]+) on.*\\=(['\"\"])[\\s\\S]+\\2/i", "\\1", $str)) != $str) {
            // Exit in case of error
            if ($res == null)
                break;
            $str = $res;
        }

        $matches = array();
        preg_match_all('~<a.*>~isU', $str, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            if (imstripos($matches[0][$i], 'nofollow') === false && imstripos($matches[0][$i], $imSettings['general']['url']) === false) {
                $result = trim($matches[0][$i], ">") . ' rel="nofollow">';
                $str = str_replace(strtolower($matches[0][$i]), strtolower($result), $str);
            }
        }

        return $str;
    }

    /**
     * Provide the last error
     * 
     * @return int
     */
    function lastError()
    {
        return $this->error;
    }
}




/**
 * An interface that defines the common methods used to access the db
 */
interface DatabaseAccess
{	
    public function testConnection();
    public function closeConnection();
    public function get_db_name();
    public function createTable($name, $fields);
    public function deleteTable($table);
    public function tableExists($table);
    public function error();
    public function lastInsertId();
    public function query($query);
    public function escapeString($string);
    public function affectedRows();
}



/**
 * @summary
 * A database driver class which access to the DB using the "mysql_" functions
 * 
 * To use this class, you must include __x5engine.php__ in your code.
 * 
 * @description Create a new ImDb Object
 *
 * @ignore
 * @class
 * @constructor
 * 
 * @param {string} $host  The database host address
 * @param {string} $user  The database username
 * @param {string} $pwd   The database password
 * @param {string} $db    The database name
 */
class MySQLDriver implements DatabaseAccess
{

    var $conn;
    var $db;
    var $db_name;
    var $engine = "MYISAM";
    
    function __construct($host, $user, $pwd, $db)
    {
        $this->setUp($host, $user, $pwd, $db);
    }
    
    function ImDb($host, $user, $pwd, $db)
    {
        $this->setUp($host, $user, $pwd, $db);
    }

    function setUp($host, $user, $pwd, $db)
    {
        $this->db_name = $db;
        $this->conn = @mysql_connect($host, $user, $pwd);
        if ($this->conn === false)
            return;
        $this->db = @mysql_select_db($db, $this->conn);
        if ($this->db === false)
            return;
        if (function_exists('mysql_set_charset'))
            @mysql_set_charset("utf8", $this->conn);
        else
            @mysql_query('SET NAMES "utf8"', $this->conn);
    }

    /**
     * Check if the class is connected or not to a db
     *
     * @return {boolean} True if the class is connected to a DB. False otherwise.
     */
    function testConnection()
    {
        return ($this->conn !== false && $this->db !== false);
    }

    /**
     * Close the connection
     * 
     * @return void
     */
    function closeConnection()
    {
        @mysql_close($this->conn);
    }

    function get_db_name()
    {
        return $this->db_name;
    }

    /**
     * Create a new table or update an existing one.
     * 
     * @param string $name   The table name
     * @param array $fields  The table fields list as array of associative arrays (one array item foreach table field). must be passed as stated in the example.
     *
     * @example
     * $db->createTable('tableName', array(
     *     "field1" => array(
     *         "type" => "INTEGER",
     *         "null" => false,
     *         "auto_increment" => true,
     *         "primary" => true
     *     ),
     *     "field2" => array(
     *         "type" => "TEXT",
     *         "null" => true,
     *         "auto_increment" => false,
     *         "more" => "CHARACTER SET UTF-8"
     *     ))
     * );
     * 
     * @return boolean True if the table was created succesfully.
     */
    function createTable( $name, $fields )
    {
        $qfields = array();
        $primaries = array();
        $createResult = false;

        // If the table does not exists, create it
        if (!$this->tableExists($name)) {
            $query = "CREATE TABLE `" . $this->db_name . "`.`" . $name . "` (";
            foreach ($fields as $key => $value) {
                $qfields[] = "`" . $key . "` " .
                            $value['type'] .
                            ($value['type'] == 'TEXT' || $value['type'] == 'MEDIUMTEXT' ? " CHARACTER SET utf8 COLLATE utf8_unicode_ci" : "") .
                            (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "") .
                            (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "") .
                            (isset($value['auto_increment']) ? " AUTO_INCREMENT" : "") .
                            (isset($value['more']) ? " " . $value['more'] : "");
                if (isset($value['primary']) && $value['primary']) {
                    $primaries[] = "`" . $key . "`";
                }
            }
            $query .= implode(",", $qfields);
            if (count($primaries))
                $query .= ", PRIMARY KEY (" . implode(",", $primaries) . ")";
            $query .= ") ENGINE = " . $this->engine . " ;";
            $createResult = mysql_query($query, $this->conn);
        } else {
            $result = mysql_query("SHOW COLUMNS FROM `" . $this->db_name . "`.`" . $name . "`", $this->conn);
            // Alter table flag: if true execute the query at the end
            $alterTable = false;
            if ($result) {
                // Actual fields
                $query = "ALTER TABLE `" . $this->db_name. "`.`" . $name . "`";
                $act_fields = array();
                while ($row = mysql_fetch_array($result)) {
                    $act_fields[] = $row;
                    $act_fields_names[] = $row[0];
                }
                // New fields
                $new_fields = array_diff(array_keys($fields), $act_fields_names);
                $new_fields = array_merge($new_fields); // Order the indexes
                if (count($new_fields) > 0) {
                    foreach ($new_fields as $key) {
                        $qfields[] = " ADD `" . $key . "` " . $fields[$key]['type'] . 
                        ($fields[$key]['type'] == 'TEXT' || $fields[$key]['type'] == 'MEDIUMTEXT' ? " CHARACTER SET utf8 COLLATE utf8_unicode_ci" : "") .
                        (!isset($fields[$key]['null']) || !$fields[$key]['null'] ? " NOT NULL" : "") .
                        (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "") .
                        (isset($fields[$key]['auto_increment']) && $fields[$key]['auto_increment'] ? " AUTO_INCREMENT" : "") .
                        // WSXTWE-1215: Manage the adding/removal of a primary key
                        (isset($fields[$key]['primary']) && $fields[$key]['primary'] ? " PRIMARY KEY" : "") .
                        (isset($fields[$key]['more']) ? " " . $fields[$key]['more'] : "");
                    }
                    $alterTable = true;
                }
                // Check if it's necessary to do some changes on actual fields: if yes, consider them in the alter query
                foreach ($act_fields as $act_field) {
                    foreach ($fields as $key => $value) {
                        $type = null;
                        $currentLenght = null;
                        $newLenght = null;
                        if (substr(strtolower($act_field["Type"]), 0, 4) === "int(" && substr(strtolower($value['type']), 0, 4) === "int(") {
                            $type = "int";
                            $currentLenght = substr(substr($act_field["Type"], strpos($act_field["Type"], "(") + 1), 0, -1);
                            $newLenght = substr(substr($value['type'], strpos($value['type'], "(") + 1), 0, -1);
                        }
                        else if (substr(strtolower($act_field["Type"]), 0, 8) === "varchar(" && substr(strtolower($value['type']), 0, 8) === "varchar(") {
                            $type = "varchar";
                            $currentLenght = substr(substr($act_field["Type"], strpos($act_field["Type"], "(") + 1), 0, -1);
                            $newLenght = substr(substr($value['type'], strpos($value['type'], "(") + 1), 0, -1);
                        }
                        if ($act_field["Field"] == $key) {
                            // Check if some actual "int" fields increment their length: if yes, consider them in the alter query
                            if ($type == "int") {
                                $fixAutoIncrement = false;
                                // WSX5-2950: This fix some situations where an existing field lost his "auto increment" property. If there is a row with this field set to 0, update it to the next highest value.
                                if (isset($value["auto_increment"]) && $value["auto_increment"] && !strpos($act_field["Extra"], "auto_increment")) {
                                    $fixAutoIncrement = true;
                                    $q = mysql_query("SELECT * FROM `" . $this->db_name . "`.`" . $name . "` WHERE `" . $key . "` = 0", $this->conn);
                                    $res = mysql_num_rows($q);
                                    if ($res !== false && $res > 0) {
                                        $q = mysql_query("SELECT MAX(`" . $key . "`) AS `highest` FROM `". $this->db_name . "`.`" . $name . "`", $this->conn);
                                        $res = mysql_fetch_array($q);
                                        $highestValue = $res !== false ? $res["highest"] : 1;
                                        mysql_query("UPDATE `" . $this->db_name . "`.`" . $name . "` SET `" . $key . "` = " . ($highestValue + 1) . " WHERE `" . $key . "` = 0", $this->conn);
                                    }
                                }
                                if ($newLenght > $currentLenght || $fixAutoIncrement) {
                                    $modify = " MODIFY `" . $key . "` " . $value['type'];
                                    $modify .= (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "");
                                    $modify .= (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "");
                                    $modify .= (isset($value['auto_increment']) && $value['auto_increment'] ? " AUTO_INCREMENT" : "");
                                    $modify .= (isset($value['more']) ? " " . $value['more'] : "");
                                    $qfields[] = $modify;
                                    $alterTable = true;
                                }
                            }
                            // Check if some actual "varchar" fields increment their length: if yes, consider them in the alter query
                            else if ($type == "varchar" && $newLenght > $currentLenght) {
                                $modify = " MODIFY `" . $key . "` " . $value['type'];
                                $modify .= (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "");
                                $modify .= (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "");
                                $modify .= (isset($value['more']) ? " " . $value['more'] : "");
                                $qfields[] = $modify;
                                $alterTable = true;
                            }
                            // Check if some actual "text" fields should be altered to "mediumtext": if yes, consider them in the alter query
                            else if (strtolower($act_field["Type"]) == "text" && strtolower($value['type']) == "mediumtext") {
                                $modify = " MODIFY `" . $key . "` " . $value['type'];
                                $modify .= " CHARACTER SET utf8 COLLATE utf8_unicode_ci";
                                $modify .= (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "");
                                $modify .= (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "");
                                $modify .= (isset($value['more']) ? " " . $value['more'] : "");
                                $qfields[] = $modify;
                                $alterTable = true;
                            }
                        }
                    }
                }
                // If alter query must be executed, execute it
                if ($alterTable) {
                    $query .= implode(",", $qfields);
                    $createResult = mysql_query($query, $this->conn);
                }
            }
        }
        return $createResult;
    }

    /**
     * Delete a table from the database.
     * 
     * @param {string} $table The table name
     *
     * @return {Void}
     */
    function deleteTable($table)
    {
        mysql_query("DROP TABLE " . $this->db_name . "." . $table, $this->conn);
    }

    /**
     * Check if the table exists
     * 
     * @param {string} $table The table name
     * 
     * @return {boolean} True if the table exists. False otherwise.
     */
    function tableExists($table)
    {
        $result = mysql_query("SHOW FULL TABLES FROM `" . $this->db_name . "` LIKE '" . mysql_real_escape_string($table, $this->conn) . "'", $this->conn);
        // Check that the name is correct (usage of LIKE is not correct if there are wildcards in the table name. Unfortunately MySQL 4 doesn't allow another syntax..)
        while (!is_bool($result) && $tb = mysql_fetch_array($result)) {
            if ($tb[0] == $table)
                return true;
        }
        return false;
    }

    /**
     * Get the last MySQL error.
     * 
     * @return {array}
     */
    function error()
    {
        return mysql_error();
    }

    /**
     * Provide the last inserted ID of the AUTOINCREMENT column
     * 
     * @return {int} The id of the latest insert operation
     */
    function lastInsertId()
    {
        $res = $this->query("SELECT LAST_INSERT_ID() AS `id`");
        if (count($res) > 0 && isset($res[0]['id'])) {
            return $res[0]['id'];
        }
        return 0;
    }

    /**
     * Execute a MySQL query.
     * 
     * @param {string} $query
     * 
     * @return {array}        The query result or FALSE on error
     */
    function query($query)
    {
        $result = mysql_query($query, $this->conn);
        if (!is_bool($result)) {
            $rows = array();
            while($row = mysql_fetch_array($result)) {
                $rows[] = $row;
            }
            return $rows;
        }
        return $result;
    }

    /**
     * Escape a MySQL query string.
     * 
     * @param {string} $string The string to escape
     * 
     * @return {string} The escaped string
     */
    function escapeString($string)
    {
        if (!is_array($string)) {
            return mysql_real_escape_string($string, $this->conn);
        } else {
            for ($i = 0; $i < count($string); $i++)
                $string[$i] = $this->escapeString($string[$i]);
            return $string;
        }
    }

    /**
     * Return the number of affected rows in the last query.
     * 
     * @return {integer} The number of affected rows.
     */
    function affectedRows()
    {
        return mysql_affected_rows($this->conn);
    }
}




/**
 * @summary
 * A database driver class which access to the DB using MySQLi.
 * 
 * To use this class, you must include __x5engine.php__ in your code.
 * 
 * @description Create a new ImDb Object
 *
 * @ignore
 * @class
 * @constructor
 * 
 * @param {string} $host  The database host address
 * @param {string} $user  The database username
 * @param {string} $pwd   The database password
 * @param {string} $db    The database name
 */
class MySQLiDriver implements DatabaseAccess
{
    private $db;
    private $db_name;
    private $engine = "INNODB";
    
    function __construct($host, $user, $pwd, $db)
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->db_name = $db;
        $this->db = @new mysqli($host, $user, $pwd);
        if ($this->db->connect_errno) {
            return;
        }
        if (strlen($db)) {
            $this->db->select_db($db);
        }
        if (function_exists('mysqli_set_charset')) {
            $this->db->set_charset("utf8mb4");
        }
        else {
            $this->db->query('SET NAMES "utf8mb4"');
        }
    }

    /**
     * Check if the class is connected or not to a db
     *
     * @return {boolean} True if the class is connected to a DB. False otherwise.
     */
    function testConnection()
    {
        return ($this->db->connect_errno == 0);
    }

    /**
     * Close the connection
     * 
     * @return void
     */
    function closeConnection()
    {
        $this->db->close();
    }

    function get_db_name()
    {
        return $this->db_name;
    }
    
    /**
     * Create a new table or update an existing one.
     * 
     * @param string $name   The table name
     * @param array $fields  The table fields list as array of associative arrays (one array item foreach table field). must be passed as stated in the example.
     *
     * @example
     * $db->createTable('tableName', array(
     *     "field1" => array(
     *         "type" => "INTEGER",
     *         "null" => false,
     *         "auto_increment" => true,
     *         "primary" => true
     *     ),
     *     "field2" => array(
     *         "type" => "TEXT",
     *         "null" => true,
     *         "auto_increment" => false,
     *         "more" => "CHARACTER SET UTF-8"
     *     ))
     * );
     * 
     * @return boolean True if the table was created succesfully.
     */
    function createTable( $name, $fields )
    {
        $qfields = array();
        $primaries = array();
        $createResult = false;

        // If the table does not exists, create it
        if (!$this->tableExists($name)) {
            $query = "CREATE TABLE `" . $this->db_name . "`.`" . $name . "` (";
            foreach ($fields as $key => $value) {
                $qfields[] = "`" . $key . "` " .
                            $value['type'] .
                            ($value['type'] == 'TEXT' || $value['type'] == 'MEDIUMTEXT' ? " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" : "") .
                            (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "") .
                            (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "") .
                            (isset($value['auto_increment']) ? " AUTO_INCREMENT" : "") .
                            (isset($value['more']) ? " " . $value['more'] : "");
                if (isset($value['primary']) && $value['primary']) {
                    $primaries[] = "`" . $key . "`";
                }
            }
            $query .= implode(",", $qfields);
            if (count($primaries))
                $query .= ", PRIMARY KEY (" . implode(",", $primaries) . ")";
            $query .= ") ENGINE = " . $this->engine . " ;";
            $createResult = $this->db->query($query);
        } else {
            $result = $this->db->query("SHOW FULL COLUMNS FROM `" . $this->db_name . "`.`" . $name . "`");
            // Alter table flag: if true execute the query at the end
            $alterTable = false;
            if ($result) {
                // Actual fields
                $query = "ALTER TABLE `" . $this->db_name. "`.`" . $name . "`";
                $act_fields = array();
                while ($row = $result->fetch_array()) {
                    $act_fields[] = $row;
                    $act_fields_names[] = $row[0];
                }
                // New fields
                $new_fields = array_diff(array_keys($fields), $act_fields_names);
                $new_fields = array_merge($new_fields); // Order the indexes
                if (count($new_fields) > 0) {
                    foreach ($new_fields as $key) {
                        $qfields[] = " ADD `" . $key . "` " . $fields[$key]['type'] . 
                        ($fields[$key]['type'] == 'TEXT' || $fields[$key]['type'] == 'MEDIUMTEXT' ? " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" : "") .
                        (!isset($fields[$key]['null']) || !$fields[$key]['null'] ? " NOT NULL" : "") .
                        (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "") .
                        (isset($fields[$key]['auto_increment']) && $fields[$key]['auto_increment'] ? " AUTO_INCREMENT" : "") .
                        // WSXTWE-1215: Manage the adding/removal of a primary key
                        (isset($fields[$key]['primary']) && $fields[$key]['primary'] ? " PRIMARY KEY" : "") .
                        (isset($fields[$key]['more']) ? " " . $fields[$key]['more'] : "");
                    }
                    $alterTable = true;
                }
                // Check if it's necessary to do some changes on actual fields: if yes, consider them in the alter query
                foreach ($act_fields as $act_field) {
                    foreach ($fields as $key => $value) {
                        $type = null;
                        $currentLenght = null;
                        $newLenght = null;
                        if (substr(strtolower($act_field["Type"]), 0, 4) === "int(" && substr(strtolower($value['type']), 0, 4) === "int(") {
                            $type = "int";
                            $currentLenght = substr(substr($act_field["Type"], strpos($act_field["Type"], "(") + 1), 0, -1);
                            $newLenght = substr(substr($value['type'], strpos($value['type'], "(") + 1), 0, -1);
                        }
                        else if (substr(strtolower($act_field["Type"]), 0, 8) === "varchar(" && substr(strtolower($value['type']), 0, 8) === "varchar(") {
                            $type = "varchar";
                            $currentLenght = substr(substr($act_field["Type"], strpos($act_field["Type"], "(") + 1), 0, -1);
                            $newLenght = substr(substr($value['type'], strpos($value['type'], "(") + 1), 0, -1);
                        }
                        if ($act_field["Field"] == $key) {
                            // Check "null" and "more" configuration
                            $explicitlyNull = isset($value['more']) && strtolower($value['more']) == 'null';
                            $explicitlyNotNull = (isset($value['more']) && strtolower($value['more']) == 'not null') || !isset($value['null']) || !$value['null'];

                            // Check if some actual "int" fields increment their length: if yes, consider them in the alter query
                            if ($type == "int") {
                                $fixAutoIncrement = false;
                                // WSX5-2950: This fix some situations where an existing field lost his "auto increment" property. If there is a row with this field set to 0, update it to the next highest value.
                                if (isset($value["auto_increment"]) && $value["auto_increment"] && !strpos($act_field["Extra"], "auto_increment")) {
                                    $fixAutoIncrement = true;
                                    $q = $this->db->query("SELECT * FROM `" . $this->db_name . "`.`" . $name . "` WHERE `" . $key . "` = 0");
                                    if ($q->num_rows > 0) {
                                        $q = $this->db->query("SELECT MAX(`" . $key . "`) AS `highest` FROM `". $this->db_name . "`.`" . $name . "`");
                                        $res = $q->fetch_array();
                                        $highestValue = !is_null($res) ? $res["highest"] : 1;
                                        $this->db->query("UPDATE `" . $this->db_name . "`.`" . $name . "` SET `" . $key . "` = " . ($highestValue + 1) . " WHERE `" . $key . "` = 0");
                                    }
                                }
                                if ($newLenght > $currentLenght || $fixAutoIncrement) {
                                    $modify = " MODIFY `" . $key . "` " . $value['type'];
                                    $modify .= (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "");
                                    $modify .= (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "");
                                    $modify .= (isset($value['auto_increment']) && $value['auto_increment'] ? " AUTO_INCREMENT" : "");
                                    $modify .= (isset($value['more']) ? " " . $value['more'] : "");
                                    $qfields[] = $modify;
                                    $alterTable = true;
                                }
                            }
                            // Check if some actual "varchar" fields increment their length: if yes, consider them in the alter query
                            else if ($type == "varchar" && $newLenght > $currentLenght) {
                                $modify = " MODIFY `" . $key . "` " . $value['type'];
                                $modify .= (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "");
                                $modify .= (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "");
                                $modify .= (isset($value['more']) ? " " . $value['more'] : "");
                                $qfields[] = $modify;
                                $alterTable = true;
                            }
                            // Check if some actual "text" fields should be altered to "mediumtext": if yes, consider them in the alter query
                            else if ($act_field['Collation'] == 'utf8_unicode_ci' || (strtolower($act_field["Type"]) == "text" && strtolower($value['type']) == "mediumtext")) {
                                $modify = " MODIFY `" . $key . "` " . $value['type'];
                                $modify .= " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                                $modify .= (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "");
                                $modify .= (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "");
                                $modify .= (isset($value['more']) ? " " . $value['more'] : "");
                                $qfields[] = $modify;
                                $alterTable = true;
                            }
                            // Check for "null" and "more" configuration changes
                            else if ((strtolower($act_field['Null']) == 'no' && $explicitlyNull) || (strtolower($act_field['Null']) == 'yes' && $explicitlyNotNull)) {
                                $modify = " MODIFY `" . $key . "` " . $value['type'];
                                $modify .= ($value['type'] == 'TEXT' || $value['type'] == 'MEDIUMTEXT' ? " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" : "");
                                $modify .= (!isset($value['null']) || !$value['null'] ? " NOT NULL" : "");
                                $modify .= (isset($value['unique']) && $value['unique'] ? " UNIQUE" : "");
                                $modify .= (isset($value['auto_increment']) && $value['auto_increment'] ? " AUTO_INCREMENT" : "");
                                $modify .= (isset($value['more']) ? " " . $value['more'] : "");
                                $qfields[] = $modify;
                                $alterTable = true;
                            }
                        }
                    }
                }
                // If alter query must be executed, execute it
                if ($alterTable) {
                    $query .= implode(",", $qfields);
                    $createResult = $this->db->query($query);
                }
            }
        }
        return $createResult;
    }

    /**
     * Delete a table from the database.
     * 
     * @param {string} $table The table name
     *
     * @return {Void}
     */
    function deleteTable($table)
    {
        $this->db->query("DROP TABLE " . $this->db_name . "." . $table);
    }

    /**
     * Check if the table exists
     * 
     * @param {string} $table The table name
     * 
     * @return {boolean} True if the table exists. False otherwise.
     */
    function tableExists($table)
    {
        $result = $this->db->query("SHOW FULL TABLES FROM `" . $this->db_name . "` LIKE '" . $this->db->real_escape_string($table) . "'");
        // Check that the name is correct (usage of LIKE is not correct if there are wildcards in the table name. Unfortunately MySQL 4 doesn't allow another syntax..)
        while (!is_bool($result) && $tb = $result->fetch_array()) {
            if (strtolower($tb[0]) == strtolower($table))
                return true;
        }
        return false;
    }

    /**
     * Get the last MySQL error.
     * 
     * @return {array}
     */
    function error()
    {
        return $this->db->error;
    }

    /**
     * Provide the last inserted ID of the AUTOINCREMENT column
     * 
     * @return {int} The id of the latest insert operation
     */
    function lastInsertId()
    {
        $res = $this->query("SELECT LAST_INSERT_ID() AS `id`");
        if (count($res) > 0 && isset($res[0]['id'])) {
            return $res[0]['id'];
        }
        return 0;
    }

    /**
     * Execute a MySQL query.
     * 
     * @param {string} $query
     * 
     * @return {array}        The query result or FALSE on error
     */
    function query($query)
    {
        $result = $this->db->query($query);
        if (!is_bool($result)) {
            $rows = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $rows[] = $row;
            }
            return $rows;
        }
        return $result;
    }

    /**
     * Escape a MySQL query string.
     * 
     * @param {string} $string The string to escape
     * 
     * @return {string} The escaped string
     */
    function escapeString($string)
    {
        if (!is_array($string)) {
            return $this->db->real_escape_string($string);
        } else {
            for ($i = 0; $i < count($string); $i++) {
                $string[$i] = $this->escapeString($string[$i]);
            }
            return $string;
        }
    }

    /**
     * Return the number of affected rows in the last query.
     * 
     * @return {integer} The number of affected rows.
     */
    function affectedRows()
    {
        return $this->db->affected_rows;
    }
}




/**
 * @summary
 * A utility class which provides an easy access to the databases defined by the WSX5 user.
 * Detect if MySQLi is supported, otherwise fallback on MySQL.
 * 
 * To use this class, you must include __x5engine.php__ in your code.
 * 
 * @description Create a new ImDb Object
 *
 * @class
 * @constructor
 * 
 * @param string $host  The database host address
 * @param string $user  The database username
 * @param string $pwd   The database password
 * @param string $db    The database name
 * @param string $table_prefix    A prefix for all table names
 */
class ImDb implements DatabaseAccess
{
    private $driver;
    private $table_prefix;
    
    function __construct($host, $user, $pwd, $db, $table_prefix)
    {
        // Detect the correct driver
        if (function_exists("mysqli_connect")) {
            $this->driver = new MySQLiDriver($host, $user, $pwd, $db);
        } else if (function_exists("mysql_connect")) {
            $this->driver = new MySQLDriver($host, $user, $pwd, $db);
        } else {
            die("No database support detected");
        }
        $this->table_prefix = $table_prefix;
    }
    static function from_db_data($db): ImDb
    {
        return new ImDb($db['host'], $db['user'], $db['password'], $db['database'], $db['table_prefix']);
    }

    /**
     * Check if the class is connected or not to a db
     *
     * @return {boolean} True if the class is connected to a DB. False otherwise.
     */
    function testConnection()
    {
        return $this->driver->testConnection();
    }

    /**
     * Close the connection
     * 
     * @return void
     */
    function closeConnection()
    {
        $this->driver->closeConnection();
    }

    function get_db_name()
    {
        return $this->driver->get_db_name();
    }

    /**
     * Create a new table or update an existing one.
     * 
     * @param {string} $name   The table name
     * @param {array} $fields  The table fields list as array of associative arrays (one array item foreach table field). must be passed as stated in the example.
     *
     * @example
     * $db->createTable('tableName', array(
     *     "field1" => array(
     *         "type" => "INTEGER",
     *         "null" => false,
     *         "auto_increment" => true,
     *         "primary" => true
     *     ),
     *     "field2" => array(
     *         "type" => "TEXT",
     *         "null" => true,
     *         "auto_increment" => false,
     *         "more" => "CHARACTER SET UTF-8"
     *     ))
     * );
     * 
     * @return {boolean} True if the table was created succesfully.
     */
    function createTable($name, $fields)
    {
        return $this->driver->createTable($this->_prefixed_table_name($name), $fields);
    }

    /**
     * Delete a table from the database.
     * 
     * @param {string} $table The table name
     *
     * @return {Void}
     */
    function deleteTable($table)
    {
        $this->driver->deleteTable($this->_prefixed_table_name($table));
    }

    /**
     * Check if the table exists
     * 
     * @param {string} $table The table name
     * 
     * @return {boolean} True if the table exists. False otherwise.
     */
    function tableExists($table)
    {
        return $this->driver->tableExists($this->_prefixed_table_name($table));
    }

    /**
     * Get the last MySQL error.
     * 
     * @return {array}
     */
    function error()
    {
        return $this->driver->error();
    }

    /**
     * Provide the last inserted ID of the AUTOINCREMENT column
     * 
     * @return {int} The id of the latest insert operation
     */
    function lastInsertId()
    {
        return $this->driver->lastInsertId();
    }

    /**
     * Execute a MySQL query.
     * 
     * @param mixed $query if is_string($query) it will be executed, if is_array($query) it's used to call select, update, delete or insert function
     * 
     * @return array        The query result or FALSE on error
     */
    function query($query)
    {
        if (is_string($query)) {
            return $this->driver->query($query);
        }
        if (is_array($query)) {
            if (isset($query['into'])) {
                return $this->insert($query);
            } else if (isset($query['update'])) {
                return $this->update($query);
            } else if (isset($query['delete']) || isset($query['delete_from'])) {
                return $this->delete($query);
            } else if (isset($query['select']) || isset($query['select_from'])
                || isset($query['order_by']) || isset($query['orderBy'])
                || isset($query['group_by']) || isset($query['groupBy'])
                || isset($query['limit'])) {
                return $this->select($query);
            }
        }
        return false;
    }

    function tableColumns($data)
    {
        if (is_string($data)) {
            return $this->query('SHOW COLUMNS FROM ' . $this->table($data));
        } else if (is_array($data) && isset($data['table'])) {
            return $this->query('SHOW COLUMNS FROM ' . $this->table($data['table']) . (isset($data['like']) ? ' LIKE \'' . $data['like'] . '\'' : ''));
        }
        return false;
    }

    /**
     * Execute SQL SELECT.
     * 
     * $select_data['select'] => array of 'column_name' (default: all columns).
     * 
     * $select_data['from'] or $select_data['select_from'] => table name (mandatory).
     * 
     * $select_data['where'] => array of 'column_name' => column_value (default: no conditions).
     * 
     * $select_data['where_flat'] => array of 'condition' (default: no conditions).
     * 
     * $select_data['order_by'] or $select_data['orderBy'] => array of columns / string of single column name used for ordering results.
     * 
     * $select_data['group_by'] or $select_data['groupBy'] => array of columns / string of single column name used for ordering results.
     * 
     * @param array $select_data
     * @return array The SELECT result or false on error
     */
    public function select($select_data)
    {
        if ($this->testConnection()) {
			$table_name = isset($select_data['from']) ? $select_data['from'] : $select_data['select_from'];
            return $this->query('SELECT ' . $this->_to_sql_column_name($select_data['select']) 
                . ' FROM ' . $this->table($table_name) 
                . $this->_where($select_data)
                . $this->_group_by($select_data)
                . $this->_order_by($select_data)
                . $this->_query_limit($select_data));
        }
        return false;
    }
    /**
     * Execute SQL INSERT.
     * 
     * $insert_data['into'] => table name (mandatory).
     * 
     * $insert_data['values'] => array of 'column_name' => column_value (mandatory).
     * 
     * @param array $insert_data
     */
    public function insert($insert_data)
    {
        if ($this->testConnection()) {
            return $this->query('INSERT INTO ' . $this->table($insert_data['into']) . ' ' . $this->_values($insert_data['values']));
        }
        return false;
    }
    /**
     * Execute SQL DELETE.
     * 
     * $delete_data['from'] or $delete_data['delete_from'] => table name (mandatory).
     * 
     * $delete_data['where'] => array of 'column_name' => column_value (default: no conditions).
     * 
     * $delete_data['where_flat'] => array of 'condition' (default: no conditions).
     * 
     * @param array $delete_data
     */
    public function delete($delete_data)
    {
        if ($this->testConnection()) {
			$table_name = isset($delete_data['from']) ? $delete_data['from'] : $delete_data['delete_from'];
            return $this->query('DELETE FROM ' . $this->table($table_name) . $this->_where($delete_data));
        }
        return false;
    }
    /**
     * Execute SQL UPDATE.
     * 
     * $update_data['update'] => table name (mandatory).
     * 
     * $update_data['set'] => array of 'column_name' => column_value (mandatory).
     * 
     * $update_data['where'] => array of 'column_name' => column_value (default: no conditions).
     * 
     * $update_data['where_flat'] => array of 'condition' (default: no conditions).
     * 
     * @param array $update_data
     */
    public function update($update_data)
    {
        if ($this->testConnection()) {
            return $this->query('UPDATE ' . $this->table($update_data['update']) . ' ' . $this->_set($update_data['set']) . $this->_where($update_data));
        }
        return false;
    }

    public function table($table_name): string
    {
        return '`' . (strlen($this->get_db_name()) > 0 ? $this->get_db_name() . '`.`' : '') . $this->_prefixed_table_name($table_name) . '`';
    }

    private function _prefixed_table_name($table_name){
        return $this->table_prefix . $table_name;
    }
    
    private function _values($values): string
    {
        return '(' . $this->_to_sql_column_name(array_keys($values)) . ') VALUES ' . $this->_to_sql_value(array_values($values));
    }
    private function _set($values) : string
    {
        $set_array = array();
        foreach ($values as $column => $value) {
            $set_array[] = $this->_to_sql_column_name($column) . '=' . $this->_to_sql_value($value);
        }
        return 'SET ' . implode(', ', $set_array);
    }
    private function _where($data) : string
    {
        $conditions = $this->_conditions($data);
        return count($conditions) ? ' WHERE ' . implode(" AND ", $conditions) : '';
    }
    private function _conditions($data): array
    {
        $conditions = array();
        if (isset($data['where'])) {
            foreach ($data['where'] as $column => $value) {
                $conditions[] = (is_string($value) ? 'BINARY ' : '') . '`' . $column . '` ' . (is_null($value) ? 'IS' : (is_array($value) ? 'IN' : '=')) . ' ' . $this->_to_sql_value($value);
            }
        }
        if (isset($data['where_flat'])) {
            if (is_string($data['where_flat'])) {
                $conditions[] = $data['where_flat'];
            } else if (is_array($data['where_flat'])) {
                foreach ($data['where_flat'] as $cond) {
                    $conditions[] = $cond;
                }
            }
        }
        return $conditions;
    }
    private function _to_sql_value($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        } else if (is_array($value) && count($value)) {
            if (isset($value['fn'])) {
                return strtoupper($value['fn']) . '(' . (isset($value['value']) ? $this->_to_sql_value($value['value']) : '') . ')';
             } else {
                return '(' . implode(', ', array_map(array($this, '_to_sql_value'), $value)) . ')';
            }
        } else if (is_string($value)) {
            return '\'' . $this->escapeString($value) . '\'';
        }
        return $value;
    }
    private function _to_sql_column_name($column): string
    {
        if (isset($column)) {
            if (is_string($column)) {
                return '`' . $column . '`';
            }
            if (is_array($column) && count($column)) {
                if (isset($column['column']) || isset($column['fn'])) {
                    $name = $this->_to_sql_column_name($column['column']);
                    $as = isset($column['as']) ? ' AS ' . $this->_to_sql_column_name($column['as']) : '';
                    return isset($column['fn']) ? strtoupper($column['fn']) . '(' . $name . ')' . $as : $name . $as;
                } else {
                    return implode(', ', array_map(array($this, '_to_sql_column_name'), $column));
                }
            }
        }
        return '*';
    }
    private function _order_by($data): string
    {
        $order_data = isset($data['order_by']) ? $data['order_by'] : (isset($data['orderBy']) ? $data['orderBy'] : null);
        if (!is_null($order_data)) {
            $arr = array();
            if (is_string($order_data)) {
                $arr[] = $this->_to_sql_column_name($order_data);
            } else if (is_array($order_data)) {
                foreach ($order_data as $k => $v) {
                    if (is_int($k)) {
                        $arr[] = $this->_to_sql_column_name($v);
                    } else {
                        $arr[] = $this->_to_sql_column_name($k) . ' ' . strtoupper($v);
                    }
                }
            }
            return ' ORDER BY ' . implode(', ', $arr);
        }
        return '';
    }
    private function _group_by($data) : string
    {
		$group_by_data = isset($data['group_by']) ? $data['group_by'] : (isset($data['groupBy']) ? $data['groupBy'] : null);
        if(!is_null($group_by_data)){
            return ' GROUP BY ' . $this->_to_sql_column_name($group_by_data);
        }
        return '';
    }
    private function _query_limit($data): string
    {
        return isset($data['limit']) ? ' LIMIT ' . implode(', ', $data['limit']) : '';
    }

    /**
     * Escape a MySQL query string.
     * 
     * @param {string} $string The string to escape
     * 
     * @return {string} The escaped string
     */
    function escapeString($string)
    {
        return $this->driver->escapeString($string);
    }

    /**
     * Return the number of affected rows in the last query.
     * 
     * @return {integer} The number of affected rows.
     */
    function affectedRows()
    {
        return $this->driver->affectedRows();
    }

    static function clone_local_tables(ImDb $db, array $table_names_map, bool $overwrite = false): array
    {
        $results = array();
        if ($db->testConnection()) {
            foreach ($table_names_map as $master_table => $clone_table) {
                $res = array(
                    'master' => array(
                        'name' => $master_table,
                        'exist' => $db->tableExists($master_table),
                    ),
                    'clone' => array(
                        'name' => $clone_table,
                        'exist' => $db->tableExists($clone_table),
                    )
                );
                if ($res['clone']['exist'] && $overwrite) {
                    $db->deleteTable($clone_table);
                }
                if ($res['master']['exist'] && (!$res['clone']['exist'] || $overwrite)) {
                    $db->query('CREATE TABLE ' . $db->table($clone_table) . ' LIKE ' . $db->table($master_table));
                    $db->query('INSERT INTO ' . $db->table($clone_table) . ' SELECT * FROM ' . $db->table($master_table));
                }
                $results[] = $res;
            }
        }
        return $results;
    }

    static function clone_remote_tables(ImDb $master_db, ImDb $clone_db, array $table_names_map, bool $overwrite = false): array
    {
        $results = array();
        if ($master_db->testConnection() && $clone_db->testConnection()) {
            foreach ($table_names_map as $master_table => $clone_table) {
                $res = array(
                    'master' => array(
                        'name' => $master_table,
                        'exist' => $master_db->tableExists($master_table),
                    ),
                    'clone' => array(
                        'name' => $clone_table,
                        'exist' => $clone_db->tableExists($clone_table),
                    )
                );
                if ($res['clone']['exist'] && $overwrite) {
                    $clone_db->deleteTable($clone_table);
                }
                if ($res['master']['exist'] && (!$res['clone']['exist'] || $overwrite)) {
                    $create_table = $master_db->query('SHOW CREATE TABLE ' . $master_db->table($master_table));
                    if (is_array($create_table)) {
                        // Using preg_replace to replace just the first occurrency
                        $create_table_query = preg_replace('/`' . preg_quote($master_table, '/') . '`/', $clone_db->table($clone_table), $create_table[0]['Create Table'], 1);
                        $clone_db->query($create_table_query);

                        $master_rows = $master_db->select(array('from' => $master_table));
                        foreach ($master_rows as $master_row) {
                            $clone_db->insert(array(
                                'into' => $clone_table,
                                'values' => $master_row
                            ));
                        }
                    }
                }
                $results[] = $res;
            }
        }
        return $results;
    }
}




/**
 * Analytics class
 * @access public
 */
class Analytics
{
	private $db;
    private $table_name;

	public function __construct($db, $tableprefix) {
		$this->db = $db;
        $this->table_name = $tableprefix . "_visits";
		if (!$this->db->testConnection()) {
            die("Analytics: Cannot connect to database");
        }
	}

	/**
	 * Register the visit of an url
	 *
     * @param  String $uid    The current user unique id
	 * @param  String $url    The visited url in the current domain
	 * @param  String $lang   The user's language
	 * @param  String [$ts]   The timestamp in php format yyyy-MM-dd hh:mm:ss. Leave null to set automatically.
	 *
     * @return Bool           True if the registration was ok
	 */
	public function visit($uid, $url, $lang, $ts = null) {
		$this->createTable();

        if ($ts == null) {
            $ts = date("Y-m-d H:i:s");
        }

        // Check that the uid is in the correct format
        if (!preg_match("/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i", $uid)) {
            return;
        }

        // Remove the text after the anchors
        $pos = strpos($url, "#");
        if ($pos !== false) {
            $url = substr($url, 0, $pos);
        }

        // Remove the url params
        $pos = strpos($url, "?");
        if ($pos !== false) {
            $url = substr($url, 0, $pos);
        }

        return $this->db->insert(array(
            'into' => $this->table_name,
            'values' => array(
                'ts' => $ts,
                'uid' => $uid,
                'lang' => $lang,
                'url' => $url
            )
        ));
	}

	/**
	 * Get the unique visitors of the specified url
     * @param  String $from The start date in format like 2015-06-25 15:00:00
     * @param  String $to   The end date in format like 2015-06-25 15:00:00
     * @param  String $url  The visited url. Leave empty to get the data of the whole site
	 *
     * @return Array        An array like $utcData => $numberOfVisitor
	 */
	public function getTotalSiteVisitors($from, $to, $url = null) {
        $results = $this->db->select(array(
            'select' => array(
                array('fn' => 'date', 'column' => 'ts', 'as' => 'date'),
                array('fn' => 'count', 'column' => array('fn' => 'distinct', 'column' => 'uid'), 'as' => 'count')
            ),
            'from' => $this->table_name,
            'where' => $this->_where($url),
            'where_flat' => $this->_where_flat($from, $to),
            'group_by' => 'date',
            'order_by' => 'date'
        ));

        $curDate = new DateTime($from);
        $endDate = new DateTime($to);

        $data = array();
		// First create the empty array
		while ($curDate <= $endDate) {
			$data[formatDate($curDate)] = 0;
			$curDate->modify("+1 day");
		}
        if (is_array($results)) {
            // Then fill it with the known data
            foreach ($results as $entry) {
                $data[formatDate(DateTimeImmutable::createFromFormat("Y-m-d", $entry['date']))] = $entry['count'];
            }
        }
        return $data;
	}

	/**
	 * Get the total page views
     * @param  String $from The start date in format like 2015-06-25 15:00:00
     * @param  String $to   The end date in format like 2015-06-25 15:00:00
     * @param  String $url  The visited url. Leave empty to get the data of the whole site
	 *
     * @return Array        An array like $utcData => $numberOfViews
	 */
	public function getPageViews($from, $to, $url = null) {
        $results = $this->db->select(array(
            'select' => array(
                array('fn' => 'date', 'column' => 'ts', 'as' => 'date'),
                array('fn' => 'count', 'column' => 'url', 'as' => 'count')
            ),
            'from' => $this->table_name,
            'where' => $this->_where($url),
            'where_flat' => $this->_where_flat($from, $to),
            'group_by' => 'date',
            'order_by' => 'date'
        ));

        $curDate = new DateTime($from);
        $endDate = new DateTime($to);

        $data = array();
		// First create the empty array
		while ($curDate <= $endDate) {
			$data[formatDate($curDate)] = 0;
			$curDate->modify("+1 day");
		}
        if (is_array($results)) {
            // Then fill it with the known data
            foreach ($results as $entry) {
                $data[formatDate(DateTimeImmutable::createFromFormat("Y-m-d", $entry['date']))] = $entry['count'];
            }
        }
        return $data;
	}

	/**
	 * Get the total unique page views
     * @param  String $from The start date in format like 2015-06-25 15:00:00
     * @param  String $to   The end date  in format like 2015-06-25 15:00:00
     * @param  String $url  The visited url. Leave empty to get the data of the whole site
	 *
     * @return Array        An array like $utcData => $numberOfViews
	 */
	public function getUniquePageViews($from, $to, $url = null) {
        $results = $this->db->select(array(
            'select' => array(
                'url',
                array('fn' => 'date', 'column' => 'ts', 'as' => 'date'),
                array('fn' => 'count', 'column' => array('fn' => 'distinct', 'column' => 'uid'), 'as' => 'count')
            ),
            'from' => $this->table_name,
            'where' => $this->_where($url),
            'where_flat' => $this->_where_flat($from, $to),
            'group_by' => array('url', 'date'),
            'order_by' => 'date'
        ));

        $curDate = new DateTime($from);
        $endDate = new DateTime($to);

        $data = array();
		// First create the empty array
		while ($curDate <= $endDate) {
			$data[formatDate($curDate)] = 0;
			$curDate->modify("+1 day");
		}
        if (is_array($results)) {
            // Then fill it with the known data
            foreach ($results as $entry) {
                $data[formatDate(DateTimeImmutable::createFromFormat("Y-m-d", $entry['date']))] += $entry['count'];
            }
        }
        return $data;
	}

	/**
	 * Get an array with the most visited urls
     *
     * @param  String $from          The start date
     * @param  String $to            The end date
     * @param  String $number        The number of pages you want to see. Set zero to fetch them all.
     * @param  Bool   $orderByUnique True to order by the unique count. False to order by the normal count.
	 *
     * @return Array                 An array that contains the requested data
	 */
	public function getMostVisitedPages($from, $to, $number = 0, $orderByUnique = false) {
        $data = array(
            "total_count"        => 0,
            "total_unique_count" => 0,
            "data"               => array()
        );

        $totalcount = $this->db->select(array(
            'select' => array('fn' => 'count', 'column' => 'url', 'as' => 'count'),
            'from' => $this->table_name,
            'where_flat' => $this->_where_flat($from, $to)
        ));
        if (!is_array($totalcount)) {
            return $data;
        }
        $data['total_count'] = $totalcount[0]['count'];

        $uniquecountq = $this->db->select(array(
            'select' => array('fn' => 'count', 'column' => array('fn' => 'distinct', 'column' => 'url'), 'as' => 'count'),
            'from' => $this->table_name,
            'where_flat' => $this->_where_flat($from, $to),
            'group_by' => array('url', 'uid')
        ));
        if (!is_array($uniquecountq)) {
            return $data;
        }
        foreach ($uniquecountq as $count) {
            $data['total_unique_count'] += $count['count'];
        }

        $select = array(
            'select' => array(
                'url',
                array('fn' => 'count', 'column' => 'uid', 'as' => 'count'),
                array('fn' => 'count', 'column' => array('fn' => 'distinct', 'column' => 'uid'), 'as' => 'unique_count')
            ),
            'from' => $this->table_name,
            'where_flat' => $this->_where_flat($from, $to),
            'group_by' => 'url',
            'order_by' => ($orderByUnique ? array('unique_count' => 'desc') : array('count' => 'desc'))
        );
        if ($number > 0) {
            $select['limit'] = array(0, $number);
        }
        $results = $this->db->select($select);

        if (is_array($results)) {
            foreach ($results as $entry) {
                $data['data'][$entry['url']] = array(
                    'count'             => $entry['count'],
                    'count_perc'        => $entry['count'] / $data['total_count'],
                    'unique_count'      => $entry['unique_count'],
                    'unique_count_perc' => $entry['unique_count'] / $data['total_unique_count']
                );
            }
        }

        return $data;
	}

    /**
     * Get an array with the most popular browser language
     *
     * @param  String $from          The start date
     * @param  String $to            The end date
     * @param  Number $number The number of languages you want to see. Set zero to fetch them all.
     *
     * @return Array          An array like $language => $count
     */
    public function getBrowserLanguages($from, $to, $number = 0) {
        $data = array(
            "total_count" => 0,
            "data"        => array()
        );

        $total = $this->db->select(array(
            'select' => array('fn' => 'count', 'column' => array('fn' => 'distinct', 'column' => 'uid'), 'as' => 'count'),
            'from' => $this->table_name,
            'where_flat' => $this->_where_flat($from, $to)
        ));
        if (!is_array($total)) {
            return $data;
        }
        $data['total_count'] = $total[0]['count'];

        $select = array(
            'select' => array(
                'lang',
                array('fn' => 'count', 'column' => array('fn' => 'distinct', 'column' => 'uid'), 'as' => 'count')
            ),
            'from' => $this->table_name,
            'where_flat' => $this->_where_flat($from, $to),
            'group_by' => 'lang',
            'order_by' => array('count' => 'desc')
        );
        if ($number > 0) {
            $select['limit'] = array(0, $number);
        }
        $results = $this->db->select($select);

        if (is_array($results)) {
            foreach ($results as $entry) {
                $data['data'][$entry['lang']] = array(
                    "count" => $entry['count'],
                    "perc"  => $entry['count'] / $data['total_count']
                );
            }
        }
        return $data;
    }

	/**
	 * Create the DB tables used by the analytics system
	 *
     * @return Void
	 */
	private function createTable() {
		if (!$this->db->tableExists($this->table_name)) {
    		$this->db->createTable(
                $this->table_name,
                array(
                    "id" => array('type' => 'INT(11)', 'primary' => true, 'auto_increment' => true),
                    "ts" => array('type' => 'TIMESTAMP'),
                    "uid" => array('type' => 'VARCHAR(36)'),
                    "lang" => array('type' => 'VARCHAR(8)'),
                    "url" => array('type' => 'TEXT')
                )
            );
		}
    }

    private function _where($url): array
    {
        if (is_null($url)) {
            return array();
        } else {
            return array('url' => $url);
        }
    }
    private function _where_flat($from, $to): array
    {
        return array(
            'DATE(`ts`) >= DATE(\'' . $this->db->escapeString($from) . '\')',
            'DATE(`ts`) <= DATE(\'' . $this->db->escapeString($to) . '\')'
        );
    }
}







/**
 * Provide support for sending and saving the email form data
 */

class ImForm
{

    var $fields = array();
    var $files = array();
    var $answers = array();

    /**
     * Set the data of a field
     * 
     * @param string $label  The field label
     * @param strin  $value  The field value
     * @param string $dbname The name to use in the db
     * @param boolean $isSeparator True if this field must be used as separator in the email
     * 
     * @return boolean
     */
    function setField($label, $value, $dbname = "", $isSeparator = false)
    {
        $this->fields[] = array(
            "label"     => $label,
            "value"     => is_null($value) ? "" : $value,
            "dbname"    => $dbname,
            "isSeparator" => $isSeparator
        );
        return true;
    }

    /**
     * Provide the currently set fields
     * 
     * @return array
     */
    function fields()
    {
        return $this->fields;
    }

    /**
     * Checks if the file extension is one of the accepted
     * @param  string  $fileName   The name of the file
     * @param  mixed   $extensions The accepted extensions
     * @return boolean True if the file extension is accepted, false otherwise
     */
    function acceptedExtension($fileName, $extensions)
    {
        if (is_string($extensions)) {
            $extensions = strlen($extensions) ? explode(",", trim($extensions, ",")) : array();
        }
        if (count($extensions) === 0) return true;

        $fileName = strtolower($fileName);
        foreach ($extensions as $extension) {
            // WSXELE-738: Fix extensions separated by spaces
            $extension = strtolower(trim($extension));

            $extLen = strlen($extension) + 1;
            if (strlen($fileName) > $extLen && substr($fileName, -$extLen) === "." . $extension) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set a file field
     * 
     * @param string  $label      The field label
     * @param file    $value      The $_FILE[id] content
     * @param string  $folder     The folder in which the file must be saved
     * @param string  $dbname     The db column in which this filed must be saved
     * @param mixed   $extensions The extensions allowed for the file (string or array)
     * @param integer $maxsize    The max size (0 to not check this)
     * 
     * @param integer 1 = No file uploaded, 0 = success, -1 = generic error, -2 = extension not allowed, -3 = File too large
     */
    function setFile($label, $value, $folder = "", $dbname = "", $extensions = array(), $maxsize = 0)
    {

        if (!is_array($value['tmp_name'])){
            foreach($value as $key => $v){
                $value[$key] = array($v);
            }
        }

        $file_count = count($value['tmp_name']);
        for ($i=0; $i<$file_count; $i++) {

            if (!file_exists($value['tmp_name'][$i])){
                return 1; // If the file doesn't exists it means that it was not uploaded
            }

            $extension = $this->acceptedExtension($value['name'][$i], $extensions);
            if (!$extension)
                return -2;

            $size = ($maxsize == 0 || $maxsize >= $value['size'][$i]);
            if (!$size)
                return -3;

        }
            
        if ($folder != "" && substr($folder, 0, -1) != "/")
            $folder .= "/";


        for ($i=0; $i<$file_count; $i++) {
            $newValue = array ();
            foreach($value as $key => $v){
                $newValue[$key] = $v[$i];
            }
            $this->files[] = array(
                "value" => $newValue,
                "label" => $label,
                "dbname" => $dbname,
                "folder" => $folder,
                "content" => @file_get_contents($value['tmp_name'][$i])
            );
        }

        return 0;
    }

    /**
     * Provides the currently set files
     *
     *  @return array
     */
    function files()
    {
        return $this->files;
    }

    /**
     * Set the answer to check
     * 
     * @param string $questionId The question id
     * @param string $answer     The correct answer
     *
     * @return void
     */
    function setAnswer($questionId, $answer)
    {
        $this->answers[$questionId] = $answer;
    }

    /**
     * Check if the answer $answer is correct for question $questionId
     * 
     * @param  string $questionId The question id
     * @param  string $answer     The answer
     * 
     * @return boolean
     */
    function checkAnswer($questionId, $answer)
    {
        $questionId = strval($questionId);
        return (isset($this->answers[$questionId]) && trim(strtolower($this->answers[$questionId])) == trim(strtolower($answer)));
    }

    /**
     * Provides the currently set answers
     * 
     * @return array
     */
    function answers()
    {
        return $this->answers;
    }

    /**
     * Save the data in the database
     * 
     * @param  string $host
     * @param  string $user
     * @param  string $passwd
     * @param  string $database
     * @param  string $table
     * 
     * @return boolean
     */
    function saveToDb($db, $table)
    {
        if (!$db->testConnection())
            return false;

        $fields = array();

        // WSXTWE-1215: Add an autoincrement primary key
        $fields["id"] = array(
            "type" => "INT(11)",
            "null" => false,
            "auto_increment" => true,
            "primary" => true
        );
        $i = 0;
        $insert = array('into' => $table, 'values' => array());
        foreach ($this->fields as $field) {
            if (!$field['isSeparator']) {
                $name = isset($field['dbname']) && $field['dbname'] !== "" ? $field['dbname'] : "field_" . $i++;
                $fields[$name] = array(
                    "type" => "TEXT"
                );
                $insert['values'][$name] = is_array($field['value']) ? implode(", ", $field['value']) : $field['value'];
            }
        }
        $i = 0;
        foreach ($this->files as $file) {
            $fieldname = isset($file['dbname']) && $file['dbname'] !== "" ? $file['dbname'] : "file_" . $i++;
            $filename = $this->findFileName($file['folder'], $file['value']['name']);
            $fields[$fieldname] = array(
                "type" => "TEXT"
            );
            $insert['values'][$fieldname] = $filename;
           
            // Create and check the folder
            $folder = "../";
            if (($pos = strpos($file['folder'], "/")) === 0)
                $file['folder'] = substr($file['folder'], 1);
            $folder .= $file['folder'];
            if ($folder != "../" && !file_exists($folder))
                @mkdir($folder, 0777, true);
            $folder = str_replace("//", "/", $folder .= $filename);
            // Save the file
            @move_uploaded_file($file['value']['tmp_name'], $folder);
        }

        // Create the table
        $db->createTable($table, $fields);

        // Save the fields data
        $db->insert($insert);
        $db->closeConnection();
        return true;
    }

    /**
     * Find a free filename
     * 
     * @param  string $folder   The folder in which the file is being saved
     * @param  string $tmp_name The filename
     * 
     * @return string           The new name
     */
    function findFileName($folder, $tmp_name)
    {
        $pos = strrpos($tmp_name, ".");
        $ext = ($pos !== false ? substr($tmp_name, $pos) : "");
        $fname = basename($tmp_name, $ext);            
        do {
            $rname = $fname . "_" . rand(0, 10000) .  $ext;
        } while (file_exists($folder . $rname));
        return $rname;
    }

    /**
     * Send the email to the site's owner
     * 
     * @param  string  $from
     * @param  string  $replyTo
     * @param  string  $to
     * @param  string  $subject
     * @param  string  $text    The email body
     * @param  boolean $csv     Attach the CSV files?
     * 
     * @return boolean
     */
    function mailToOwner($from, $replyTo, $to, $subject, $text, $csv = false)
    {
        global $ImMailer;

        //Form Data
        $txtData = strip_tags($text);
        if (strlen($txtData))
             $txtData .= "\n\n";
        $htmData = nl2br($text);
        if (strlen($htmData))
            $htmData .= "\n<br><br>\n";
        $htmData .= "<table border=0 width=\"100%\">\r\n";
        $csvHeader = "";
        $csvData = "";
        $firstField = true;
        
        foreach ($this->fields as $field) {
            if ($field['isSeparator']) {
                //
                // This field is a form separator
                // 
                $txtData .= (!$firstField ? "\r\n" : "") . $field['label'] . "\r\n" . str_repeat("=", strlen($field['label'])) . "\r\n";
                $htmData .= "<tr valign=\"top\"><td colspan=\"2\" style=\"" . (!$firstField ? "padding-top: 8px;" : "") . " border-bottom: 1px solid [email:bodySeparatorBorderColor]; [email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . "</b></td></tr>\r\n";
            } else {
                //
                // This field is a classic form field
                // 
                $label = ($field['label'] != "" ? $field['label'] . ": " : "");
                if (is_array($field['value'])) {
                    $txtData .= $label . implode(", ", $field['value']) . "\r\n";
                    $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . $label . "</b></td><td style=\"[email:contentStyle]\">" . implode(", ", $field['value']) . "</td></tr>\r\n";
                    if ($csv) {
                        $csvHeader .= $field['label'] . ";";
                        $csvData .= implode(", ", $field['value']) . ";";
                    }
                } else {        
                    $txtData .= $label . $field['value'] . "\r\n";
                    // Is it an email?
                    if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $field['value'])) {
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td style=\"[email:contentStyle]\"><a href=\"mailto:" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                    } else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $field['value'])) {
                        // Is it an URL?
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td style=\"[email:contentStyle]\"><a href=\"" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                    } else {
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td style=\"[email:contentStyle]\">" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . "</td></tr>\r\n";
                    }
                    if ($csv) {
                        $csvHeader .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . ";";
                        $csvData .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . ";";
                    }
                }
            }
            $firstField = false;
        }

        $htmData .= "</table>";
        $attachments = array();
        if ($csv) {
            $attachments[] = array(
                "name" => "form_data.csv", 
                "content" => $csvHeader . "\n" . $csvData,
                "mime" => "text/csv"
            );
        }
        foreach ($this->files as $file) {
            $attachments[] = array(
                'name' => $file['value']['name'],
                'content' => $file['content'],
                'mime' => $file['value']['type']
            );
        }
        return $ImMailer->send($from, $replyTo, $to, $subject, $txtData, $htmData, $attachments);
    }

    /**
     * Send the email to the site's customer
     * 
     * @param  string  $from
     * @param  string  $reolyTo
     * @param  string  $to
     * @param  string  $subject
     * @param  string  $text    The email body
     * @param  boolean $summary Append the data to the email? (It's not an attachment)
     * 
     * @return boolean
     */
    function mailToCustomer($from, $replyTo, $to, $subject, $text, $csv = false)
    {
        global $ImMailer;

        //Form Data
        $txtData = strip_tags($text);
        if (strlen($txtData))
            $txtData .= "\n\n";
        $htmData = nl2br($text);
        if (strlen($htmData))
            $htmData .= "\n<br><br>\n";
        $csvHeader = "";
        $csvData = "";
        $firstField = true;
        
        if ($csv) {
            $htmData .= "<table border=0 width=\"100%\">\r\n";
            foreach ($this->fields as $field) {
                if ($field['isSeparator']) {
                    //
                    // This field is a form separator
                    // 
                    $txtData .= (!$firstField ? "\r\n" : "") . $field['label'] . "\r\n" . str_repeat("=", strlen($field['label'])) . "\r\n";
                    $htmData .= "<tr valign=\"top\"><td colspan=\"2\" style=\"" . (!$firstField ? "padding-top: 8px;" : "") . " border-bottom: 1px solid [email:bodySeparatorBorderColor]; [email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . "</b></td></tr>\r\n";
                } else {
                    //
                    // This field is a classic form field
                    // 
                    $label = ($field['label'] != "" ? $field['label'] . ": " : "");
                    if (is_array($field['value'])) {
                        $txtData .= $label . implode(", ", $field['value']) . "\r\n";
                        $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . $label . "</b></td><td style=\"[email:contentStyle]\">" . implode(", ", $field['value']) . "</td></tr>\r\n";
                        if ($csv) {
                            $csvHeader .= $field['label'] . ";";
                            $csvData .= implode(", ", $field['value']) . ";";
                        }
                    } else {                        
                        $txtData .= $label . $field['value'] . "\r\n";
                        // Is it an email?
                        if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $field['value'])) {
                            $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td style=\"[email:contentStyle]\"><a href=\"mailto:" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                        } else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $field['value'])) {
                            // Is it an URL?
                            $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td style=\"[email:contentStyle]\"><a href=\"" . $field['value'] . "\">". $field['value'] . "</a></td></tr>\r\n";
                        } else {
                            $htmData .= "<tr valign=\"top\"><td width=\"25%\" style=\"[email:contentStyle]\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $label) . "</b></td><td style=\"[email:contentStyle]\">" . str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . "</td></tr>\r\n";
                        }
                        if ($csv) {
                            $csvHeader .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['label']) . ";";
                            $csvData .= str_replace(array("\\'", '\\"'), array("'", '"'), $field['value']) . ";";
                        }
                    }
                }
                $firstField = false;
            }
            $htmData .= "</table>\n";
        }
        
        return $ImMailer->send($from, $replyTo, $to, $subject, $txtData, $htmData);
    }
}



/**
 * Blog class
 * @access public
 */
class ImGuestbook
{
	/**
	 * Get all the comments from all the guestbooks in the current website
	 *
	 * @param String $from       The start date (can be empty)
	 * @param String $to         The end date (can be empty)
	 * @param Boolean $approved  True to get only the approved comments, false to the only the ones waiting for validation
	 * 
	 * @return Array
	 */
	static public function getAllComments($from = "", $to = "", $approved = true)
	{
		global $imSettings;

		$commentsArray = array();
		foreach ($imSettings['guestbooks'] as $gb) {
			$comments = new ImTopic($gb['id'], "", "../", "index.php?id=" . $gb['id']);
	            $comments->loadXML($gb['folder']);
	        foreach ($comments->getComments($from, $to, $approved) as $comment) {
	        	$comment['topicid'] = $gb['id'];
	        	$comment['title'] = "";
	            $commentsArray[] = $comment;
	        }
		}
		usort($commentsArray, array("ImTopic", "compareCommentsArray"));
		return $commentsArray;
	}
}


/**
 * Manage the redirect to different pages basin gon the user's language
 */
class LanguageRedirect {

	/**
	 * The associative array of language => page
	 * @var array
	 */
	private $languages;
	private $defaultUrl;

	public function __construct()
	{
		$this->languages = array();
		$this->defaultUrl = "";
	}

	/**
	 * Add a redirect rule
	 * 
	 * @param String $langId      The language id
	 * @param String $redirectUrl The url to wich the user is redirect to
	 *
	 * @return void
	 */
	public function addRedirectRule($langId, $redirectUrl)
	{
		if (strlen($langId) > 0 && strlen($redirectUrl) > 0) {
			$parsedLang = $this->parseLanguage($langId);
			if ($parsedLang !== false) {
				$this->languages[] = array(
					"language" => $parsedLang,
					"url"      => $redirectUrl
				);
			}
		}
	}

	/**
	 * Set the default redirect URL used when the set languages are not enough.
	 * 
	 * @param String $url The default url
	 *
	 * @return void
	 */
	public function setDefaultUrl($url)
	{
		$this->defaultUrl = $url;
	}

	/**
	 * Get the redirect URL basing on the current language and the rules set
	 * 
	 * @return String The URL
	 */
	public function getRedirectUrl()
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$detectedLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$matches = array();

			// Look for all the languages that match as primary
			foreach ($detectedLangs as $lang) {
				$lang = $this->parseLanguage($lang);
				// Look for a similar language
				foreach ($this->languages as $entry) {
					if (strtolower($lang["primary"]) == strtolower($entry["language"]["primary"])) {
						// If this is the first language that matches as primary, just keep it
						// Or if this language is not the first to match, make sure that matches with the other ones already in the list
						if (count($matches) == 0 || $matches[0]["language"]["primary"] == $entry["language"]["primary"]) {
							$matches[] = $entry;
						}
					}
				}
			}

			// Look if there are some specific matches
			if (count($matches) > 0) {
				foreach ($detectedLangs as $lang) {
					$lang = $this->parseLanguage($lang);
					// Look for the very same language
					foreach ($matches as $entry) {
						if (strtolower($lang["primary"]) == strtolower($entry["language"]["primary"]) && strtolower($lang["subtag"]) == strtolower($entry["language"]["subtag"])) {
							return $entry["url"];
						}
					}
				}
				return $matches[0]["url"];
			}
		}

		// Or return the default url
		return $this->defaultUrl;
	}

	/**
	 * Execute the redirect basing on the specified languages
	 * 
	 * @return bool True if the redirect is being made
	 */
	public function redirect()
	{
		$url = $this->getRedirectUrl();
		if (strlen($url)) {
			echo "<script>window.top.location.href='" . $url . "';</script>";
			return true;
		}
		return false;
	}

	/**
	 * Parse a string (RFC 2616) and convert it to a language array
	 * @param  String $languageString The string to be parsed
	 * @return array                  An associative array containing the language data or false on error
	 */
	private function parseLanguage($languageString)
	{
		$pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})' . '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)' . '(?P<quantifier>\d\.\d))?$/';		
		if (preg_match($pattern, $languageString, $splits)) {
			return array(
				"primary"    => $splits["primarytag"],
				"subtag"     => isset($splits["subtag"]) ? $splits["subtag"] : "",
				"quantifier" => isset($splits["quantifier"]) ? $splits["quantifier"] : ""
			);
		}
		return false;
	}

}



/**
 * @summary
 * Provides a set of useful methods for managing the private area, the users and the accesses.
 * To use it, you must include __x5engine.php__ in your code.
 *
 * @description Create a new ImDb Object
 *
 * @class
 * @constructor
 */
class imPrivateArea
{
    private const CNG_PWD_TOKEN_LENGTH = 32;
    private const CNG_PWD_TOKEN_EXPIRE_TIME = 86400; //1 day

    public $admin_email;

    private $session_type;
    private $session_email;
    private $session_real_name;
    private $session_first_name;
    private $session_last_name;
    private $session_uid;
    private $session_gids;
    private $session_page;
    private $session_change_pwd_request;
    private $cookie_name;
    private $salt;

    function __construct()
    {
        $imSettings = Configuration::getSettings();

        $this->session_type       = "im_access_utype";
        $this->session_email      = "im_access_email";
        $this->session_real_name  = "im_access_real_name";
        $this->session_first_name = "im_access_first_name";
        $this->session_last_name  = "im_access_last_name";
        $this->session_page       = "im_access_request_page";
        $this->session_change_pwd_request = 'im_access_change_pwd_request';
        $this->session_uid        = "im_access_uid";
        $this->session_gids       = "im_access_gids";
        $this->cookie_name        = "im_access_cookie_uid";
        $this->salt               = $imSettings['general']['salt'];
    }

    /**
     * Login a user with username and password
     *
     * @param {string} $email Email (or username on fallback)
     * @param {string} $pwd   Password
     *
     * @return {int} An error code:
     *                  -5 if the user email is not validated,
     *                  -2 if the username or password are invalid,
     *                  -1 if there's a db error,
     *                  0 if the process exits correctly
     */
    public function login($email, $pwd)
    {
        if (!strlen($email) || !strlen($pwd))
            return -2;

        $user = $this->getUser($email, $pwd);
        if (is_array($user)) {
            if (!$user['validated']) {
                return -5;
            } else if ($this->_setLoggedUser($user)) {
                return 0;
            }
        }
        return -2;
    }

    public function getUser($username, $pwd)
    {
        $user = $this->getUserByUsername($username);
        if ($user && self::_check_password($pwd, $user['password'], $user['crypt_encoding'])) {
            return $user;
        }
        return false;
    }

    private static function _encode_password($pwd){
        return self::_password_crypt_encoding()['encode']($pwd);
    }

    private static function _check_password($pwd, $encoded, $encoding_id){
        return self::_password_crypt_encoding($encoding_id)['check']($pwd, $encoded);
    }

    private static function _password_crypt_encoding($encoding_id = null){
        $imSettings = Configuration::getSettings();
        return isset( $imSettings['access']['password_crypt']['encodings'][$encoding_id]) ?  $imSettings['access']['password_crypt']['encodings'][$encoding_id] 
            :  $imSettings['access']['password_crypt']['encodings'][$imSettings['access']['password_crypt']['encoding_id']];
    }

    public function encrypt_DB_passwords($no_crypt_encoding_id)
    {
        $encoding_id = Configuration::getSettings()['access']['password_crypt']['encoding_id'];
        $select = $this->db->select(array(
            "select" => array("id", "password"),
            "from" => $this->db_table,
            "where_flat" => "crypt_encoding = '" . $no_crypt_encoding_id . "' OR crypt_encoding = '' OR crypt_encoding is NULL"
        ));
        if (!is_bool($select)) {
            foreach ($select as $info) {
                $this->db->update(array(
                    "update" => $this->db_table,
                    "set" => array(
                        "password" => self::_encode_password($info['password']),
                        "crypt_encoding" => $encoding_id
                    ),
                    "where" => array('id' => $info['id'])
                ));
            }
        }
    }

    /**
     * Logout a user
     *
     * @return {Void}
     */
    public function logout()
    {
        $_SESSION[$this->session_type]  = "";
        $_SESSION[$this->session_email] = "";
        $_SESSION[$this->session_uid]   = "";
        $_SESSION[$this->session_page]  = "";
        $_SESSION[$this->session_gids]  = array();
        $_SESSION['HTTP_USER_AGENT']    = "";
        im_set_cookie($this->cookie_name, "", time() - 3600, "/");
        $_COOKIE[im_cookie_name($this->cookie_name)]    = "";
    }

    /**
     * Save the current page as the referer
     *
     * @param {String} $page The page to store
     *
     * @return {Void}
     */
    public function savePage($page = "")
    {
        $url = strlen($page) ? $page : $_SERVER['REQUEST_URI'];
        $_SESSION[$this->session_page] = $this->_encode($url, $this->salt);
    }

    /**
     * Clear the current saved page
     */
    public function clearSavedPage()
    {
        $_SESSION[$this->session_page] = "";
    }

    /**
     * Return the referer page name (the one which caused the user to land on the login page).
     *
     * @method getSavedPage
     *
     * @return {mixed} The name of the page or false if no referer is available.
     */
    public function getSavedPage()
    {
        $imSettings = Configuration::getSettings();
        if (isset($_SESSION[$this->session_page]) && $_SESSION[$this->session_page] != "")
            return $this->_decode($_SESSION[$this->session_page], $this->salt);
        return false;
    }

    /**
     * Use whoIsLogged instead
     *
     * @deprecated
     *
     * @return {mixed}
     */
    public function who_is_logged()
    {
        return $this->whoIsLogged();
    }

    /**
     * Get an array of data about the logged user
     *
     * @return {mixed} An array containing the data of the current logged user or false if no user is logged.
     */
    public function whoIsLogged()
    {
        if (isset($_SESSION[$this->session_email]) && $_SESSION[$this->session_email] != "" && isset($_SESSION[$this->session_email])) {
            $email = $this->_decode($_SESSION[$this->session_email], $this->salt);
            $firstname = trim($this->_decode($_SESSION[$this->session_first_name], $this->salt));
            $lastname = trim($this->_decode($_SESSION[$this->session_last_name], $this->salt));
            $uid = isset($_SESSION[$this->session_uid]) && $_SESSION[$this->session_uid] != "" ? $this->_decode($_SESSION[$this->session_uid], $this->salt) : "";
            $groups = isset($_SESSION[$this->session_gids]) ? $_SESSION[$this->session_gids] : "";
            return array(
                "email"     => $email,
                "uid"       => $uid,
                "firstname" => $firstname,
                "lastname"  => $lastname,
                "groups"    => $groups,
                // Compatibility mode for the old code snippets
                "realname"  => strlen($firstname) || strlen($lastname) ? trim($firstname . " " . $lastname) : $email,
                "username"  => $email
            );
        }
        return false;
    }

    /**
     * Check if the logged user can access to a specific page.
     * The page is provided using its page id.
     *
     * @param {int} $page The page id. You can retrieve the page id from the file x5settings.php.
     *
     * @return {int} 0 if the current user can access the page,
     *                 -2 if the XSS security checks are not met
     *                 -3 if the user is not logged
     *                 -4 if the user is still not validated
     *                 -8 if the user cannot access the page
     */
    public function checkAccess($page)
    {
        $imSettings = Configuration::getSettings();

        //
        // The session can live only in the same browser
        //

        if (!isset($_SESSION[$this->session_type]) || $_SESSION[$this->session_type] == "" || !isset($_SESSION[$this->session_uid]) || $_SESSION[$this->session_uid] == "")
            return -3;

        if (!isset($_SESSION['HTTP_USER_AGENT']) || $_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'] . $this->salt))
            return -2;

        $uid = $this->_decode($_SESSION[$this->session_uid], $this->salt);
        if ((!isset($imSettings['access']['pages'][$page]) || !@in_array($uid, $imSettings['access']['pages'][$page])) && !@in_array($uid, $imSettings['access']['admins']))
            return -8; // The active user cannot access to this page
        return 0;
    }

    /**
     * Get the current user's landing page.
     *
     * @return {mixed} The filename of the user's landing page or false if the
     * user is not logged or has no landing page.
     */
    public function getLandingPage()
    {
        $imSettings = Configuration::getSettings();
        if (!isset($_SESSION[$this->session_type]) || !isset($_SESSION[$this->session_email]) || $_SESSION[$this->session_email] === '' || !isset($_SESSION[$this->session_uid]) || $_SESSION[$this->session_uid] === '')
            return false;


        // WSX5-845: The registration page can be activated or deactivated
        // The following may return boolean value 'false' if the user has no
        // active landing page
        $session_email = $this->_decode($_SESSION[$this->session_email], $this->salt);
        if (isset($imSettings['access']['users'][$session_email])) {
            return $imSettings['access']['users'][$session_email]['page'];
        }
        return false;
    }

    /**
     * Convert a status code to a text message
     *
     * @param {int} $code The error code
     *
     * @return {string} The text message related to the provided error code
     */
    public function messageFromStatusCode($code)
    {
        $imSettings = Configuration::getSettings();

        switch ($code) {

            // Error
            case -12 : return l10n("private_area_password_recovery_not_equals_passwords", "Confirmation password is different from password.");
            case -11 : return l10n("private_area_password_recovery_expired_token", "Your change password request has expired, do it again.");
            case -10 : return l10n("private_area_password_recovery_bad_request", "Bad Request!");
            case -9 :
            $reason = l10n("form_password", "The password entered must comply with the following rules:");
            $reason = $reason . "<br>" . str_replace("{0}", $imSettings['password_policy']['minimum_characters'], l10n("form_password_length", "- at least {0} characters"));
            if($imSettings['password_policy']['include_uppercase']){
                $reason = $reason . "<br>" . l10n("form_password_upper", "- at least one capital letter (A-Z)");
            }
            if($imSettings['password_policy']['include_numeric']){
                $reason = $reason . "<br>" . l10n("form_password_numeric", "- at least one number (0-9)");
            }
            if($imSettings['password_policy']['include_special']){
                $reason = $reason . "<br>" . l10n("form_password_special", "- at least one special character (!@#$%&*()<>?)");   
            }
            return $reason;
            case -8 : return l10n("private_area_account_not_allowed", "Your account is not allowed to access the selected page");
            case -7 : return l10n("private_area_lostpassword_error", "We cannot find your data.");
            case -6 : return l10n("private_area_user_already_exists", "The user already exists.");
            case -5 : return l10n("private_area_not_validated", "Your account is not yet validated.");
            case -4 : return l10n("private_area_waiting", "Your account is not yet active.");
            //case -3 : return l10n("private_area_not_allowed", "A login is required to access this page.");
            case -2 : return l10n("private_area_login_error", "Wrong username or password.");
            case 0 : case -1 : return l10n("private_area_generic_error", "Generic error.");

            // Success
            case 1  : return l10n('private_area_login_success', "Login succesful.");
            case 2  : return l10n('private_area_validation_sent', 'We sent you a validation email.');
            case 3  : return l10n('private_area_registration_success', 'You are now registered.');
            case 4  : return l10n('private_area_lostpassword_success', 'We sent you an email with your password.');

            default : return "";
        }
    }

    /**
     * Redirect in a session safe mode. IIS requires this.
     *
     * @ignore
     *
     * @param  {string} $to The redirect URL.
     *
     * @return {void}
     */
    public function sessionSafeRedirect($to)
    {
        exit('<!DOCTYPE html><html lang="it" dir="ltr"><head><title>Loading...</title><meta http-equiv="refresh" content="1; url=' . $to . '"></head><body><p style="text-align: center;">Loading...</p></body></html>');
    }

    /**
     * Get the data about a user.
     *
     * @param  {string} $id The username
     *
     * @return {mixed} The user's data (As associative array) or null if the user is not found.
     * The associative array contains the following keys: id, ts, ip, username, password, realname, email, key, validated, groups, hash
     */
    public function getUserByUsername($username)
    {
        $imSettings = Configuration::getSettings();
        if (isset($imSettings['access']['users'][$username])) {
            $user = $imSettings['access']['users'][$username];
            $email = isset($user['email']) ? $user['email'] : $username;
            $firstname = isset($user['firstname']) ? $user['firstname'] : '';
            $lastname = isset($user['lastname']) ? $user['lastname'] : '';
            $password = isset($user['password']) ? $user['password'] : '';
            $crypt_encoding = isset($user['crypt_encoding']) ? $user['crypt_encoding'] : '';
            return array(
                'user_type' => '1',
                'db_stored' => $user['db_stored'],
                "id"        => $user['id'],
                "ts"        => "",
                "ip"        => "",
                "username"  => $username,
                "email"     => $email,
                "password"  => $password,
                "firstname" => $firstname,
                "lastname"  => $lastname,
                // Compatibility mode for old code snippets
                "realname"  => isset($user['realname']) ? $user['realname'] : trim($firstname . " " . $lastname),
                "key"       => "",
                "validated" => true,
                "groups"    => $user['groups'],
                "crypt_encoding" => $crypt_encoding
            );
        }
        return null;
    }
    

    /**
     * Encode the string
     *
     * @ignore
     *
     * @param  string $string The string to encode
     * @param  $key The encryption key
     *
     * @return string    The encoded string
     */
    private function _encode($s, $k)
    {
        if (strlen($s) == 0) {
            return "";
        }

        $r = array();
        for($i = 0; $i < strlen($s); $i++) {
            $r[] = ord($s[$i]) + ord($k[$i % strlen($k)]);
        }

        // Try to encode it using base64
        if (function_exists("base64_encode") && function_exists("base64_decode")) {
            return base64_encode(implode('.', $r));
        }

        return implode('.', $r);
    }

    /**
     * Decode the string
     *
     * @ignore
     *
     * @param  string $s The string to decode
     * @param  string $k The encryption key
     *
     * @return string    The decoded string
     */
    private function _decode($s, $k)
    {
        if (strlen($s) == 0) {
            return "";
        }

        // Try to decode it using base64
        if (function_exists("base64_encode") && function_exists("base64_decode")) {
            $s = base64_decode($s);
        }

        $s = explode(".", $s);
        $r = array();
        for($i = 0; $i < count($s); $i++) {
            $r[$i] = chr($s[$i] - ord($k[$i % strlen($k)]));
        }
        return implode('', $r);
    }

    /**
     * Setup a simple session that does not allow to login but rather contains
     * some useful data about the user.
     *
     * This is used mainly to provide some informations about the user
     * once registration but BEFORE validation and login.
     * This data can be retrieve using $this->whoIsLogged();
     *
     * @ignore
     *
     * @param String $email
     * @param String $firstname
     * @param String $lastname
     */
    private function _setSimpleSession($email, $firstname, $lastname)
    {
        $_SESSION[$this->session_email]      = $this->_encode($email, $this->salt);
        $_SESSION[$this->session_first_name] = $this->_encode($firstname, $this->salt);
        $_SESSION[$this->session_last_name]  = $this->_encode($lastname, $this->salt);
    }

    /**
     * Set the session after the login
     *
     * @ignore
     *
     * @param {string} $type "0" or "1"
     * @param {string} $uid
     * @param {array}  $gids
     * @param {string} $email
     * @param {string} $firstname
     * @param {string} $lastname
     *
     * @return {Void}
     */
    private function _setSession($type, $uid, $gids, $email, $firstname, $lastname)
    {
        @session_regenerate_id();
        $this->_setSimpleSession($email, $firstname, $lastname);
        $_SESSION[$this->session_type]       = $this->_encode($type, $this->salt);
        $_SESSION[$this->session_uid]        = $this->_encode($uid, $this->salt);
        $_SESSION[$this->session_gids]       = $gids;
        $_SESSION['HTTP_USER_AGENT']         = md5($_SERVER['HTTP_USER_AGENT'] . $this->salt);
        im_set_cookie($this->cookie_name, $this->_encode($uid, $this->salt), 0, "/"); // Expires when the browser is closed
    }

    private function _setLoggedUser($user) : bool
    {
        $this->_setSession(
            $user['user_type'],
            $user['id'],
            $user['groups'],
            $user['email'],
            $user['firstname'],
            $user['lastname']
        );
        return true;
    }
}


/**
 * Contains the methods used by the search engine
 * @access public
 */
class imSearch {

    var $scope;
    var $page;
    var $results_per_page;

    function __construct()
    {
        $this->setScope();
        $this->results_per_page = 10;
        if (function_exists("mb_internal_encoding")) {
            mb_internal_encoding("UTF-8");
        }
    }

    /**
     * Loads the pages defined in search.inc.php to the search scope
     * @access public
     */
    function setScope()
    {
        global $imSettings;
        $scope = $imSettings['search']['general']['defaultScope'];

        // Logged users can search in their private pages
        $pa = new imPrivateArea();
        if ($user = $pa->who_is_logged()) {
            foreach ($imSettings['search']['general']['extendedScope'] as $key => $value) {
                if (isset($imSettings['access']['pages'][$key]) && in_array($user['uid'], $imSettings['access']['pages'][$key]))
                    $scope[] = $value;
            }
        }

        $this->scope = $scope;
    }

    /**
     * Do the pages search
     * @access public
     * @param queries The search query (array)
     */
    function searchPages($queries)
    {        
        global $imSettings;
        $html = "";
        $found_content = array();
        $found_count = array();

        if (is_array($this->scope)) {
            foreach ($this->scope as $filename) {
                $count = 0;
                $weight = 0;
                $file_content = @implode("\n", file($filename));
                // Replace the nonbreaking space with a white space
                // to avoid that is converted to a 196+160 UTF8 char
                $file_content = str_replace("&nbsp;", " ", $file_content);
                if (function_exists("html_entity_decode"))
                    $file_content = html_entity_decode($file_content, ENT_COMPAT, 'UTF-8');

                // Remove contents wrapped between "<!-- UNSEARCHABLE --><!-- UNSEARCHABLE END -->" comments
                while (stristr($file_content, "<!-- UNSEARCHABLE -->") !== false) {
                    $unsearchable_start = stripos($file_content, "<!-- UNSEARCHABLE -->");
                    $unsearchable_end = stripos($file_content, "<!-- UNSEARCHABLE END -->", $unsearchable_start) + strlen("<!-- UNSEARCHABLE END -->");
                    $unsearchable = substr($file_content, $unsearchable_start, $unsearchable_end - $unsearchable_start);
                    $file_content = str_replace($unsearchable, "", $file_content);
                }

                // Remove the breadcrumbs
                while (stristr($file_content, "<div id=\"imBreadcrumb\"") !== false) {
                    $imbreadcrumb_start = stripos($file_content, "<div id=\"imBreadcrumb\"");
                    $imbreadcrumb_end = stripos($file_content, "</div>", $imbreadcrumb_start) + strlen("</div>");
                    $imbreadcrumb = substr($file_content, $imbreadcrumb_start, $imbreadcrumb_end - $imbreadcrumb_start);
                    $file_content = str_replace($imbreadcrumb, "", $file_content);
                }

                // Remove CSS
                while (stristr($file_content, "<style") !== false) {
                    $style_start = stripos($file_content, "<style");
                    $style_end = stripos($file_content, "</style>", $style_start) + strlen("</style>");
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove JS
                while (stristr($file_content, "<script") !== false) {
                    $script_start = stripos($file_content, "<script");
                    $script_end = stripos($file_content, "</script>", $script_start) + strlen("</script>");
                    $script = substr($file_content, $script_start, $script_end - $script_start);
                    $file_content = str_replace($script, "", $file_content);
                }

                // Remove noscript tag
                while (stristr($file_content, "<noscript") !== false) {
                    $noscript_start = stripos($file_content, "<noscript");
                    $noscript_end = stripos($file_content, "</noscript>", $noscript_start) + strlen("</noscript>");
                    $noscript = substr($file_content, $noscript_start, $noscript_end - $noscript_start);
                    $file_content = str_replace($noscript, "", $file_content);
                }

                // Remove the hidden spans
                while (stristr($file_content, "<span class=\"imHidden\"") !== false) {
                    $imhidden_start = stripos($file_content, "<span class=\"imHidden\"");
                    $imhidden_end = stripos($file_content, "</span>", $imhidden_start) + strlen("</span>");
                    $imhidden = substr($file_content, $imhidden_start, $imhidden_end - $imhidden_start);
                    $file_content = str_replace($imhidden, "", $file_content);
                }

                // Remove PHP
                while (stristr($file_content, "<?php") !== false) {
                    $php_start = stripos($file_content, "<?php");
                    $php_end = stripos($file_content, "?>", $php_start) !== false ? stripos($file_content, "?>", $php_start) + 2 : strlen($file_content);
                    $php = substr($file_content, $php_start, $php_end - $php_start);
                    $file_content = str_replace($php, "", $file_content);
                }

                // Get the title of the page
                $file_titles = array();
                if (preg_match_all('/\<(?:title|header[^\>]*\>[^\<]*\<(h2|h1|div)[^\>]*)\>([^\<]*)\<\/(?:title|\1)\>/', $file_content, $matches, PREG_PATTERN_ORDER)) {
                    $file_titles = $matches[2];
                }

                foreach ($file_titles as $file_title) {
                    foreach ($queries as $query) {
                        $title = imstrtolower($file_title);
                        while (($title = imstristr($title, $query)) !== false) {
                            $weight += 3;
                            $count++;
                            $title = imsubstr($title, imstrlen($query));
                        }
                    }
                }

                // Get the keywords
                preg_match('/\<meta name\=\"keywords\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
                if (count($matches) > 1) {
                    $keywords = $matches[1];
                    foreach ($queries as $query) {
                        $tkeywords = imstrtolower($keywords);
                        while (($tkeywords = imstristr($tkeywords, $query)) !== false) {
                            $weight += 4;
                            $count++;
                            $tkeywords = imsubstr($tkeywords, imstrlen($query));
                        }
                    }
                }

                // Get the description
                preg_match('/\<meta name\=\"description\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
                if (count($matches) > 1) {
                    $keywords = $matches[1];
                    foreach ($queries as $query) {
                        $tkeywords = imstrtolower($keywords);
                        while (($tkeywords = imstristr($tkeywords, $query)) !== false) {
                            $weight += 3;
                            $count++;
                            $tkeywords = imsubstr($tkeywords, imstrlen($query));
                        }
                    }
                }

                $page_pos = strpos($file_content, "<main id=\"imContent\">") + imstrlen("<main id=\"imContent\">");
                if ($page_pos == false)
                    $page_pos = strpos($file_content, "<body>") + strlen("<body>");
                $page_end = strpos($file_content, "</main>") + strlen("</main>");
                if ($page_end == false)
                    $page_end = strpos($file_content, "</body>") + strlen("</body>");
                $file_content = strip_tags(substr($file_content, $page_pos, $page_end-$page_pos));
                $t_file_content = imstrtolower($file_content);

                foreach ($queries as $query) {
                    $file = $t_file_content;
                    while (($file = imstristr($file, $query)) !== false) {
                        $count++;
                        $weight++;
                        $file = imsubstr($file, imstrlen($query));
                    }
                }

                if ($count > 0) {
                    $found_count[$filename] = $count;
                    $found_weight[$filename] = $weight;
                    $found_content[$filename] = $file_content;
                    if (count($file_titles) == 0)
                        $found_title[$filename] = $filename;
                    else
                        $found_title[$filename] = $file_titles[0];
                }
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_weight as $name => $weight) {
                $count = $found_count[$name];
                $i++;
                if (($i > $this->page * $this->results_per_page) && ($i <= ($this->page + 1) * $this->results_per_page)) {
                    $title = strip_tags($found_title[$name]);
                    $file = $found_content[$name];
                    $file = strip_tags($file);
                    $ap = 0;
                    $filelen = imstrlen($file);
                    $text = "";
                    for ($j=0; $j < ($count > 6 ? 6 : $count); $j++) {
                        $minpos = $filelen;
                        $word = "";
                        foreach ($queries as $query) {
                            if ($ap < $filelen && ($pos = imstrpos(imstrtolower($file), imstrtolower($query), $ap)) !== false) {
                                if ($pos < $minpos) {
                                    $minpos = $pos;
                                    $word = $query;
                                }
                            }
                        }
                        $prev = explode(" ", imsubstr($file, $ap, $minpos-$ap));
                        if (count($prev) > ($ap > 0 ? 9 : 8))
                            $prev = ($ap > 0 ? implode(" ", array_slice($prev, 0, 8)) : "") . " ... " . implode(" ", array_slice($prev, -8));
                        else
                            $prev = implode(" ", $prev);
                        if (imstrlen($word)) {
                            $text .= $prev . "<strong>" . imsubstr($file, $minpos, imstrlen($word)) . "</strong>";
                            $ap = $minpos + imstrlen($word);
                        }
                    }
                    $next = explode(" ", imsubstr($file, $ap));
                    if (count($next) > 9)
                        $text .= implode(" ", array_slice($next, 0, 8)) . "...";
                    else
                        $text .= implode(" ", $next);
                    $text = str_replace("|", "", $text);
                    $text = str_replace("<br />", " ", $text);
                    $text = str_replace("<br>", " ", $text);
                    $text = str_replace("\n", " ", $text);
                    $text = str_replace("\t", " ", $text);
                    $text = trim($text);
                    $html .= "<div class=\"imSearchPageResult\"><h3><a class=\"imCssLink\" href=\"" . $name . "\">" . strip_tags($title, "<b><strong>") . "</a></h3>" . strip_tags($text, "<b><strong>") . "<div class=\"imSearchLink\"><a class=\"imCssLink\" href=\"" . $name . "\">" . (imsubstr($imSettings['general']['url'], -1) != "/" ? $imSettings['general']['url'] . "/" : $imSettings['general']['url']) . $name . "</a></div></div>\n";
                }
            }
            $html = preg_replace_callback('/\\s+/', function ($matches) {
                return implode(' ', $matches);
            }, $html);
            $html .= "<div class=\"imSLabel\">&nbsp;</div>\n";
        }

        return array("content" => $html, "count" => count($found_content));
    }

    /**
     * Do the blog posts search
     * @access public
     * @param queries The search query (array)
     */
    function searchBlog($queries)
    {
        global $imSettings;
        $html = "";
        $found_content = array();
        $found_count = array();

        if (isset($imSettings['blog']) && is_array($imSettings['blog']['posts'])) {
            foreach ($imSettings['blog']['posts'] as $key => $value) {
                // WSXELE-799: Skip the post that are published in the future
                if ($value['utc_time'] > time()) {
                    continue;
                }
                $count = 0;
                $weight = 0;
                $qs = isset($value['slug']) ? $value['slug'] : ('id=' . $key);
                $filename = 'blog/index.php?' . $qs;
                $file_content = $value['body'];

                // Rimuovo le briciole dal contenuto
                while (stristr($file_content, "<div id=\"imBreadcrumb\"") !== false) {
                    $imbreadcrumb_start = stripos($file_content, "<div id=\"imBreadcrumb\"");
                    $imbreadcrumb_end = stripos($file_content, "</div>", $imbreadcrumb_start) + strlen("</div>");
                    $imbreadcrumb = substr($file_content, $imbreadcrumb_start, $imbreadcrumb_end - $imbreadcrumb_start);
                    $file_content = str_replace($imbreadcrumb, "", $file_content);
                }

                // Rimuovo gli stili dal contenuto
                while (stristr($file_content, "<style") !== false) {
                    $style_start = stripos($file_content, "<style");
                    $style_end = stripos($file_content, "</style>", $style_start) + strlen("</style>");
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Rimuovo i JS dal contenuto
                while (stristr($file_content, "<script") !== false) {
                    $script_start = stripos($file_content, "<script");
                    $script_end = stripos($file_content, "</script>", $script_start) + strlen("</script>");
                    $script = substr($file_content, $script_start, $script_end - $script_start);
                    $file_content = str_replace($script, "", $file_content);
                }

                foreach ($queries as $query) {
                    $queryRegex = '/' . preg_quote($query, '/') . '/';
                    // Conto il numero di match nei titoli
                    if (($t_count = preg_match_all($queryRegex, imstrtolower($value['title']), $matches))) {
                        $weight += ($t_count * 3);
                        $count += $t_count;
                    }
                    // tag_description
                    if (preg_match($queryRegex, $value['tag_description']) === 1) {
                        $count++;
                        $weight += 2;
                    }
                    // keywords
                    if (preg_match($queryRegex, $value['keywords']) === 1) {
                        $count++;
                        $weight += 2;
                    }
                    // Conto il numero di match nei tag
                    if (in_array($query, $value['tag'])) {
                        $count++;
                        $weight += 4;
                    }
                    // Conto occorrenze nel contenuto
                    if (($t_count = preg_match_all($queryRegex, imstrtolower(strip_tags($file_content)), $matches))) {
                        $weight += $t_count;
                        $count += $t_count;
                    }
                }

                $title = "Blog &gt;&gt; " . $value['title'];

                if ($count > 0) {
                    $found_count[$filename] = $count;
                    $found_weight[$filename] = $weight;
                    $found_content[$filename] = $file_content;
                    $found_breadcrumbs[$filename] = "<div class=\"imBreadcrumb\" style=\"display: block; padding-bottom: 3px;\">";
                    if ($value['author'] != "" || $value['category'] != "") {
                        $found_breadcrumbs[$filename] .= l10n('blog_published') . " ";
                        if ($value['author'] != "") {
                            $found_breadcrumbs[$filename] .= l10n('blog_by') . " <strong>" . $value['author'] . "</strong> ";
                        }
                        if ($value['category'] != "") {
                            $found_breadcrumbs[$filename] .= l10n('blog_in') . " <a href=\"blog/index.php?category=" . urlencode(str_replace(' ', '_', $value['category'])) . "\" target=\"_blank\" rel=\"nofollow\">" . $value['category'] . "</a> ";
                        }
                        $found_breadcrumbs[$filename] .= "&middot; ";
                    }
                    $found_breadcrumbs[$filename] .= $value['timestamp'] . "</div>";
                    if ($title == "")
                        $found_title[$filename] = $filename;
                    else
                        $found_title[$filename] = $title;
                }
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_weight as $name => $weight) {
                $count = $found_count[$name];
                $i++;
                if (($i > $this->page * $this->results_per_page) && ($i <= ($this->page + 1) * $this->results_per_page)) {
                    $title = strip_tags($found_title[$name]);
                    $file = $found_content[$name];
                    $file = strip_tags($file);
                    $ap = 0;
                    $filelen = imstrlen($file);
                    $text = "";
                    for ($j = 0; $j < ($count > 6 ? 6 : $count); $j++) {
                        $minpos = $filelen;
                        $word = "";
                        foreach ($queries as $query) {
                            if ($ap < $filelen && ($pos = imstrpos(imstrtolower($file), imstrtolower($query), $ap)) !== false) {
                                if ($pos < $minpos) {
                                    $minpos = $pos;
                                    $word = $query;
                                }
                            }
                        }
                        $prev = explode(" ", imsubstr($file, $ap, $minpos-$ap));
                        if(count($prev) > ($ap > 0 ? 9 : 8))
                            $prev = ($ap > 0 ? implode(" ", array_slice($prev, 0, 8)) : "") . " ... " . implode(" ", array_slice($prev, -8));
                        else
                            $prev = implode(" ", $prev);
                        $text .= $prev . "<strong>" . imsubstr($file, $minpos, imstrlen($word)) . "</strong> ";
                        $ap = $minpos + imstrlen($word);
                    }
                    $next = explode(" ", imsubstr($file, $ap));
                    if(count($next) > 9)
                        $text .= implode(" ", array_slice($next, 0, 8)) . "...";
                    else
                        $text .= implode(" ", $next);
                    $text = str_replace("|", "", $text);
                    $html .= "<div class=\"imSearchBlogResult\"><h3><a class=\"imCssLink\" href=\"" . $name . "\">" . strip_tags($title, "<b><strong>") . "</a></h3>" . strip_tags($found_breadcrumbs[$name], "<b><strong>") . "\n" . strip_tags($text, "<b><strong>") . "<div class=\"imSearchLink\"><a class=\"imCssLink\" href=\"" . $name . "\">" . (imsubstr($imSettings['general']['url'], -1) != "/" ? $imSettings['general']['url'] . "/" : $imSettings['general']['url']) . $name . "</a></div></div>\n";
                }
            }
            echo "  <div class=\"imSLabel\">&nbsp;</div>\n";
        }

        $html = preg_replace_callback('/\\s+/', function ($matches) {
            return implode(' ', $matches);
        }, $html);
        return array("content" => $html, "count" => count($found_content));
    }

    /**
     * Do the products search
     * @access public
     * @param queries The search query (array)
     */
    function searchProducts($queries)
    {
       // Di questa funzione manca la paginazione!

        global $imSettings;
        $html = "";
        $found_products = array();
        $found_count = array();

        foreach ($imSettings['search']['products'] as $id => $product) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($product['name']));
            $t_description = strip_tags(imstrtolower($product['description']));
            $t_sku = strip_tags(imstrtolower($product['sku']));

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            // Conto il numero di match nello SKU
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_sku, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_products[$id] = $product;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_products as $id => $product) {
                $i++;
                if (($i > $this->page * $this->results_per_page) && ($i <= ($this->page + 1) * $this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchProductResult\">";
                    // Top row
                    $html .= "<div class=\"imProductImage\">";
                    $html .= $product['image'];
                    $html .= "</div>";
                    $html .= "<div class=\"imProductDescription\">";
                    $html .= "<div class=\"imProductTitle\">";
                    $html .= "<h3>" . $product['title_link'] . "</h3>";
                    $html .= "<span>" . $product['price'] . "<a class=\"imCssLink\" href=\"#\"  onclick=\"x5engine.cart.ui.addToCart('" . $id . "', 1);\" style=\"margin-left: 5px;\">" . l10n('cart_add') . "</a></span>";
                    $html .= "</div>";
                    $html .= "<p>" . strip_tags($product['description']) . "</p>";
                    $html .= "</div>";
                    // Close the container
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_products));
    }

    /**
     * Do the images search
     * @access public
     * @param queries The search query (array)
     */
    function searchImages($queries)
    {
        // Di questa funzione manca la paginazione! 

        global $imSettings;
        $id = 0;
        $html = "";
        $found_images = array();
        $found_count = array();

        foreach ($imSettings['search']['images'] as $image) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($image['title']));
            $t_description = strip_tags(imstrtolower($image['description']));

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella location
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($image['location']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 2);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_images[$id] = $image;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }

            $id++;
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_images as $id => $image) {
                $i++;
                if (($i > $this->page * $this->results_per_page) && ($i <= ($this->page + 1) * $this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchImageResult\">";
                    $html .= "<div class=\"imSearchImageResultContent\"><a href=\"" . $image['page'] . "\"><img src=\"" . $image['src'] . "\" alt=\"\" /></a></div>";
                    $html .= "<div class=\"imSearchImageResultContent\">";
                    $html .= "<h3>" . $image['title'];
                    if ($image['location'] != "")
                        $html .= "&nbsp;(" . $image['location'] . ")";
                    $html .= "</h3>";
                    $html .= strip_tags($image['description']);
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_images));
    }

    /**
     * Do the videos search
     * @access public
     * @param queries The search query (array)
     */
    function searchVideos($queries)
    {
        // Di questa funzione manca la paginazione!

        global $imSettings;
        $id = 0;
        $found_count = array();
        $found_videos = array();
        $html = "";
        $month = 7776000;

        foreach ($imSettings['search']['videos'] as $video) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($video['title']));
            $t_description = strip_tags(imstrtolower($video['description']));

            // I video più recenti hanno maggiore peso in proporzione
            $time = strtotime($video['date']);
            $ago = strtotime("-3 months");
            if ($time - $ago > 0)
                $weight += 5 * max(0, ($time - $ago) / $month);

            // Conto il numero di match nei tag
            foreach ($queries as $query) {
                $t_count = preg_match_all('/\\s*' . preg_quote($query, '/') . '\\s*/', imstrtolower($video['tags']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 10);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella categoria
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($video['category']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 2);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query) . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_videos[$id] = $video;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }

            $id++;
        }

        if ($found_count) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_videos as $id => $video) {
                $i++;
                if (($i > $this->page * $this->results_per_page) && ($i <= ($this->page + 1) * $this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchVideoResult\">";
                    $html .= "<div class=\"imSearchVideoResultContent\"><a href=\"" . $video['page'] . "\"><img src=\"" . $video['src'] . "\" alt=\"\" /></a></div>";
                    $html .= "<div class=\"imSearchVideoResultContent\">";
                    $html .= "<h3>" . $video['title'];
                    if (!$video['familyfriendly'])
                        $html .= "&nbsp;<span style=\"color: red; text-decoration: none;\">[18+]</span>";
                    $html .= "</h3>";
                    $html .= strip_tags($video['description']);
                    if ($video['duration'] > 0) {
                        if (function_exists('date_default_timezone_set'))
                            date_default_timezone_set('UTC');
                        $html .= "<span class=\"imSearchVideoDuration\">" . l10n('search_duration') . ": " . date("H:i:s", $video['duration']) . "</span>";
                    }
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_videos));
    }

    /**
     * Start the site search
     * 
     * @param array  $keys The search keys as string (string)
     * @param string $page Page to show (integer)
     * @param string $type The content type to show
     *
     * @return void
     */
    function search($keys, $page, $type)
    {
        global $imSettings;

        $html = "";
        $content = "";
        $emptyResultsHtml = "<div style=\"margin-top: 15px; text-align: center; font-weight: bold;\">" . l10n('search_empty') . "</div>\n";

        $html .= "<div class=\"imPageSearchField\"><form method=\"get\" action=\"imsearch.php\" role=\"search\">";
        $html .= "<input value=\"" . htmlspecialchars($keys, ENT_COMPAT, 'UTF-8') . "\" type=\"text\" name=\"search\" aria-label=\"" . l10n('search_search') . "\" autofocus/>";
        $html .= "<input style=\"margin-left: 6px;\" type=\"submit\" value=\"" . l10n('search_search') . "\">";
        $html .= "</form></div>\n";

        // Exit if no search query was given
        if (trim($keys) == "" || $keys == null) {
            $html .= $emptyResultsHtml;
            return $html;
        }

        $search = trim(imstrtolower($keys));
        $this->page = $page;

        $queries = preg_split("/\s+/", $search);

        // Search everywhere to populate the results numbers shown in the sidebar menu
        // Pages
        $pages = $this->searchPages($queries);
        // Fallback on the selection if there are no pages
        if ($pages['count'] == 0 && $type == "pages")
            $type = "blog";

        // Blog
        if (isset($imSettings['blog']) && is_array($imSettings['blog']['posts']) && count($imSettings['blog']['posts']) > 0)
            $blog = $this->searchBlog($queries);
        else
            $blog = array("count" => 0);
        // Fallback on the selection if there is no blog
        if ($blog['count'] == 0 && $type == "blog")
            $type = "products";

        // Products
        if (is_array($imSettings['search']['products']) && count($imSettings['search']['products']) > 0)
            $products = $this->searchProducts($queries);
        else
            $products = array("count" => 0);
        // Fallback on the selection if there are no products
        if ($products['count'] == 0 && $type == "products")
            $type = "images";

        // Images
        if (is_array($imSettings['search']['images']) && count($imSettings['search']['images']) > 0)
            $images = $this->searchImages($queries);
        else
            $images = array("count" => 0);
        // Fallback on the selection if there are no images
        if ($images['count'] == 0 && $type == "images")
            $type = "videos";

        // Videos
        if (is_array($imSettings['search']['videos']) && count($imSettings['search']['videos']) > 0)
            $videos = $this->searchVideos($queries);
        else
            $videos = array("count" => 0);
        // Fallback on the selection if there are no videos
        if ($videos['count'] == 0 && $type == "videos")
            $type = "pages";            

        // Show only the requested content type
        switch ($type) {
            case "pages":
                if ($pages['count'] > 0)
                    $content .= "<div>" . $pages['content'] . "</div>\n";
                $results_count = $pages['count'];
                break;
            case "blog":
                if ($blog['count'] > 0)
                    $content .= "<div>" . $blog['content'] . "</div>\n";
                $results_count = $blog['count'];
                break;
            case "products":
                if ($products['count'] > 0)
                    $content .= "<div>" . $products['content'] . "</div>\n";
                $results_count = $products['count'];
                break;
            case "images":
                if ($images['count'] > 0)
                    $content .= "<div>" . $images['content'] . "</div>\n";
                $results_count = $images['count'];
                break;
            case "videos":
                if ($videos['count'] > 0)
                    $content .= "<div>" . $videos['content'] . "</div>\n";
                $results_count = $videos['count'];
                break;
        }

        // Exit if there are no results
        if (!$results_count) {
            $html .= $emptyResultsHtml;
            return $html;
        }

        $sidebar = "<ul>\n";
        if ($pages['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=pages\">" . l10n('search_pages') . " (" . $pages['count'] . ")</a></span></li>\n";
        if ($blog['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=blog\">" . l10n('search_blog') . " (" . $blog['count'] . ")</a></span></li>\n";
        if ($products['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=products\">" . l10n('search_products') . " (" . $products['count'] . ")</a></span></li>\n";
        if ($images['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=images\">" . l10n('search_images') . " (" . $images['count'] . ")</a></span></li>\n";
        if ($videos['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=videos\">" . l10n('search_videos') . " (" . $videos['count'] . ")</a></span></li>\n";
        $sidebar .= "</ul>\n";

        $html .= "<div id=\"imSearchResults\">\n";
        $html .= "\t<div id=\"imSearchSideBar\">" . $sidebar . "</div>\n";
        $html .= "\t<div id=\"imSearchContent\">" . $content . "</div>\n";
        $html .= "</div>\n";

        // Pagination
        if ($results_count > $this->results_per_page) {
            $html .= "<div style=\"text-align: center; clear: both;\">";
            // Back
            if ($page > 0) {
                $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page - 1) . "&type=" . $type . "\" aria-label=\"" . l10n("cmn_pagination_prev", "Previous") . "\">&lt;&lt;</a>&nbsp;";
            }

            // Central pages
            $start = max($page - 5, 0);
            $end = min($page + 10, ceil($results_count / $this->results_per_page));

            for ($i = $start; $i < $end; $i++) {
                if ($i != $this->page)
                    $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . $i . "&type=" . $type . "\">" .
                             "<span class=\"screen-reader-only-even-focused\">" . l10n("cmn_pagination_go_to_page", "Go to page:") . "</span>" . ($i + 1) . "</a>&nbsp;";
                else
                    $html .= "<span class=\"screen-reader-only-even-focused\">" . l10n("cmn_pagination_current_page", "Current page:") . "</span>" . ($i + 1) . "&nbsp;";
            }

            // Next
            if ($results_count > ($page + 1) * $this->results_per_page) {
                $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page + 1) . "&type=" . $type . "\" aria-label=\"" . l10n("cmn_pagination_next", "Next") . "\">&gt;&gt;</a>";
            }
            $html .= "</div>";
        }

        return $html;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
require_once("PHPMailerAutoload.php");

/**
 * Contains the methods used to style and send emails
 * @access public
 */
class ImSendEmail
{

    var $header;
    var $footer;
    var $bodyBackground;
    var $bodyBackgroundEven;
    var $bodyBackgroundOdd;
    var $bodyBackgroundBorder;
    var $bodyTextColorOdd;
    var $bodySeparatorBorderColor;
    var $emailBackground;
    var $emailContentStyle;
    var $emailContentFontFamily;
    var $emailType = "html";
    var $pathToRoot = "../";
    var $use_smtp = false;
    var $use_smtp_auth = false;
    var $smtp_host;
    var $smtp_port;
    var $smtp_username;
    var $smtp_password;
    var $smtp_encryption = 'none';

    /**
     * Apply the CSS style to the HTML code
     * @param  string $html The HTML code
     * @return string       The styled HTML code
     */
    function styleHTML($html)
    {
        $html = str_replace("[email:contentStyle]", $this->emailContentStyle, $html);
        $html = str_replace("[email:contentFontFamily]", $this->emailContentFontFamily, $html);
        $html = str_replace("[email:bodyBackground]", $this->bodyBackground, $html);
        $html = str_replace("[email:bodyBackgroundBorder]", $this->bodyBackgroundBorder, $html);
        $html = str_replace("[email:bodyBackgroundOdd]", $this->bodyBackgroundOdd, $html);
        $html = str_replace("[email:bodyBackgroundEven]", $this->bodyBackgroundEven, $html);
        $html = str_replace("[email:bodyTextColorOdd]", $this->bodyTextColorOdd, $html);
        $html = str_replace("[email:bodySeparatorBorderColor]", $this->bodySeparatorBorderColor, $html);
        $html = str_replace("[email:emailBackground]", $this->emailBackground, $html);
        return $html;
    }

    /**
     * Send an email
     *
     * @param string $sender      Envelope sender address
     * @param string $from        Self explanatory
     * @param string $to          Self explanatory
     * @param string $subject     Self explanatory
     * @param string $text        Self explanatory
     * @param string $html        Self explanatory
     * @param array  $attachments Self explanatory
     *
     * @return boolean
     */
    function send($sender = "", $from = "", $to = "", $subject = "", $text = "", $html = "", $attachments = array())
    {
        global $imSettings;
        /*
        |--------------
        |  PHPMailer
        |--------------
         */
        if ($this->emailType == 'phpmailer') {
            $email = new PHPMailer(false);
            // SMTP support
            if ($this->use_smtp) {
                $email->isSMTP();
                $email->Host = $this->smtp_host;
                $email->Port = $this->smtp_port;
                if ($this->smtp_encryption != 'none') {
                    $email->SMTPSecure = $this->smtp_encryption;
                }
                $email->SMTPAuth = $this->use_smtp_auth;
                if ($this->use_smtp_auth) {
                    $email->Username = $this->smtp_username;
                    $email->Password = $this->smtp_password;
                }
            }
            // Meta
            $email->CharSet = 'UTF-8'; // WSXELE-1067: Force UTF-8
            $email->Subject = $subject;

            if ($from != null && $from != "") {
                if (isset($imSettings['general']['enable_sender_header']) && $imSettings['general']['enable_sender_header']) {
                    $email->From = addressFromEmail($from);
                    $email->FromName = nameFromEmail($from);
                    $email->Sender = $sender;
                }
                else {
                    $email->From = addressFromEmail($sender);
                    $email->FromName = nameFromEmail($sender);
                    if (addressFromEmail($sender) !== addressFromEmail($from)) {
                        $email->addReplyTo($from);
                    }
                }
            }
            else {
                $email->From = $sender;
            }

            // WSXELE-1120: Split the email addresses if necessary
            $to = str_replace(";", ",", $to); // Make sure it works for both "," and ";" separators
            foreach (explode(",", $to) as $addr) {
                // WSXELE-1157: Provide support for the format John Doe <johndoe@email.com>
                $email->addAddress(addressFromEmail($addr), nameFromEmail($addr));
            }
            // Content
            $email->isHTML(true);
            $email->Body = $this->header . $this->styleHTML($html) . $this->footer;
            $email->AltBody = $text;
            // Attachments
            foreach ($attachments as $file) {
                if (isset($file['name']) && isset($file['content']) && isset($file['mime'])) {
                    $email->addStringAttachment($file['content'], $file['name'], 'base64', $file['mime'], 'attachment');
                }
            }
            if (!$email->send()) {
                $this->registerLog($email->ErrorInfo);
                return false;
            }
            return true;
        }

        /*
        |--------------
        |  WSX5 class
        |--------------
         */

        $email = new imEMail($sender, $from, $to, $subject, "utf-8", isset($imSettings['general']['enable_sender_header']) && $imSettings['general']['enable_sender_header']);
        $email->setText($text);
        $email->setHTML($this->header . $this->styleHTML($html) . $this->footer);
        $email->setStandardType($this->emailType);
        foreach ($attachments as $a) {
            if (isset($a['name']) && isset($a['content']) && isset($a['mime'])) {
                $email->attachFile($a['name'], $a['content'], $a['mime']);
            }
        }
        if (!$email->send()) {
            $this->registerLog("Cannot send email with internal script");
            return false;
        }
        return true;
    }

    /**
     * Restore some special chars escaped previously in WSX5
     *
     * @param string $str The string to be restored
     *
     * @return string
     */
    function restoreSpecialChars($str)
    {
        $str = str_replace("{1}", "'", $str);
        $str = str_replace("{2}", "\"", $str);
        $str = str_replace("{3}", "\\", $str);
        $str = str_replace("{4}", "<", $str);
        $str = str_replace("{5}", ">", $str);
        return $str;
    }

    /**
     * Decode the Unicode escaped chars like %u1239
     *
     * @param string $str The string to be decoded
     *
     * @return string
     */
    function decodeUnicodeString($str)
    {
        $res = '';

        $i = 0;
        $max = strlen($str) - 6;
        while ($i <= $max) {
            $character = $str[$i];
            if ($character == '%' && $str[$i + 1] == 'u') {
                $value = hexdec(substr($str, $i + 2, 4));
                $i += 6;

                if ($value < 0x0080) // 1 byte: 0xxxxxxx
                    $character = chr($value);
                else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
                    $character = chr((($value & 0x07c0) >> 6) | 0xc0) . chr(($value & 0x3f) | 0x80);
                else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
                $character = chr((($value & 0xf000) >> 12) | 0xe0) . chr((($value & 0x0fc0) >> 6) | 0x80) . chr(($value & 0x3f) | 0x80);
            } else
                $i++;

            $res .= $character;
        }
        return $res . substr($str, $i);
    }

    /**
     * Get the email log path (relative to the sites' root)
     * @return String
     */
    function getLogPath()
    {
        global $imSettings;
        return pathCombine(array($imSettings['general']['public_folder'], "email_log.txt"));
    }

    /**
     * Register a message in the email log
     * @param  String $message The message to be saved in the log
     * @return void
     */
    function registerLog($message)
    {
        if (function_exists("file_get_contents") && function_exists("file_put_contents")) {
            $data = "";
            $file = pathCombine(array($this->pathToRoot, $this->getLogPath()));
            if (file_exists($file)) {
                $data = @file_get_contents($file);
            }
            $data = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL . $data;
            @file_put_contents($file, $data);
        }
    }
}


/**
 * Provide the smallest and simplest PHP templating engine ever
 */
class Template {

	private $vars = array();
	private $view_template_file;

	public function __construct($view_template_file)
	{
		$this->view_template_file = $view_template_file;
	}

	public function __get($name)
	{
		return $this->vars[$name];
	}

	public function __set($name, $value)
	{
		if($name == 'view_template_file') {
			throw new Exception("Cannot bind variable named 'view_template_file'");
		}
		$this->vars[$name] = $value;
	}

	public function render()
	{
		extract($this->vars);
		ob_start();
		include($this->view_template_file);
		return ob_get_clean();
	}
}


/**
 * @summary
 * Manage the user messages in a topic or discussion.
 * To use it, you must include __x5engine.php__ in your code.
 *
 * @description Create a new instance of ImTopic class
 *
 * @class
 * @constructor
 *
 * @param {string} $id       The topic id
 * @param {string} $basepath The base path
 * @param {string} $postUrl  The URL to post to
 */
class ImTopic
{
    /**
     * The captcha code used inside this class
     * @var string
     */
    static $captcha_code       = "";

    public $comments           = null;
    private $id;
    private $target;
    private $basePathRes;
    private $db                = null;
    private $table             = "";
    private $folder            = "";
    private $storageType       = "xml";
    private $basepath          = "";
    private $posturl           = "";
    private $title             = "";
    private $comPerPage        = 10;
    private $paginationNumbers = 10;

    /**
     * Create a new instance of ImTopic class
     *
     * <@ignore></@ignore>
     *
     * @param {string} $id       The topic id
     * @param {string} $target   Id container 
     * @param {string} $basepath The base path
     * @param {string} $postUrl  The URL to post to
     */
    function __construct($id, $target = "", $basepath = "", $postUrl = "", $queryString = null, $queryStringFallbackAll = false)
    {
        $this->id = $id;
        $this->target = $target;
        $this->basePathRes = $basepath;
        if (strlen($postUrl)) {
            $this->posturl = trim($postUrl, "?&");
            $this->posturl .=(strpos($this->posturl, "?") === false ? "?" : "&");
        } else {
            $this->posturl = basename($_SERVER['PHP_SELF']) . "?";
        }
        //adding parameters of querystring to url if is present
        if ($queryString != null) {
            $found = false;
            foreach ($queryString as $value) {
                if ($_GET[$value] != null && $_GET[$value] != "") {
                    $this->posturl .= $value . "=" . $_GET[$value] . "&";
                    $found = true;
                }
            }
            if(!$found && $queryStringFallbackAll){
                $this->posturl .= $_SERVER['QUERY_STRING'];
            }
            if (substr($this->posturl, -1) == "&") {
                $this->posturl = substr($this->posturl, 0, -1);
            }
        }
        $this->basepath = $this->prepFolder($basepath);
        // Create the comments array
        $this->comments = new ImComment();
    }

    /**
     * Set the number of comments to show in each page
     *
     * @param {integer} $n
     *
     * @return {Void}
     */
    function setCommentsPerPage($n) {
        $this->comPerPage = $n;
    }

    /**
     * Set the path to wich the data is posted to when a message is submitted
     *
     * @param {string} $posturl
     *
     * @return {Void}
     */
    function setPostUrl($posturl)
    {
        $this->posturl = $posturl . (strpos($posturl, "?") === false ? "?" : "&");
    }

    /**
     * Set the title of this topic
     *
     * @param {string} $title
     *
     * @return {Void}
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Return the encrypted filename of a string
     *
     * @ignore
     *
     * @param  string $str
     *
     * @return string
     */
    function encFileName($str)
    {
        return substr(md5($str), 0, 8) . substr($str, -4);
    }

    /**
     * Load the data from the xml file contained in the specified folder.
     * The filename of this topic is automatically calculated basing on the topic's id to provide a major lever of security.
     *
     * @param {string} $folder The file's folder
     *
     * @return {Void}
     */
    function loadXML($folder = "")
    {
        if ($this->comments == null)
            return;

        $this->folder = $this->prepFolder($folder);
        $encName = $this->encFileName($this->id);
        // Check if the encrypted filename exists
        if (file_exists($this->basepath . $this->folder . $encName))
            $this->comments->loadFromXML($this->basepath . $this->folder . $encName);
        // If the encrypted filename doesn't exist, try the normal filename
        else
            $this->comments->loadFromXML($this->basepath . $this->folder . $this->id);
        $this->storageType = "xml";
    }

    /**
     * Save the data to an xml file in the provided folder.
     * The filename of this topic is automatically calculated basing on the topic's id to provide a major lever of security.
     *
     * @param {string} $folder The folder where is saved the file
     *
     * @return {boolean} True if the file is saved correctly
     */
    function saveXML($folder = "")
    {
        if ($this->comments == null)
            return;

        $encName = $this->encFileName($this->id);
        $folder = $folder != "" ? $this->prepFolder($folder) : $this->folder;
        if ($this->comments->saveToXML($this->basepath . $folder . $encName)) {
            // If the comments can be saved, check if the non-encrypted file exists. If so, delete it.
            if (file_exists($this->basepath . $this->folder . $this->id))
                unlink($this->basepath . $this->folder . $this->id);
            return true;
        }
        return false;
    }

    /**
     * Setup the folder
     *
     * @ignore
     *
     * @param string $folder The folder path to prepare
     *
     * @return string
     */
    function prepFolder($folder)
    {
        if (strlen(trim($folder)) == 0)
            return "./";

        if (substr($folder, 0, -1) != "/")
            $folder .= "/";

        return $folder;
    }


    /**
     * Save the topic usin the correct storage type
     *
     * @return Boolean
     */
    function save()
    {
        return $this->saveXML();
    }

    /**
     * Checks the $_POST array for new messages
     *
     * @ignore
     *
     * @param boolean $moderate    TRUE to show only approved comments
     * @param string  $to          The email to notify the new comment
     * @param string  $type        The topic type (guestbook|blog)
     * @param string  $moderateurl The url where the user can moderate the comments
     *
     * @return boolean
     */
    function checkNewMessages($moderate = true, $to = "", $type = "guestbook", $moderateurl = "")
    {
        global $ImMailer;
        global $imSettings;

        /*
        |-------------------------------------------
        |    Check for new messages
        |-------------------------------------------
         */

        if (!isset($_POST['x5topicid']) || $_POST['x5topicid'] != $this->id)
            return false;
        if (!checkJsAndSpam(md5($this->id)))
            return false;

        $comment = array(
            "email"     => $_POST['email'],
            "name"      => $_POST['name'],
            "url"       => $_POST['url'],
            "body"      => $_POST['body'],
            "ip"        => $_SERVER['REMOTE_ADDR'],
            "timestamp" => date("Y-m-d H:i:s"),
            "abuse"     => "0",
            "approved"  => $moderate ? "0" : "1"
        );
        if (isset($_POST['rating']))
            $comment['rating'] = $_POST['rating'];
        $this->comments->add($comment);
        if (!$this->save()) {
            echo "<script>window.top.location.href='" . $this->posturl . $this->id . "error';</script>";
            return false;
        }
        // Send the notification email
        if ($to != "") {
            // ---------------------------------------------------
            $from = $imSettings['general']['common_email_sender_addres'];
            $replyTo = $comment['email'];
            // ---------------------------------------------------

            if ($type == "guestbook")
                $html = str_replace(array("Blog", "blog"), array("Guestbook", "guestbook"), l10n('blog_new_comment_text')) . " \"" . $this->title . "\":<br /><br />\n\n";
            else if ($type == "productpage")
                $html = l10n('cart_new_comment_text') . " \"" . $this->title . "\":<br /><br />\n\n";
            else
                $html = l10n('blog_new_comment_text') . ":<br /><br />\n\n";
            $html .= "<b>" . l10n('blog_name') . "</b> " . stripslashes($_POST['name']) . "<br />\n";
            $html .= "<b>" . l10n('blog_email') . "</b> " . $_POST['email'] . "<br />\n";
            $html .= "<b>" . l10n('blog_website') . "</b> " . $_POST['url'] . "<br />\n";
            if (isset($_POST['rating']))
                $html .= "<b>" . l10n('blog_rating', "Vote:") . "</b> " . $_POST['rating'] . "/5<br />\n";
            $html .= "<b>" . l10n('blog_message') . "</b> " . stripslashes($_POST['body']) . "<br /><br />\n\n";
            // Set the proper link
            if ($moderateurl != "") {
                $html .= ($moderate ? l10n('blog_approve_link') : l10n('blog_unapprove_link')) . ":<br />\n";
                $html .= "<a href=\"" . $moderateurl . "\">" . $moderateurl . "</a>";
            }
            if ($type == "guestbook")
                $subject = str_replace(array("Blog", "blog"), array("Guestbook", "guestbook"), l10n('blog_new_comment_object'));
            else if ($type == "productpage")
                $subject = str_replace(array("Blog", "blog"), array($this->title, $this->title), l10n('blog_new_comment_object'));
            else
                $subject = l10n('blog_new_comment_object');
            $ImMailer->send($from, $replyTo, $to, $subject, strip_tags($html), $html);
        }

        // Redirect
        echo "<script>window.top.location.href='" . $this->posturl . ($moderate ? $this->id . "success" : "") . "';</script>";
        return true;
    }

    /**
     * Get the comments sent in the specified period that already validated
     *
     * @param  String $from
     * @param  String $to
     *
     * @return Array
     */
    function getComments($from = "", $to = "", $approved = true)
    {
        $comments = array();
        foreach ($this->comments->comments as $comment) {
            if ($approved && $comment['approved'] != 1 || !$approved && $comment['approved'] != 0) {
                continue;
            }
            if (strlen($from) && strtotime($comment['timestamp']) < strtotime($from)) {
                continue;
            }
            if (strlen($to) && strtotime($comment['timestamp']) > strtotime($to)) {
                continue;
            }
            $comments[] = $comment;
        }
        usort($comments, array("ImTopic", "compareCommentsArray"));
        return $comments;
    }

    /**
     * Used in getCommentsToValidate in order to sort the comments array in the right descending order
     * @param  Array $a The comment data a
     * @param  Array $b The comment data b
     * @return Void
     */
    static function compareCommentsArray($a, $b)
    {
        $ta = strtotime($a['timestamp']);
        $tb = strtotime($b['timestamp']);

        if ($ta == $tb) {
            return 0;
        }
        if ($ta < $tb) {
            return 1;
        }
        return -1;
    }

    /**
     * Check for new abuses
     *
     * @ignore
     *
     * @return void
     */
    function checkNewAbuses()
    {
        if (isset($_GET['x5topicid']) && $_GET['x5topicid'] == $this->id) {
            if (isset($_GET['abuse'])) {
                $n = intval($_GET['abuse']);
                $c = $this->comments->get($n);
                $c['abuse'] = "1";
                $this->comments->edit($n, $c);
                $this->save();
                echo "ok";
            }
        }
    }

    /**
     * Generate the HTML for the star rating control
     * 
     * @param {int} $selectedVote   currently selected vote
     * 
     * @return void
     */
    function getStarRatingHTML($selectedVote = 0)
    {
        for ($i = 1; $i <= 5; $i++) {
            echo "<input value=\"" . $i . "\" id=\"star" . $i . "\" " . ($i == $selectedVote ? "checked " : "") . "type=\"radio\" name=\"rating\" class=\"screen-reader-only-even-focused\" aria-required=\"true\"/>";
            echo "<label for=\"star" . $i . "\" class=\"topic-star-label\"><span class=\"screen-reader-only-even-focused\">" . $i . "</span>";
            echo "<span class=\"topic-star-big\"></span>";
            echo "</label>";
        }
    }

    /**
     * Show the comments form
     *
     * @param {boolean} $rating      true to show the rating
     * @param {boolean} $captcha     true to enable captcha
     *
     * @return bool                  True if a new comment has been
     */
    function showForm($rating = true, $captcha = true)
    {
        global $imSettings;
        $id = $this->id . "-topic-form";
        $requiredMark = "<span aria-hidden=\"true\">*</span>";

        $this->checkNewAbuses();

        /*
        |-------------------------------------------
        |    Show the form
        |-------------------------------------------
         */

        if (isset($_GET[$this->id . 'success'])) {
            echo "<div class=\"alert alert-green\">" . l10n('blog_send_confirmation') . "</div>";
        } else if (isset($_GET[$this->id . 'error'])) {
            echo "<div class=\"alert alert-red\">" . l10n('blog_send_error') . "</div>";
        }

        echo "<div class=\"topic-form\">
              <form id=\"" . $id ."\" class=\"comments-and-ratings-topic-form\" action=\"" . $this->posturl . "\" method=\"post\">
                <input type=\"hidden\" name=\"post_id\" value=\"" . $this->id . "\"/>
                <div class=\"topic-form-row\">
                    <div class=\"topic-form-item\">
                        <label for=\"" . $id . "-name\">" . l10n('blog_name') . $requiredMark . "</label> 
                        <input type=\"text\" id=\"" . $id . "-name\" name=\"name\" class=\"imfield mandatory striptags trim\" aria-required=\"true\"/>
                    </div>
                    <div class=\"topic-form-item second-column\">
                        <label for=\"" . $id . "-url\">" . l10n('blog_website') . "</label>
                        <input type=\"text\" id=\"" . $id . "-url\" name=\"url\" />
                    </div>
                </div>
                <div class=\"topic-form-row\">
                    <div class=\"topic-form-item\">
                        <label for=\"" . $id . "-email\">" . l10n('blog_email') . $requiredMark . "</label>
                        <input type=\"text\" id=\"" . $id . "-email\" name=\"email\" class=\"imfield mandatory valEmail\" aria-required=\"true\"/>
                    </div>
                    <div class=\"topic-form-item second-column empty-column\">
                    </div>
                </div>";

        if ($rating) {
            //note: star-full Input occupies the same width as the stars and is used to understand if at least one star has been selected
            //and to make the "mandatory" tip appear to the right of the stars: the span kills 0% if a score is not selected.
            echo "<div class=\"topic-form-row\">
                    <div class=\"topic-form-item rating\">
                        <label for=\"" . $id . "-star-full\">" . l10n('blog_rating') . $requiredMark . "</label>
                        <span class=\"topic-star-container-big variable-star-rating\" aria-label=\"" . l10n('comments_and_ratings_selected_rate') . " 0\">";
                            $this->getStarRatingHTML();
            echo "          <input type=\"text\" id=\"" . $id . "-star-full\" name=\"star-full\" class=\"imfield mandatory\" style=\"width: 160px; visibility: hidden; display: none;\"/>
                        </span>
                    </div>
                </div>";
        }
        echo "<div class=\"topic-form-row\">
                <div class=\"topic-form-item\">
                    <label for=\"" . $id . "-body\">" . l10n('blog_message') . $requiredMark . "</label>
                    <textarea maxlength=\"1500\" id=\"" . $id . "-body\" name=\"body\" class=\"imfield mandatory striptags trim\" style=\"width: 100%; height: 100px;\" aria-required=\"true\"></textarea>
                </div>
        </div>";
        if ($captcha) {
            echo ImTopic::$captcha_code;
        }
        echo "<input type=\"hidden\" value=\"" . $this->id . "\" name=\"x5topicid\">";
        echo "<input type=\"text\" value=\"\" name=\"prt\" class=\"prt_field\">";
        echo "<div class=\"topic-form-row\">
                <input type=\"submit\" value=\"" . l10n('comments_and_ratings_send_review') . "\" />
              </div>
              </form>
              <script>x5engine.boot.push( function () { x5engine.imForm.initForm('#" . $id . "', false, { 'jsid': '" . md5($this->id) . "', showAll: true }); });</script>
        </div>\n";
    }

    /**
     * Show the topic summary
     *
     * @param {boolean} $rating      TRUE to show the ratings
     * @param {boolean} $admin       TRUE to show approved and unapproved comments
     * @param {boolean} $hideifempty true to hide the summary if there are no comments
     *
     * @return {Void}
     */
    function showSummary($ratingAndStars = true, $admin = false, $hideifempty = true)
    {
        $data = $this->getRatingsDataSummary($admin);

        $classContainer = "topic-summary" . ($data["totalComments"] > 0 ? "" : " no-review");
        $classContainer .= $ratingAndStars ? " comments-and-star" : " comments";

        echo "<div id=\"" . $this->id . "-topic-summary\" class=\""  . $classContainer . "\">\n";
        if ( $ratingAndStars ) {
            /** case comment and star **/

            //add block of topic average
            echo $data["totalComments"] == 0 ? $this->getTopicZeroAverage() : $this->getTopicAverage($data["totalComments"], $data["vote"]);

            //add block topic bars
            echo $this->getTopicBars($data["votescount"], $data["ratingByValue"]);

            //add block of add review button
            echo $this->getTopicAddReviewHtml();
        }
        else {
            /** case only comment **/
            echo "<div class=\"topic-total-review\">";
                echo "<div class=\"topic-review-c\">";
                    echo "<div class=\"topic-number-review\">" . $data["totalComments"] . "</div>";
                    echo "<div class=\"label-review\">" . ( $data["totalComments"] == 1 ? l10n('comments_and_ratings_label_review') : l10n('comments_and_ratings_label_reviews') ) . "</div>";
                    echo "<div class=\"fill\"></div>";
                echo "</div>\n";
            echo "</div>\n";

            //add block of button of topic to add review
            echo $this->getTopicAddReviewHtml();
            
            echo "<div class=\"topic-space\">";
                echo "<div class=\"fill\"></div>";
            echo "</div>";
        }
        echo "</div>\n"; //end topic-summary
    }

    function getRatingsDataSummary($admin = false) {
        $c = $this->comments->getAll();
        $vote = 0;
        $votes = 0;
        $votescount = 0;
        $ratingByValue = array("1"=> 0, "2"=> 0, "3"=> 0, "4"=> 0, "5"=> 0);
        $totalComments = 0;

        if (count($c) > 0) {
            foreach ($c as $comment) {
                if ($comment['approved'] == "1" || $admin) {
                    if ( isset($comment['body']) ) {
                        $totalComments++;
                        if ( isset($comment['rating']) && $comment['rating'] > 0 ) {
                            $votes += $comment['rating'];
                            $votescount++;
                            $ratingByValue[$comment['rating']] = $ratingByValue[$comment['rating']] + 1;
                        }
                    }
                }
            }
            $vote = $votescount > 0 ? $votes / $votescount : 0;
        }

        return array(
            "vote" =>  $vote,
            "formatVote" =>  number_format($vote, 1),
            "votescount" =>  $votescount,
            "ratingByValue" =>  $ratingByValue,
            "totalComments" =>  $totalComments
        );
    }

    /**
     * Get the rating of the topic
     *
     * @ignore
     *
     * @return Array( "count" => "number of comments", "rating" => "the post rating out of 5 stars")
     */
    function getRating()
    {
        $results = array("count" => 0, "rating" => 0, "hasrating" => false);
        $c = $this->comments->getAll();
        $comments = array();
        $votes = 0;
        $votescount = 0;
        foreach ($c as $comment) {
            if (isset($comment['body'])) {
                $comments[] = $comment;
            }
            if (isset($comment['rating']) && $comment['rating'] > 0) {
                $votes += $comment['rating'];
                $votescount++;
            }
        }
        $count = count($comments);
        $vote = $votescount > 0 ? $votes / $votescount : 0;

        if ($count == 0) {
            return $results;
        }

        $results["count"] = $count;
        $results["rating"] = $vote;

        return $results;
    }

    /**
     * Returns html of box average of topic with zero comment
     * @return string
     */
    function getTopicZeroAverage()
    {
        $average = "<div class=\"topic-average\">";
        $average .=     "<div class=\"label-no-review\">" . l10n('comments_and_ratings_no_reviews') . "</div>";
        $average .=     "<div class=\"fill\"></div>";
        $average .= "</div>\n"; //end topic-average
        return $average;
    }

    /**
     * Returns html of box average of topic
     * @return string
     */
    function getTopicAverage($totalComments, $vote)
    {
        $average = "<div class=\"topic-average\">";
        $average .=     "<div style=\"margin-bottom: 5px;\">";
        $average .=         "<div class=\"rating-value\"><span class=\"big\">" . ( $vote == 0 ? "-" : number_format($vote, 1) ) . "</span>&nbsp;/&nbsp;<span>5</span></div>";
        $average .=     "</div>";
        $average .=     "<span class=\"topic-star-container-big\" title=\"" . number_format($vote, 1) . "/5\">";
        $average .=         "<span class=\"topic-star-fixer-big\" style=\"width: " . round($vote/5 * 100) . "%;\"></span>";
        $average .=     "</span>\n";
        $average .=     "<div class=\"label-review\">" . $totalComments . "&nbsp;" . ( $totalComments == 1 ? l10n('comments_and_ratings_label_review') : l10n('comments_and_ratings_label_reviews') ) . "</div>";
        $average .=     "<div class=\"fill\"></div>";
        $average .= "</div>\n"; //end topic-average
        return $average;
    }

    /**
     * Returns html of box topic bars
     * @return string
     */
    function getTopicBars($votescount, $ratingByValue)
    {
        $topicBars =  "<div class=\"topic-bars\">";
        for ($i = 5; $i > 0; $i--) {
            $topicBars .= "<div class=\"topic-bar\">";
            $topicBars .= "<div class=\"bar-star-n\"><span class=\"screen-reader-only-even-focused\">" . l10n("comments_and_ratings_rate", "Rate:") . "</span>" . $i . "&nbsp; <span class=\"topic-star-fixer-small star\"></span></div>\n";  
            $topicBars .= "<div class=\"bar-progress\"><span style=\"width:". ($votescount > 0 ? ( ($ratingByValue[$i] * 100) / $votescount ) : 0) ."%;\"></span></div>\n";        
            $topicBars .= "<div class=\"bar-total\"><span class=\"screen-reader-only-even-focused\">" . l10n("comments_and_ratings_number_of_rates", "Number of rates:") . "</span>". $ratingByValue[$i] ."</div>\n";
            $topicBars .= "</div>\n"; //end topic-bar
        }
        $topicBars .= "<div class=\"fill\"></div>";
        $topicBars .= "</div>\n"; //end topic-bars
        return $topicBars;
    }

    /**
     * Returns html of button to add add review of topic
     * @return string
     */
    function getTopicAddReviewHtml()
    {
        $addReview = "<div class=\"topic-add-review\">";
        $addReview .= "<input type=\"button\" class=\"topic-add-review-btn\" value=\"" . l10n('comments_and_ratings_add_review') . "\" />";
        $addReview .= "<div class=\"fill\"></div>";
        $addReview .= "</div>";
        return $addReview;
    }

    /**
     * Returns true if this topic has comments
     * @return boolean
     */
    function hasComments()
    {
        return $this->comments && count($this->comments->getAll()) > 0;
    }

    /**
     * Show the comments list
     *
     * @param {integer} $page            The page to show
     * @param {integer} $commentsPerPage The number of comments to show for each page
     * @param {boolean} $rating          True to show the ratings
     * @param {string}  $order           desc or asc
     * @param {boolean} $showabuse       True to show the "Abuse" button
     * @param {boolean} $hideifempty     True to hide the summary if there are no comments
     *
     * @return {Void}
     */
    function showComments($rating = true, $order = "desc", $showabuse = true, $showOnMultipleColumns = false, $hideifempty = false)
    {
        global $imSettings;

        $page = @$_GET[$this->id . "page"];
        $c = $this->comments->getPage($page, $this->comPerPage, "timestamp", $order, true);
        $ncolumns = 1;

        if ( count($c) == 0 && $hideifempty ) {
            return;
        }

        echo "<div class=\"topic-comments " . ($showOnMultipleColumns  == true ? "multiple-columns" : "one-columns") . "\">\n";
        if ( count($c) > 0 ) {
            $ncolumns = min(count($c), 4);

            // Show the comments
            $i = 0;
            foreach ( $c as $comment ) {
                if ( isset($comment['body']) && $comment['approved'] == "1" ) {
                    //format date to extended format set by user
                    $createDate = new DateTime($comment['timestamp']);
                    $formatDate = formatDate($createDate, true);

                    //check to make sure that the text does not exceed the maximum font size. If it passes I cut the text
                    $body = $comment['body'];
                    $body = str_replace(array("<br>", "<br />"), "", $comment['body']);
                    $bodyTooLong = false;
                    if ( function_exists("mb_strlen") ) {
                        do {
                            $new_body = htmlspecialchars_decode($body, ENT_QUOTES);
                            $delta = mb_strlen($body) - mb_strlen($new_body);
                            $body = $new_body;
                        }
                        while ($delta > 0);
                        $bodyTooLong = mb_strlen( $body ) > 1500;
                    }
                    else {
                        do {
                            $new_body = htmlspecialchars_decode($body, ENT_QUOTES);
                            $delta = strlen($body) - strlen($new_body);
                            $body = $new_body;
                        }
                        while ($delta > 0);
                        $bodyTooLong = strlen( $body ) > 1500;
                    }
                    if ( $bodyTooLong ) {
                        // WARNING: Maybe you don't see them, there are invisibile spaces inside quotes in the next lines
                        $body = str_replace(array("<br>", "<br />"), "​", $comment['body']);
                        $body = substr($body, 0, 1500) . '...';
                        $body = str_replace("​", "<br>", $body);
                    }
                    else {
                        $body = $comment['body'];
                    }
                    //avatar setting
                    $gravatar = new X5Gravatar($comment['email']);
                    $extra = "alt='' class='avatar-img'";
                    $gravatar->setExtra($extra);

                    echo "<div class=\"topic-comment\">\n";
                    echo "<div class=\"topic-comment-info-body\">\n";
                    echo "<div class=\"topic-comment-info\">\n";
                    echo "<div class=\"topic-comment-avatar\">" . $gravatar->toHTML() . "</div>\n";
                    if ( $showOnMultipleColumns ) {
                        echo "<div class=\"topic-comment-info-details\">\n";
                    }
                    echo "<div class=\"topic-comment-user\">";
                    echo stristr($comment['url'], "http") ? "<a href=\"" . $comment['url'] . "\" target=\"_blank\" " . (strpos($comment['url'], $imSettings['general']['url']) === false ? 'rel="nofollow"' : '') . "><span>" . $comment['name'] . "</span></a>" : "<span>" . $comment['name'] . "</span>";
                    echo "</div>";
                    echo "<div class=\"topic-comment-date imBreadcrumb\" datetime=\"" . $comment['timestamp'] . "\">" . $formatDate . "</div>\n";
                    if ( $rating && isset($comment['rating']) && $comment['rating'] > 0 ) {
                        echo "<div><span class=\"topic-star-container-small\" aria-label=\"" . $comment['rating'] . "/5\" role=\"img\">
                                    <span class=\"topic-star-fixer-small\" style=\"width: " . round($comment['rating']/5 * 100) . "%;\"></span>
                            </span></div>\n";
                    }
                    if ( $showOnMultipleColumns ) {
                        echo "</div>\n"; // closed topic-comment-info-details
                    }
                    echo "</div>\n"; // closed topic-info
                    echo "<div class=\"topic-comment-body\">" . $body . "</div>\n";
                    echo "</div>\n"; // closed topic-info-body
                    if ( $showabuse ) {
                        echo "<div class=\"topic-comment-abuse\">\n";
                        echo "<a onclick=\"x5engine.topicSendAbuse({ target: this, text: '<div>" . l10n('comments_and_ratings_new_abuse') . "</div>', postUrl: '" . $this->posturl . "x5topicid=" . $this->id . "&amp;abuse=" . $comment['id'] . "'});\" href=\"javascript:void(0);\">" . l10n('blog_abuse') . "</a>\n";
                        echo "</div>\n";
                    }
                    echo "</div>\n"; // closed topic-comment
                }
                $i++;
            }
            echo "<script>\n";
            echo "x5engine.boot.push( function () {\n";
                echo "if (x5engine.responsive.isMobileDevice()) { $('.topic-comment-abuse').addClass('mobile'); }\n";
            echo "});\n";
            echo "</script>\n";
        }
        echo "</div>\n";

        if ( $showOnMultipleColumns ) {
            echo "<script src=\"" . $this->basePathRes ."res/masonry.pkgd.min.js\" ></script>\n";
            echo "<script>x5engine.boot.push(\"x5engine.topicCommentsMultipleColumnsResize('" . $this->target . "', " . $ncolumns . ")\", false, 6);</script>\n";
        }

        // Show the pagination
        $this->showPagination($page);
    }

    /**
     * Show the pagination links for this topic
     *
     * @param  {int} $page Page number
     *
     * @return {Void}
     */
    function showPagination($page) {
        $currentPage = is_null($page) ? 0 : $page;
        $pages = $this->comments->getPagesNumber($this->comPerPage, true);
        if ($pages < 2) {
            return;
        }

        echo "<div class=\"pagination-container\">\n";
        $anchor = "#" .$this->id . "-topic-summary";
        $interval = floor($this->paginationNumbers / 2);
        
        if ( $currentPage > 0 ) {
            $pageToGo = $currentPage - 1;
            $link = updateQueryStringVar($this->id . "page", $pageToGo) . $anchor;
            echo "\t<a href=\"?" . $link . "\" class=\"page\">" . l10n("cmn_pagination_prev") . "</a>\n";
        }

        $leading_dots = false;
        $trailing_dots = false;
        for ($i = max(0, $page - $interval); $i < min($pages, $i + $interval); $i++) {
            if ($pages < 7 || $i == 1 || $i == $pages || ($i >= $currentPage - 1 && $i <= $currentPage + 1)) {
                $cl = "page";
                $cl .= $i == $page ? " current" : "";
                $link = updateQueryStringVar($this->id . "page", $i) . $anchor;
                echo "\t<a href=\"?" . $link . "\" class=\"" . $cl . "\"><span class=\"screen-reader-only-even-focused\">" . ($i == $page ? l10n("cmn_pagination_current_page", "Current page:") : l10n("cmn_pagination_go_to_page", "Go to page:")) . "</span>" . ($i + 1) . "</a>\n";
            }
            else if ($i < $currentPage - 1 && !$leading_dots) {
                echo "<span class=\"dots-page\">...</span>";
                $leading_dots = true;
            }
            else if ($i > $currentPage + 1 && !$trailing_dots) {
                echo "<span class=\"dots-page\">...</span>";
                $trailing_dots = true;
            }
        }
        
        if ( $currentPage < ($pages - 1) ) {
            $pageToGo = $currentPage + 1;
            $link = updateQueryStringVar($this->id . "page", $pageToGo) . $anchor;
            echo "\t<a href=\"?" . $link . "\" class=\"page\">" . l10n("cmn_pagination_next") . "</a>\n";
        }
        echo "</div>\n";
    }

    /**
     * Show the comments list in a administration section
     *
     * @ignore
     *
     * @param boolean $rating true to show the ratings
     * @param string  $order  desc or asc
     *
     * @return void
     */
    function showAdminComments($rating = true, $order = "desc")
    {

        global $imSettings;
        $this->comments->sort("ts", $order);

        if (isset($_GET['disable'])) {
            $n = (int)$_GET['disable'];
            $c = $this->comments->get($n);
            if (count($c) != 0) {
                $c['approved'] = "0";
                $this->comments->edit($n, $c);
                $this->saveXML();
            }
        }

        if (isset($_GET['enable'])) {
            $n = (int)$_GET['enable'];
            $c = $this->comments->get($n);
            if (count($c) != 0) {
                $c['approved'] = "1";
                $this->comments->edit($n, $c);
                $this->saveXML();
            }
        }

        if (isset($_GET['delete'])) {
            $this->comments->delete((int)$_GET['delete']);
            $this->storageType == "xml" ? $this->saveXML() : $this->saveDb();
        }

        if (isset($_GET['unabuse'])) {
            $n = (int)$_GET['unabuse'];
            $c = $this->comments->get($n);
            if (count($c)) {
                $c['abuse'] = "0";
                $this->comments->edit($n, $c);
                $this->saveXML();
            }
        }

        if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['delete']) || isset($_GET['unabuse'])) {
            echo "<script>window.top.location.href='" . $this->posturl . "';</script>\n";
            exit();
        }

        echo "<div class=\"topic-comments\">\n";
        $c = $this->comments->getAll();
        if (count($c) > 0) {
            // Show the comments
            for ($i = 0; $i < count($c); $i++) {
                $comment = $c[$i];
                if (isset($comment['body'])) {
                    echo "<div class=\"topic-comment " . ($comment['approved'] == "1" ? "enabled" : "disabled") . ($comment['abuse'] == "1" ? " abused" : "") . "\">\n";
                    echo "<div class=\"topic-comment-user-wrapper\">\n";
                    echo "\t\t<div class=\"topic-comment-user\">";
                    // Abuse sign
                    if ($comment['abuse'] == "1") {
                        echo "<img src=\"" . $this->basepath . "res/exclamation.png\" alt=\"Abuse\" title=\"" . l10n('admin_comment_abuse') . "\" style=\"vertical-align: middle;\">\n";
                    }
                    // User name (with link to its url if available)
                    // Prepare the url
                    if (isset($comment['url']) && strlen($comment['url']) > 0) {
                        if (strpos($comment['url'], "http://") !== 0 && strpos($comment['url'], "https://") !== 0) {
                            $comment['url'] = "http://" . $comment['url'];
                        }
                        echo "<a href=\"" . $comment['url'] . "\" target=\"_blank\" " . (strpos($comment['url'], $imSettings['general']['url']) === false ? 'rel="nofollow"' : '') . ">" . $comment['name'] . "</a>";
                    } else {
                        echo $comment['name'];
                    }
                    // Email
                    if (isset($comment['email'])) {
                        echo " (<a href=\"mailto:" . $comment['email'] . "\">" . $comment['email'] . "</a>)";
                    }
                    echo "\t\t<div class=\"topic-comment-date\">" . $comment['timestamp'] . "</div>\n";
                    // Rating
                    if ($rating && isset($comment['rating']) && $comment['rating'] > 0) {
                        echo "\t\t<div class=\"topic-star-container-small\" aria-label=\"" . $comment['rating'] . "/5\" role=\"img\">
                                    <span class=\"topic-star-fixer-small\" style=\"width: " . round($comment['rating']/5 * 100) . "%;\"></span>
                            </div>\n";
                    }
                    echo "\t\t</div>\n";
                    echo "\t\t<div class=\"topic-comment-body\">" . $comment['body'] . "</div>\n";
                    echo "\t</div>\n";
                    echo "\t<div class=\"topic-comment-controls\">\n";
                    echo "\t\t<span class=\"left\">IP: " . $comment['ip'] . "</span>\n";
                    if ($comment['abuse'] == "1")
                        echo "\t\t<a class=\"green\" href=\"" . $this->posturl . "unabuse=" . $comment['id'] . "\">" . l10n("blog_abuse_remove", "Remove abuse") . "</a> |\n";
                    if ($comment['approved'] == "1")
                        echo "\t\t<a class=\"black\" onclick=\"return confirm('" . str_replace("'", "\\'", l10n('blog_unapprove_question')) . "')\" href=\"" . $this->posturl . "disable=" . $comment['id'] . "\">" . l10n('blog_unapprove') . "</a> |\n";
                    else
                        echo "\t\t<a class=\"black\" onclick=\"return confirm('" . str_replace("'", "\\'", l10n('blog_approve_question')) . "')\" href=\"" . $this->posturl . "enable=" . $comment['id'] . "\">" . l10n('blog_approve') . "</a> |\n";
                    echo "\t\t<a class=\"red\" onclick=\"return confirm('" . str_replace("'", "\\'", l10n('blog_delete_question')) . "')\" href=\"" . $this->posturl . "delete=" . $comment['id'] . "\">" . l10n('blog_delete') . "</a>\n";
                    echo "</div>\n";
                    echo "</div>\n";
                }
            }
        } else {
            echo "<div style=\"text-align: center; margin: 15px; 0\">" . l10n('blog_no_comment') . "</div>\n";
        }
        echo "</div>\n";
    }

    /**
     * Show a rating form
     *
     * @return void
     */
    function showRating()
    {
        global $imSettings;
        //if null cookie
        $cookieVotedIsNull = is_null(im_get_cookie('vtd' . $this->id));
        //value of voted
        $cookieVotedValue = $cookieVotedIsNull ? 0 : im_get_cookie('vtd' . $this->id);

        if ( isset($_POST['x5topicid']) && $_POST['x5topicid'] == $this->id && $cookieVotedIsNull && isset($_POST['imJsCheck']) && $_POST['imJsCheck'] == 'jsactive' ) {
            $this->comments->add(
                array(
                    "rating" => $_POST['rating'],
                    "approved" => "1"
                )
            );
            $this->saveXML();
        }
        $data = $this->getRatingsDataRating();

        $classContainer = "topic-summary star" . ($data["totalComments"] > 0 ? "" : " no-review");
        echo "<div id=\"" . $this->id . "-topic-summary\" class=\""  . $classContainer . "\">\n";

        //add block of topic average
        echo $data["totalComments"] == 0 ? $this->getTopicZeroAverage() : $this->getTopicAverage($data["totalComments"], $data["vote"]);

        //add block topic bars
        echo $this->getTopicBars($data["totalComments"], $data["ratingByValue"]);

        //add block vote with stars
        echo "<div class=\"topic-star\">";
            echo "<div style=\"text-align: center;\">";
                echo "<div class=\"box-star\">";
                    echo "<div class=\"enter_rating\">" . ( $cookieVotedIsNull ? l10n("comments_and_ratings_enter_rating") : l10n("comments_and_ratings_thanks") ) . "</div>";
                    echo "<div class=\"topic-star-container-big" . ( $cookieVotedIsNull ? " variable-star-rating" : "" ) . "\" data-url=\"" . $this->posturl . "\" data-id=\"" . $this->id . "\" aria-label=\"" . l10n('comments_and_ratings_selected_rate') . " " . $cookieVotedValue . "\">";
                        $this->getStarRatingHTML($cookieVotedIsNull ? 0 : $cookieVotedValue);
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"fill\"></div>";
            echo "</div>";
        echo "</div>";

        echo "</div>\n"; //end topic-summary
    }

    function getRatingsDataRating() {	
        $c = $this->comments->getAll();
        $totalComments = 0;
        $votes = 0;
        $ratingByValue = array("1"=> 0, "2"=> 0, "3"=> 0, "4"=> 0, "5"=> 0);

        $vote = 0;
        if ( count($c) > 0 ) {
            // Check aproved comments count
            $ca = array();
            foreach ($c as $comment) {
                if ( $comment['approved'] == "1" && isset($comment['rating']) && $comment['rating'] > 0 ) {
                    $totalComments++;
                    $votes += $comment['rating'];
                    $ratingByValue[$comment['rating']] = $ratingByValue[$comment['rating']] + 1;
                }
            }
            $vote = ( $totalComments > 0 ? $votes/$totalComments : 0 );
        }

        return array(
            "vote" =>  $vote,
            "formatVote" =>  number_format($vote, 1),
            "ratingByValue" =>  $ratingByValue,
            "totalComments" =>  $totalComments
        );
    }
}




/**
 * XML Handling class
 * @access public
 */
class imXML 
{
    var $tree = array();
    var $force_to_array = array();
    var $error = null;
    var $parser;
    var $inside = false;

    function __construct($encoding = 'UTF-8')
    {
        $this->parser = xml_parser_create($encoding);
        xml_set_object($this->parser, $this); // $this was passed as reference &$this
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($this->parser, "startEl", "stopEl");
        xml_set_character_data_handler($this->parser, "charData");
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    }

    function parse_file($file)
    {
        $fp = @fopen($file, "r");
        if (!$fp)
            return false;
        while ($data = fread($fp, 4096)) {
            if (!xml_parse($this->parser, $data, feof($fp))) {
                return false;
            }
        }
        fclose($fp);
        return $this->tree[0]["content"];
    }

    function parse_string($str)
    {
        if (!xml_parse($this->parser, $str))
            return false;
        if (isset($this->tree[0]["content"]))
            return $this->tree[0]["content"];
        return false;
    }

    function startEl($parser, $name, $attrs)
    {
        array_unshift($this->tree, array("name" => $name));
        $this->inside = false;
    }

    function stopEl($parser, $name)
    {
        if ($name != $this->tree[0]["name"])
            return false;
        if (count($this->tree) > 1) {
            $elem = array_shift($this->tree);
            if (isset($this->tree[0]["content"][$elem["name"]])) {
                if (is_array($this->tree[0]["content"][$elem["name"]]) && isset($this->tree[0]["content"][$elem["name"]][0])) {
                    array_push($this->tree[0]["content"][$elem["name"]], $elem["content"]);
                } else {
                    $this->tree[0]["content"][$elem["name"]] = array($this->tree[0]["content"][$elem["name"]],$elem["content"]);
                }
            } else {
                if (in_array($elem["name"], $this->force_to_array)) {
                    $this->tree[0]["content"][$elem["name"]] = array($elem["content"]);
                } else {
                    if (!isset($elem["content"])) $elem["content"] = "";
                    $this->tree[0]["content"][$elem["name"]] = $elem["content"];
                }
            }
        }
        $this->inside = false;
    }

    function charData($parser, $data)
    {
        if (!preg_match("/\\S/", $data))
                return false;
        if ($this->inside) {
            $this->tree[0]["content"] .= $data;
        } else {
            $this->tree[0]["content"] = $data;
        }
        $this->inside_data = true; 
    }
}


/**
 * Prints an error that warns the user that JavaScript is not enabled, then redirects to the previous page after 5 seconds.
 *
 * @param {boolean} $docType True to use the meta redirect with a complete document. False to use a javascript code.
 *
 * @return {Void}
 */
function imPrintJsError($docType = true)
{
    $message = l10n('form_js_error') . "<br />" . l10n('form_js_error_redirect');
    return imPrintError($message, $docType);
}

/**
 * Prints a custom error message then redirects to the previous page after 5 seconds.
 *
 * @param {string} $message The message to show
 * @param {boolean} $docType True to use the meta redirect with a complete document. False to use a javascript code.
 *
 * @return {Void}
 */
function imPrintError($message, $docType = true)
{
    if ($docType) {
        $html = "<DOCTYPE><html><head><meta charset=\"UTF-8\"> <meta http-equiv=\"Refresh\" content=\"5;URL=" . $_SERVER['HTTP_REFERER'] . "\"></head><body>";
        $html .= $message;
        $html .= "</body></html>";
    } else {
        $html = "<meta charset=\"UTF-8\"> <meta http-equiv=\"Refresh\" content=\"5;URL=" . $_SERVER['HTTP_REFERER'] . "\">";
        $html .= $message;
    }
    return $html;
}

/**
 * Check if the current user can access to the provided page id.
 * If not, the user is redirected to the login page.
 *
 * @method imCheckAccess
 *
 * @param {string} $page The id of the page to check
 * @param {string} $pathToRoot The path to reach the root from the current folder
 *
 * @return {Void}
 */
function imCheckAccess($page, $pathToRoot = "")
{
    $pa = Configuration::getPrivateArea();
    $stat = $pa->checkAccess($page);
    if ($stat !== 0) {
        $pa->savePage();
        header("Location: " . $pathToRoot . "imlogin.php?loginstatus=" . $stat );
        exit;
    }
}


/**
 * Show the guestbook
 * This function is provided as compatibility for v9 guestbook widget
 *
 * @param string  $id              The guestbook id
 * @param string  $filepath        The folder where the comments must be stored
 * @param string  $email           The email to notify the new comments
 * @param boolean $captcha         true to show the captcha
 * @param boolean $direct_approval true to directly approve comments
 *
 * @return void
 */
function showGuestBook($id, $filepath, $email, $captcha = true, $direct_approval = true)
{
    global $imSettings;

    $gb = new ImTopic("gb" . $id);
    $gb->loadXML($filepath);
    $gb->showSummary(false);
    $gb->showForm(false, $captcha, !$direct_approval, $email, "guestbook", $imSettings['general']['url'] . "/admin/guestbook.php?id=" . $id);
    $gb->showComments(false);
}

/**
 * Provide the database connection data of given id
 *
 * @method getDbData
 *
 * @param  {string} $dbid The database id, if not specified, will be used the first available database
 *
 * @return {array}        an array like array('description' => '', 'host' => '', 'database' => '', 'user' => '', 'password' => '')
 */
function getDbData($dbid = '') {
    global $imSettings;
    if ($dbid == '') {
        $dbs = array_values($imSettings['databases']);
        if (!is_array($dbs) || count($dbs) == 0) {
            return false;
        }
        return $dbs[0];
    }
    if (!isset($imSettings['databases'][$dbid]))
        return false;
    return $imSettings['databases'][$dbid];
}


/**
 * Shuffle an associative array
 *
 * @method shuffleAssoc
 *
 * @param {array} $list The array to shuffle
 *
 * @return {array}       The shuffled array
 */
function shuffleAssoc($list)
{
    if (!is_array($list))
        return $list;
    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)
        $random[$key] = $list[$key];
    return $random;
}

/**
 * Looks for function existence and caches the result
 * 
 * @method cached_function_exists
 * 
 * @param {string} $function The function name
 * 
 * @return {bool} Existence of the function
 */
$function_exists_cache = array();
function cached_function_exists($function) {
    global $function_exists_cache;
    if (!isset($function_exists_cache[$function])) {
        $function_exists_cache[$function] = function_exists($function);
    }
    return $function_exists_cache[$function];
}

/**
 * If you want to support Multibyte Languages, use this function instead of stripos.
 * Use this function only if $needle can contain non latin chars.
 *
 * @method imstripos
 *
 * @param {string}  $haystack Where to search
 * @param {string}  $needle   What to replace
 * @param {integer} $offset   Start searching from here
 *
 * @return {integer}          The position of the searched string
 */
function imstripos($haystack, $needle, $offset = 0)
{
    if (cached_function_exists('mb_stripos'))
        return mb_stripos($haystack, $needle, $offset);

    return stripos($haystack, $needle, $offset);
}

/**
 * If you want to support Multibyte Languages, use this function instead of strpos.
 * Use this function only if $needle can contain non latin chars.
 *
 * @method imstrpos
 *
 * @param {string}  $haystack Where to search
 * @param {string}  $needle   What to replace
 * @param {integer} $offset   Start searching from here
 *
 * @return {integer}          The position of the searched string
 */
function imstrpos($haystack, $needle, $offset = 0)
{
    if (cached_function_exists('mb_strpos'))
        return mb_strpos($haystack, $needle, $offset);

    return strpos($haystack, $needle, $offset);
}

/**
 * If you want to support Multibyte Languages, use this function instead of stristr.
 * Use this function only if $needle can contain non latin chars.
 *
 * @method imstristr
 *
 * @param {string}  $haystack Where to search
 * @param {string}  $needle   What to replace
 *
 * @return {string}           Haystack starting from needle
 */
function imstristr($haystack, $needle)
{
    if (cached_function_exists('mb_stristr'))
        return mb_stristr($haystack, $needle);

    return stristr($haystack, $needle);
}

/**
 * If you want to support Multibyte Languages, use this function instead of substr.
 * Use this function only if using other imstr functions.
 *
 * @method imsubstr
 *
 * @param {string}   $string   String to extract the substring from
 * @param {integer}  $start    Starting position
 * @param {integer}  $length   Substring length
 *
 * @return {string}           Substring
 */
function imsubstr($string, $start, $length = NULL)
{
    if (cached_function_exists('mb_substr'))
        return mb_substr($string, $start, $length);

    return substr($string, $start, $length);
}

/**
 * If you want to support Multibyte Languages, use this function instead of strtolower.
 * Use this function only if $str can contain non latin chars.
 *
 * @param  string $str
 *
 * @return string
 */
function imstrtolower($str)
{
    return (cached_function_exists("mb_strtolower") ? mb_strtolower($str) : strtolower($str));
}

/**
 * If you want to support Multibyte Languages, use this function instead of strtoupper.
 * Use this function only if $str can contain non latin chars.
 *
 * @param  string $str
 *
 * @return string
 */
function imstrtoupper($str)
{
    return (cached_function_exists("mb_strtoupper") ? mb_strtoupper($str) : strtoupper($str));
}

/**
 * If you want to support Multibyte Languages, use this function instead of strlen.
 * Use this function only if $str can contain non latin chars or with other imstr functions.
 *
 * @param  string $str
 *
 * @return integer
 */
function imstrlen($str)
{
    if (cached_function_exists('mb_strlen'))
        return mb_strlen($str);

    return strlen($str);
}

/**
 * Get a localization string.
 * The string is taken from the ones specified at step 1 of WSX5
 *
 * @method l10n
 *
 * @param {string} $id      The localization key
 * @param {string} $default The default string
 *
 * @return {string}         The localization
 */
function l10n($id, $default = "")
{
    global $l10n;

    if (!isset($l10n[$id]))
        return $default;

    return $l10n[$id];
}

/**
 * Combine a series of paths
 *
 * @method pathCombine
 *
 * @param  {array}  $paths The array with the elements of the path
 *
 * @return {string} The path created combining the elements of the array
 */
function pathCombine($paths = array())
{
    $s = array();
    foreach ($paths as $path) {
        if (strlen($path)) {
            $s[] = trim($path, "/\\ ");
        }
    }
    return implode("/", $s);
}

/**
 * Check if the argument is an email address
 * @param  String  $email
 * @return boolean
 */
function isEmail($email)
{
    return strpos($email, "@") > 0 && preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $email);
}

if (!function_exists('htmlspecialchars_decode')) {
    /**
     * Fallback for htmlspecialchars_decode in PHP4
     * @ignore
     * @param  string  $text
     * @param  integer $quote_style
     * @return string
     */
    function htmlspecialchars_decode($text, $quote_style = ENT_COMPAT)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
    }
}

if (!function_exists('json_encode')) {
    /**
     * Fallback for json_encode
     * @ignore
     */
    function json_encode($data)
    {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addcslashes($data, "\/\"\n\r\f\t") . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}

/**
 * Check for the valid data about spam and js
 *
 * @param  string $expectedValue The expected value for the JS check field
 *
 * @return bool
 */
function checkJsAndSpam($expectedValue = "")
{
    // Spam!
    if (isset($_POST['prt']) && $_POST['prt'] != "") {
        return false;
    }

    // Javascript disabled
    if (!isset($_POST['imJsCheck'])) {
        echo imPrintJsError(false);
        return false;
    }

    if (strlen($_POST['imJsCheck']) && $_POST['imJsCheck'] != $expectedValue) {
        echo imPrintJsError(false);
        return false;
    }

    return true;
}

/**
 * Search if at least one element of $needle is in $haystack.
 * @param  Array   $needle   Non-associative array
 * @param  Array   $haystack Non-associative array
 * @param  boolean $all      Set to true to ensure that all the elements in $needle are in $haystack
 * @return boolean
 */
function in_array_field($needle, $haystack, $all = false)
{
    if ($all) {
        foreach ($needle as $key)
            if (!in_array($key, $haystack))
                return false;
        return true;
    } else {
        foreach ($needle as $key)
            if (in_array($key, $haystack))
                return true;
        return false;
    }
}

/**
 * Get the most recent date in the provided array of PHP times that do not define a future time.
 * The date is provided in the RSS format Sun, 15 Jun 2008 21:15:07 GMT
 * @param  array    $timeArr
 * @return string
 */
function getLastAvailableDate($timeArr) {
    if (count($timeArr) > 0) {
        sort($timeArr, SORT_DESC);
        $utcTime = time() + date("Z", time());
        foreach ($timeArr as $time) {
            if ($time <= $utcTime) {
                return date("r", $time);
            }
        }
    }
    return date("r", time());
}

/**
 * Update the query string adding or updating a variable with a specified value
 *
 * @param  string $name  The variable to be replaced
 * @param  string $value The value to set
 *
 * @return string        The updated query string
 */
function updateQueryStringVar($name, $value) {
    if (!isset($_SERVER['QUERY_STRING'])) return "";
    $qs = array();
    parse_str($_SERVER['QUERY_STRING'], $qs);
    $qs[$name] = $value;
    return http_build_query($qs);
}

/**
 * Get the email address from a string formatted like "John Doe <johndoe@email.com>"
 *
 * @param  String $email The email string
 *
 * @return String        The email address
 */
function addressFromEmail($email) {
    $start = strpos($email, "<");
    $end = strpos($email, ">");
    if ($start > 0 && $end !== false) {
        return trim(substr($email, $start + 1, $end - $start - 1));
    }
    return $email;
}

/**
 * Get the user name from a string formatted like "John Doe <johndoe@email.com>"
 *
 * @param  String $email The email string
 *
 * @return String        The user name
 */
function nameFromEmail($email) {
    $end = strpos($email, "<");
    if ($end > 0) {
        return trim(substr($email, 0, $end));
    }
    return "";
}


/**
 * Format a DateTime using normal or extended format, as configured by user in step 2 (default styles)
 *
 * @param  DateTime $date      The date to format
 * @param  boolean  $extended  true to use extended format, false otherwise
 * @param  boolean  $showDay   true to show day number and/or name, false to show only month and year
 * @param  boolean  $showTime  true to show time in hh:mm:ss format
 *
 * @return String              The formatted date
 */
function formatDate($date, $extended = false, $showDay = true, $showTime = false) {
    global $imSettings;

    $varName = 'date_format';
    if (!$showDay) $varName .= '_no_day';
    if ($extended) $varName .= '_ext';
    $format = $imSettings['general'][$varName];
    
    $date_full_months = l10n('date_full_months');
    $date_months = l10n('date_months');
    $date_full_days = l10n('date_full_days');
    $date_days = l10n('date_days');

    $month = $date->format('n');
    $dayOfWeek = $date->format('w') - 1;  // 'w' is Sunday to Saturday, while date_full_days and date_days are Monday to Sunday
	if ($dayOfWeek == -1) $dayOfWeek = 6;

    $format = str_replace('yyyy', $date->format('Y'),            $format);
    $format = str_replace('yy',   $date->format('y'),            $format);
    $format = str_replace('MMMM', '$$$$',                        $format);
    $format = str_replace('MMM',  '$$$',                         $format);
    $format = str_replace('MM',   $date->format('m'),            $format);
    $format = str_replace('M',    $month,                        $format);
    $format = str_replace('dddd', '§§§§',                        $format);
    $format = str_replace('ddd',  '§§§',                         $format);
    $format = str_replace('dd',   $date->format('d'),            $format);
    $format = str_replace('d',    $date->format('j'),            $format);
    $format = str_replace('$$$$', $date_full_months[$month - 1], $format);
    $format = str_replace('$$$',  $date_months[$month - 1],      $format);
    $format = str_replace('§§§§', $date_full_days[$dayOfWeek],   $format);
    $format = str_replace('§§§',  $date_days[$dayOfWeek],        $format);

    if ($showTime)
        $format .= ' ' . $date->format('H:i:s');

    return $format;
}

$imSettings = Array();
$l10n = Array();
$phpError = false;
$ImMailer = new ImSendEmail();

@include_once "imemail.inc.php";        // Email class - Static
@include_once "x5settings.php";         // Basic settings - Dynamically created by WebSite X5
@include_once "blog.inc.php";           // Blog data - Dynamically created by WebSite X5
@include_once "access.inc.php";         // Private area data - Dynamically created by WebSite X5
@include_once "l10n.php";               // Localizations - Dynamically created by WebSite X5
@include_once "search.inc.php" ;        // Search engine data - Dynamically created by WebSite X5



/**
* From Gravatar Help:
*        "A gravatar is a dynamic image resource that is requested from our server. The request
*        URL is presented here, broken into its segments."
* Source:
*    http://site.gravatar.com/site/implement
*
* Usage:
* <code>
*        $email = "youremail@yourhost.com";
*        $default = "http://www.yourhost.com/default_image.jpg";	// Optional
*        $gravatar = new Gravatar($email, $default);
*        $gravatar->size = 40;
*        $gravatar->rating = "G";
*        $gravatar->imageset = "identicon";
*        $gravatar->border = "FF0000";
*
*        echo $gravatar; // Or echo $gravatar->toHTML();
* </code>
*
* @access public
*/
class X5Gravatar {
    
    /**
     *    Gravatar's url
     */
    const GRAVATAR_URL = "https://www.gravatar.com/avatar.php";

    /**
     *    Ratings available
     */
    private $GRAVATAR_RATING = array("G", "PG", "R", "X");

    /**
     *    Default imageset
     */
    private $GRAVATAR_IMAGESET = array("404", "mp", "identicon", "monsterid", "wavatar");
    
    /**
     *    Query string. key/value
     */
    protected $properties = array(
        "gravatar_id"	=> NULL,
        "default"		=> NULL,
        "s"		        => 40,              // size: the default value
        "r"		        => "G",             // rating: the default rating
        "d"		        => "identicon",     // imageset: the default imageset
        "border"		=> NULL,
    );

    /**
     *    E-mail. This will be converted to md5($email)
     */
    protected $email = "";

    /**
     *    Extra attributes to the IMG tag like ALT, CLASS, STYLE...
     */
    protected $extra = "";

    /**
     *    
     */
    public function __construct($email=NULL, $default=NULL) {
        $this->setEmail($email);
        $this->setDefault($default);
    }

    /**
     *    
     */
    public function setEmail($email) {
        $this->email = $email;
        $this->properties['gravatar_id'] = md5(strtolower($this->email));
        return true;
    }

    /**
     *    
     */
    public function setDefault($default) {
        $this->properties['default'] = $default;
    }

    /**
     *    
     */
    public function setRating($rating) {
        if (in_array($rating, $this->GRAVATAR_RATING)) {
            $this->properties['r'] = $rating;
            return true;
        }
        return false;
    }

    /**
     *    
     */
    public function setImageset($imageset) {
        if (in_array($imageset, $this->GRAVATAR_IMAGESET)) {
            $this->properties['d'] = $imageset;
            return true;
        }
        return false;
    }

    /**
     *    
     */
    public function setSize($size) {
        $size = (int) $size;
        if ($size <= 0)
            $size = NULL;        // Use the default size
        $this->properties['s'] = $size;
    }

    /**
     *    
     */
    public function setExtra($extra) {
        $this->extra = $extra;
    }

    /**
     *    Object property overloading
     */
    public function __get($var) { return @$this->properties[$var]; }

    /**
     *    Object property overloading
     */
    public function __set($var, $value) {
        switch($var) {
            case "email":    return $this->setEmail($value);
            case "rating":   return $this->setRating($value);
            case "default":  return $this->setDefault($value);
            case "imageset": return $this->setImageset($value);
            case "size":     return $this->setSize($value);
            // Cannot set gravatar_id
            case "gravatar_id": return;
        }
        return @$this->properties[$var] = $value;
    }

    /**
     *    Object property overloading
     */
    public function __isset($var) { return isset($this->properties[$var]); }

    /**
     *    Object property overloading
     */
    public function __unset($var) { return @$this->properties[$var] == NULL; }

    /**
     *    Get source
     */
    public function getSrc() {
        $url = self::GRAVATAR_URL ."?";
        $first = true;
        foreach($this->properties as $key => $value) {
            if (isset($value)) {
                if (!$first)
                    $url .= "&";
                $url .= $key."=".urlencode($value);
                $first = false;
            }
        }
        return $url;    
    }

    /**
     *    toHTML
     */
    public function toHTML() {
        return     '<img src="'. $this->getSrc() .'" alt=""'
                .(!isset($this->s) ? "" : ' width="'.$this->s.'" height="'.$this->s.'"')
                .$this->extra
                .' />';    
    }

    /**
     *    toString
     */
    public function __toString() { return $this->toHTML(); }
}


// End of file