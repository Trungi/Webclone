<?php


class UrlParser {

    protected $url;

    protected $rootUrl;

    public function __construct($rootUrl, $url) {
        $this->url = $url;
        $this->rootUrl = $rootUrl;

        if (!$this->isValid()) {
            throw new InvalidURLException("$rootUrl ---- $url");
        }
    }

    public function getFilenamePath($default = 'index.html') {
        $rootPath = parse_url($this->rootUrl, PHP_URL_PATH);
        $urlPath = parse_url($this->url, PHP_URL_PATH);

        if ((!$rootPath && !$urlPath) || ($rootPath == $urlPath)) {
            $filename = $default;
        } else if (!$rootPath) {
            $filename = $urlPath;
        } else {
            if (!startsWith($urlPath, $rootPath)) {
                throw new InvalidURLException();
            }

            $filename = substr($urlPath, strlen($rootPath));
        }

        if (!$filename || ($filename && endsWith($filename, '/'))) {
            $filename = $filename + $default;
        }

        llog("Filename: $filename");
        return $filename;
    }

    public function isValid() {
        $root = parse_url($this->rootUrl);
        $url = parse_url($this->url);

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

        if ((isset($root['path']) && !startsWith($url['path'], $root['path']) && !empty($url['path'])) &&
            (startsWith($url['path'], '/'))) {
            llog("Path does not match: ".$root['path']." ---- ".$url['path']);
            return False;
        }

        return True;
    }

    public function getFullUrl() {
        $root = parse_url($this->rootUrl);
        $path = parse_url($this->url, PHP_URL_PATH);

        if ($path[0] == '/') {
            $root['path'] = $path;
        } else {
            if (!isset($root['path'])) {
                $root['path'] = '';
            }
            if (!endsWith($root['path'], '/')) {
                $root['path'] = $root['path'] . '/';
            }

            $root['path'] = $root['path'] . $path;
        }

        $root['query'] = parse_url($this->url, PHP_URL_QUERY);
        unset($root['fragment']);

        return unparse_url($root);
    }
}
