<?php


class UrlParser {
    public function __construct() {}


    /**
    *   Checks if URL is subdirectory of rootUrl
    */
    public function isValidSubUrl($rootUrl, $url) {
        $root = parse_url($rootUrl);
        $url = parse_url($url);

        if ($root['scheme'] != 'http' && $root['scheme'] != 'https' && 
            (isset($url['scheme']) && $url['scheme'] != 'http' && $url['scheme'] != 'https')) {
            llog("Scheme does not match: ".$root['scheme']." ---- ".$url['scheme']);
            return False;
        }

        if (isset($root['port']) && isset($url['port']) && $root['port'] != $url['port']) {
            llog("Port does not match: ".$root['port']." ---- ".$url['port']);
            return False;
        }

        if (isset($url['host']) && $root['host'] != $url['host']) {
            llog("Host does not match: ".$root['host']." ---- ".$url['host']);
            return False;
        }

        if (isset($root['path'])) {
            $root['path'] = $this->_cleanPath($root['path']);
        }

        if ((isset($root['path']) && !startsWith($url['path'], $root['path']) && !empty($url['path'])) &&
            (startsWith($url['path'], '/'))) {
            llog("Path does not match: ".$root['path']." ---- ".$url['path']);
            return False;
        }

        return True;
    }

    /**
    *   Change path from $sourceUrl to $destinationUrl
    *   destinationUrl must be subdirectory of source URL
    *   $sourceUrl must be full type URl
    *   $destinationUrl can be relative or absolute
    */
    public function compileURLdiff($sourceUrl, $destinationUrl) {
        return substr($this->_getFullUrl($sourceUrl, $destinationUrl), strlen($sourceUrl));
    }


    /**
    *   Change path from $sourceUrl to $destinationUrl and return FULL URL
    *   $sourceUrl must be full type URl
    *   $destinationUrl can be relative or absolute
    */
    public function compileFullUrl($sourceUrl, $destinationUrl) {
        $root = parse_url($sourceUrl);
        $path = parse_url($destinationUrl, PHP_URL_PATH);

        // merge root URL and request URL paths
        if (!empty($path) && $path[0] == '/') {
            $root['path'] = $path;
        } else {
            // remove file from root URL e.g. http://aaa.com/index.php -> http://aaa.com
            if (!isset($root['path'])) {
                $root['path'] = '';
            } else {
                $root['path'] = $this->_cleanPath($root['path']);
            }

            $root['path'] = $root['path'] . $path;
        }

        // set query from URL
        $root['query'] = parse_url($destinationUrl, PHP_URL_QUERY);
        unset($root['fragment']);

        return unparse_url($root);

    }

    /**
    *   Change path from $sourceUrl to $destinationUrl and return relative URL from source to destination
    *   $sourceUrl must be full type URl
    *   $destinationUrl can be relative or absolute
    */
    public function compileRelativeUrl($rootUrl, $sourceUrl, $destinationUrl) {
        if (!startsWith($destinationUrl, $rootUrl)) {
            echo("<br />=-------------------------------=-==-=-=-=- $destinationUrl<br />");
            return $destinationUrl;
        }
        llog("PARSING $rootUrl------$destinationUrl");

        $fromUrl = parse_url($this->compileFullUrl($rootUrl, $sourceUrl), PHP_URL_PATH);
        $toUrl = parse_url($this->compileFullUrl($rootUrl, $destinationUrl), PHP_URL_PATH);

        $root = parse_url($this->_cleanPath($rootUrl), PHP_URL_PATH);

        $returns = substr_count($fromUrl, '/') - substr_count($root, '/');

        // build folders up
        $result = "";

        for ($i=0; $i<$returns; $i++) {
            $result = $result . '../';
        }

        // build folders down
        $result = $result . substr($toUrl, strlen($root));

        llog("RESULT IS $rootUrl || $sourceUrl || $destinationUrl || $result");
        return $result;

    }


