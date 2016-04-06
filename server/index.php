<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

require_once '../webclone/settings.php';
require_once '../webclone/src/database.php';

$uri = $_SERVER['REQUEST_URI'];

// delete first '/'
$uri = substr($uri, 1);

// split to slug and url
$pos = strpos($uri, '/');
$slug = substr($uri, 0, $pos);
$url = substr($uri, $pos+1);

$db = new Database();
$result = $db->getDocument($slug, $url);

// handle 200 OK
if ($result['http_code'] == 200) {
    $file =  WEBCLONE_ROOTDIR . $result['site_slug'] . '/' . $result['document_slug'];
    header("Content-Type: ".$result['content_type']);
    readfile($file);
} 
// handle REDIRECT
elseif ($result['http_code'] == 301 or $result['http_code'] == 302) {
    header("Location: ".$result['redirect_location']);
    die();
}
// handle ERROR
elseif ($result['http_code'] == 404 or $result['http_code'] == 302) {
    header("HTTP/1.0 404 Not Found");
    die();
}