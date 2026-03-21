<?php
header('Content-Type: text/html; charset=utf-8');

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

if (isset($_GET['action'])) {
    switch($_GET['action']) {
        //check type of call action    
        case 'getThumb': 
            $id = $_POST["id"];
            youtube_image($id);
            break;
        default: break;
    }
} 

function youtube_image($id) {
    $resolution = array (
        'maxresdefault',
        'hqdefault',
        'mqdefault',
        'sddefault'
    );
    
    $url = "";
    for ($x = 0; $x < sizeof($resolution); $x++) {
        $url = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[$x] . '.jpg';
        if (strpos(get_headers($url)[0], '200') !== false) {
            break;
        }
    }
    echo json_encode($url);
    exit();
}
?>