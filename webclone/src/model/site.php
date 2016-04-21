<?php

class Website {

    /**
    *   Id
    */
    public $id;

    /**
    *   Slug is used for save folder name of this website
    *   It is also used for serving files of this website
    */
    public $slug;

    /**
    *   Auth URL of this webpage. This is URL used for authentication.
    */
    public $authUrl;

    /**
    *   Information used for logging in to this page
    */
    public $login;

    /**
    *   Information used for logging in to this page
    */
    public $password;

    /**
    *   Filename where the login cookie is stored after successful login
    */
    public $cookie;

    /**
    *   Root URL of this webpage. This is URL user submitted for copying.
    */
    public $rootUrl;
}