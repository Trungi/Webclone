<?php

class UrlParser {

    protected $url;

    protected $rootUrl;

    public function __construct($rootUrl, $url) {
        $this->url = $url;
        $this->rootUrl = $rootUrl;
    }

    public function getFilename($default = 'index.html') {
        return $default;
    }

    public function getFullUrl() {
        
    }
}