    // remove file and return just path
    private function _cleanPath($path) {
            // if this is a file, delete file nad use just path
            if (strpos($path, '.') !== false) {
                $path = substr($path, 0, strrpos($path, '/'));
            }
            if (!endsWith($path, '/')) {
                $path = $path.'/';
            }

            return $path;
    }
}


class OldUrlParser {

    protected $url;

    protected $rootUrl;

    public function __construct($rootUrl, $url, $checkUrl = True) {
        $this->url = $url;
        $this->rootUrl = $rootUrl;

        if ($checkUrl) {
            if (!$this->isValidSubUrl($rootUrl, $url)) {
                throw new InvalidURLException("$rootUrl ---- $url");
            }
        }
    }

    protected function isValidSubUrl($rootUrl, $url) {
        $root = parse_url($rootUrl);
        $url = parse_url($url);

        if ($root['scheme'] != 'http' && $root['scheme'] != 'https' && 
            (isset($url['scheme']) && $url['scheme'] != 'http' && $url['scheme'] != 'https')) {
            llog("Scheme does not match: ".$root['scheme']." ---- ".$url['scheme']);
            return False;
        }

        if (isset($root['port']) && isset($url['port']) && $root['port'] != $url['port']) {
            llog("Port does not match: ".$root['port']." ---- ".$url['port']);
            return False;
        }

        if (isset($url['host']) && $root['host'] != $url['host']) {
            llog("Host does not match: ".$root['host']." ---- ".$url['host']);
            return False;
        }

        if (isset($root['path'])) {
            $root['path'] = $this->cleanPath($root['path']);
        }

        if ((isset($root['path']) && !startsWith($url['path'], $root['path']) && !empty($url['path'])) &&
            (startsWith($url['path'], '/'))) {
            llog("Path does not match: ".$root['path']." ---- ".$url['path']);
            return False;
        }

        return True;
    }

    public function getFullUrl() {
        return $this->_getFullUrl($this->rootUrl, $this->url);
    }

    public function getPartialUrl() {
        return substr($this->_getFullUrl($this->rootUrl, $this->url), strlen($this->rootUrl));
    }

    public function getRelativeUrl($toUrl) {
        if (!$this->isValidSubUrl($this->rootUrl, $toUrl)) {
            throw new InvalidURLException("$this->rootUrl, $toUrl,,, Can not find relative URL.");
        }

        llog("PARSING $this->rootUrl------$toUrl");

        $fromUrl = parse_url($this->_getFullUrl($this->rootUrl, $this->url), PHP_URL_PATH);
        $toUrl = parse_url($this->_getFullUrl($this->rootUrl, $toUrl), PHP_URL_PATH);

        $root = parse_url($this->cleanPath($this->rootUrl), PHP_URL_PATH);

        $returns = substr_count($fromUrl, '/') - substr_count($root, '/');

        // build folders up
        $result = "";

        for ($i=0; $i<$returns; $i++) {
            $result = $result . '../';
        }

        // build folders down
        $result = $result . substr($toUrl, strlen($root));

        llog("RESULT IS $result");
        return $result;
    }

    private function _getFullUrl($root, $url) {
        $root = parse_url($root);
        $path = parse_url($url, PHP_URL_PATH);

        // merge root URL and request URL paths
        if ($path[0] == '/') {
            $root['path'] = $path;
        } else {
            // remove file from root URL e.g. http://aaa.com/index.php -> http://aaa.com
            if (!isset($root['path'])) {
                $root['path'] = '';
            } else {
                $root['path'] = $this->cleanPath($root['path']);
            }

            $root['path'] = $root['path'] . $path;
        }

        // set query from URL
        $root['query'] = parse_url($this->url, PHP_URL_QUERY);
        unset($root['fragment']);

        return unparse_url($root);
    }


    // remove file and return just path
    private function cleanPath($path) {
            // if this is a file, delete file nad use just path
            if (strpos($path, '.') !== false) {
                $path = substr($path, 0, strrpos($path, '/'));
            }

            return $path;
    }
}
