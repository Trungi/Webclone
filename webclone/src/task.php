<?php

class Task {
    const xmlExtensions = array("xml", "xhtml", "html", "html5", "php", "php5");

    protected $url;

    protected $session;

    protected $rootDir;

    protected $filename;

    public function __construct($rootDir, $rootUrl, $url, $session = null) {
        $this->rootDir = $rootDir;
        $this->rootUrl = $rootUrl;

        $urlParser = new UrlParser($rootUrl, $url);
        $this->url = $urlParser->getFullUrl();

        $id = $this->getId();
        $url = $this->getUrl();
        llog("Created task: rootDir::$rootDir, rootUrl::$rootUrl, url::$url, id::$id");
    }

    public function getUrl() {
        return $this->url;
    }

    public function getId() {
        return $this->url;
    }

    public function getRootDir() {
        return $this->rootDir;
    }

    public function getRootUrl() {
        return $this->rootUrl;
    }

    public function getSaveDir() {
        return $this->rootDir;
    }

    public function getFilenamePath() {
        $parser = new UrlParser($this->rootUrl, $this->url);
        return $parser->getFilenamePath();
    }

    public function isXml() {
        if (endsWith($url, '/')) {
            return True;
        }

        foreach ($this->xmlExtensions as $extension) {
            if (endsWith($url, $extension)) {
                return True;
            }
        }
        return False;
    }
}
