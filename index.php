<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include 'vendor/autoload.php';
include 'webclone/autoload.php';
include 'queue.php';



$url = 'http://www.st.fmph.uniba.sk/~trungel2/diploma/other.html';
$url = 'http://www.facebook.com';
$dir = '/tmp/web';
$filename = 'index.html';

$task = new Task($dir, $url, $url);
$clone = new WebCloner($task);

$result = $clone->run();


// recursively
while ($result) {
    try {
        reset($result);
        $key = key($result);

        $doc = $result[$key];
        unset($result[$key]);

        $clone = new WebCloner($doc);
        $xx = $clone->run();

        $result = array_merge($result, $xx);
    } catch (Exception $e) {
        var_dump($e);
    }
}

