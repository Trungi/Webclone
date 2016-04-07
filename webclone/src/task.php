<?php


/**
*   Class for storing one task
*/
class Task {
    /**
    *   Document
    */
    public $document;

    /**
    *   Website
    */
    public $website;

    /**
    * Database access
    */
    private $database;

    public function __construct($website, $document) {
        if (is_array($document)) {
            $doc = new Document();

            foreach ($document as $key => $val) {
                $doc->$key = $val;
            }

            $document = $doc;
        }

        if (is_array($website)) {
            $doc = new Website();

            foreach ($website as $key => $val) {
                $doc->$key = $val;
            }

            $website = $doc;
        }

        if ($website->id != $document->website_id) {
            throw new Exception("Nope");
        }

        $this->document = $document;
        $this->website = $website;

        $this->database = new Database();
    }

    public function save() {
        llog("Saving task.");

        $this->database->updateDocument(
            $this->document->id,
            $this->document->slug,
            $this->document->http_code,
            $this->document->content_type,
            $this->document->redirect_location,
            $this->document->response_headers,
            $this->document->done
        );
    }

    public function createSubTask($url) {
        llog("Creating sub task: $url");

        $urlParser = new UrlParser();
        $fullUrl = $urlParser->compileFullUrl($this->getFullUrl(), $url);

        if ($urlParser->isValidSubUrl($this->website->rootUrl, $fullUrl) && startsWith($url, 'http')) {
            $urlPart = substr($fullUrl, strlen($this->website->rootUrl));

            $this->database->createDocument($this->website->id, $urlPart);
        }

    }

    public function generateRedirectLocation($url) {
        $urlParser = new UrlParser();
        $fullUrl = $urlParser->compileFullUrl($this->getFullUrl(), $url);

        if ($urlParser->isValidSubUrl($this->website->rootUrl, $fullUrl)) {
            $urlPart = substr($fullUrl, strlen($this->website->rootUrl));

            return $urlPart;
        } else {
            return $url;
        }
    }

    public function getFilename() {
        if (!$this->document->slug) {
            $this->document->slug = uniqid();
        }

        return WEBCLONE_ROOTDIR . $this->website->slug . '/' . $this->document->slug;
    }

    public function getFullUrl() {
        return $this->website->rootUrl . $this->document->url;
    }

    public function isLoggedIn() {
        return !empty($this->website->cookie);
    }

    public function saveCookie($cookie) {
        $this->website->cookie = $cookie;

        $this->database->saveCookie($this->website->id, $cookie);
    }
}
