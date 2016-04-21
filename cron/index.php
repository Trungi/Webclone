<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

include '../vendor/autoload.php';
include '../webclone/autoload.php';


// get first task
$db = new Database();
$x = $db->getNext();


while ($x) {
    // process task
    $site = $db->getWebsite($x['website_id']);
    $task = new Task($site, $x);
    $clone = new WebCloner($task);
    $result = $clone->run();

    // get new task
    $x = $db->getNext();
}
