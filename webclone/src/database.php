<?php

class Database {

    protected $mysqli;

    public function __construct() {
        $this->mysqli = new mysqli(WEBCLONE_DB_HOST, WEBCLONE_DB_USER, WEBCLONE_DB_PASS, WEBCLONE_DB_DATABASE);
        $this->mysqli->query('SELECT DATABASE `'.WEBCLONE_DB_DATABASE.'`;');
    }

    public function createDocument($website_id, $url) {
        $this->mysqli->query(
            "INSERT INTO `".WEBCLONE_DB_TABLE_DOCUMENT."` (`website_id`, `url`)
             VALUES ('$website_id',  '$url');"
        );
    }

    public function updateDocument($id, $slug, $http_code, $content_type, $redirect_location, $headers, $done) {
        if (!$id) throw new Exception('Invalid ID');

        $this->mysqli->query(
            "UPDATE `".WEBCLONE_DB_TABLE_DOCUMENT."` 
             SET `slug`='$slug',`http_code`='$http_code',`response_headers`='$headers',`done`=$done,`redirect_location`='$redirect_location',`content_type`='$content_type'
             WHERE `id`='$id'; "
        );
    }

    public function getWebsite($id) {
        $result = $this->mysqli->query("SELECT * FROM `".WEBCLONE_DB_TABLE_SITE."` WHERE `id`=  $id")->fetch_array();
        return $result;
    }

    public function saveCookie($id, $cookie) {
        $this->mysqli->query(
            "UPDATE `".WEBCLONE_DB_TABLE_DOCUMENT."` 
             SET `cookie`='$cookie'
             WHERE `id`='$id'; "
        );
    }

    public function getNext() {
        $result = $this->mysqli->query("SELECT * FROM `".WEBCLONE_DB_TABLE_DOCUMENT."` WHERE `done`=  0 ORDER BY `id`;")->fetch_array();
        return $result;
    }

    public function getDocument($rootSlug, $url) {
        $result = $this->mysqli->query(
            "SELECT *, s.slug as site_slug, d.slug as document_slug 
             FROM ".WEBCLONE_DB_TABLE_DOCUMENT." d, ".WEBCLONE_DB_TABLE_SITE." s
             WHERE d.url= '$url' AND s.slug = '$rootSlug' AND d.website_id = s.id;"
        )->fetch_array();

        return $result;
    }
}