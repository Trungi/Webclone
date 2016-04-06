<?php


/**
*   Class for CURL handling
*/
class Downloader {

    /**
    *   Current task object
    */
    protected $task;

    /**
    *   Construct
    */
    public function __construct($task) {
        $this->task = $task;
    }

    /**
    *   Issue an curl request to get file information
    */
    public function getFileInfo() {
        $url = $this->task->getFullUrl();

        $curl = curl_init( $url );

        // Issue a HEAD request and not follow any redirects.
        curl_setopt( $curl, CURLOPT_NOBODY, true );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false );

        $data = curl_exec( $curl );
        curl_close( $curl );

        return $this->getHeaders($data);
    }

    public function login() {
        $username = 'bob';
        $password = 'bob';
        $loginUrl = 'http://spsz.6f.sk/verifylogin';
        $cookieFilename = '/var/web/cookie.txt';

        //init curl
        $ch = curl_init();

        //Set the URL to work with
        curl_setopt($ch, CURLOPT_URL, $loginUrl);

        // ENABLE HTTP POST
        curl_setopt($ch, CURLOPT_POST, 1);

        //Set the post parameters
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password);

        //Handle cookies for the login
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookeFilename);

        //Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
        //not to print out the results of its query.
        //Instead, it will return the results as a string return value
        //from curl_exec() instead of the usual true/false.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //execute the request (the login)
        $store = curl_exec($ch);

        return file_exists($cookieFilename);
    }

    /**
    *   Transforms HTTP headers into PHP array
    */
    private function getHeaders($response) {
        $headers = array();

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }

        // fix status code
        if ($response) {
            var_dump($response);
            $status = explode(' ', $headers['http_code']);
            $headers['http_code'] = $status[1];
        }
        return $headers;
    }

    /**
    *   Download the file
    */
    public function download() {
        $file = file_get_contents($this->task->getFullUrl());

        if ($file === false) {
            throw new \Exception("Wrong file");
        }

        return $file;
    }
}
