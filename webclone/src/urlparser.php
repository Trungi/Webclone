<?php


class UrlParser {

    protected $url;

    protected $rootUrl;

    public function __construct($rootUrl, $url) {
        $this->url = $url;
        $this->rootUrl = $rootUrl;

        if (!$this->isValidSubUrl($rootUrl, $url)) {
            throw new InvalidURLException("$rootUrl ---- $url");
        }
    }

    /* This is not used anymore! */
    // public function getFilenamePath($default = 'index.html') {
    //     $rootPath = parse_url($this->rootUrl, PHP_URL_PATH);
    //     $urlPath = parse_url($this->url, PHP_URL_PATH);

    //     if ((!$rootPath && !$urlPath) || ($rootPath == $urlPath)) {
    //         $filename = $default;
    //     } else if (!$rootPath) {
    //         $filename = $urlPath;
    //     } else {
    //         if (!startsWith($urlPath, $rootPath)) {
    //             throw new InvalidURLException();
    //         }

    //         $filename = substr($urlPath, strlen($rootPath));
    //     }

    //     if (!$filename || ($filename && endsWith($filename, '/'))) {
    //         $filename = $filename + $default;
    //     }

    //     llog("Filename: $filename");
    //     return $filename;
    // }

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

        if ($path[0] == '/') {
            $root['path'] = $path;
        } else {
            if (!isset($root['path'])) {
                $root['path'] = '';
            } else {
                $root['path'] = $this->cleanPath($root['path']);
            }

            $root['path'] = $root['path'] . $path;
        }

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

            if (!endsWith($path, '/')) {
                $path = $path . '/';
            }

            return $path;
    }
}
