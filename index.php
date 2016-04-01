<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include 'vendor/autoload.php';
include 'webclone/autoload.php';
include 'queue.php';

define("WEBCLONE_MAXFILESIZE", 50000);


$url = 'http://www.st.fmph.uniba.sk/~trungel2/';
$url = 'http://www.google.com';
$dir = '/tmp/web';
$filename = 'index.html';

$task = new Task($dir, $url, $url);
$clone = new WebCloner($task);

$result = $clone->run();

foreach ($result as $r) {
    $clone = new WebCloner($r);
    $result = $clone->run();
}
echo '<hr /><br />';
var_dump($result);
//////







