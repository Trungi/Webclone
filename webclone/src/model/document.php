<?php

class Document {

    /**
    *   Id
    */
    public $id;

    /**
    *   Id of website this document belongs to
    */
    public $website_id;

    /**
    *   Original URL of this document relative to root
    */
    public $url;

    /**
    *   Filename
    */
    public $slug;

    /**
    *   Status code returned while copying this doc
    */
    public $http_code;

    /**
    *
    */
    public $content_type;

    /**
    *   If this is a redirect, where should we be redirected
    */
    public $redirect_location;

    /**
    *   Response headers
    */
    public $response_headers;

    /**
    *   Is this document processed?
    */
    public $done;
}