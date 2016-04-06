<?php

$uri = $_SERVER['REQUEST_URI'];

$url = 'http://www.st.fmph.uniba.sk/~trungel2' . $uri;
$folder = '/tmp/web/';

require_once '../webclone/src/database.php';

$db = new Database();
$result = $db->getByUrl($url);

$file =  $folder . $result['filename'];

readfile($file);

// $x = file_get_contents($file);
// echo $x;