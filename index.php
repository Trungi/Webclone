<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include 'vendor/autoload.php';
include 'webclone/autoload.php';

$url = 'http://www.st.fmph.uniba.sk/~trungel2/';
// $url = 'https://www.facebook.com/unsupportedbrowser';
// $url = 'http://www.st.fmph.uniba.sk/~jelen4/photobooth_for_windows_7_by_vhanla.zip';
$dir = '/tmp/web/';
$filename = 'index.html';

$site = new Website();
$site->id = 1;
$site->slug = 'abc';
$site->rootUrl = $url;

$doc = new Document();
$doc->website_id = 1;
$doc->id = 2;
$doc->url = '';
$doc->done = 0;

$db = new Database();
$x = $db->getNext();

while ($x) {
    $site = $db->getWebsite($x['website_id']);
    $task = new Task($site, $x);
    $clone = new WebCloner($task);
    $result = $clone->run();

    $x = $db->getNext();
}


// recursively
// while ($result) {
//     try {
//         reset($result);
//         $key = key($result);

//         $doc = $result[$key];
//         unset($result[$key]);

//         $clone = new WebCloner($doc);
//         $xx = $clone->run();

//         $result = array_merge($result, $xx);
//     } catch (Exception $e) {
//         var_dump($e);
//     }
// }

