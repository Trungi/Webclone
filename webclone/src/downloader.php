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

        // if we are logged in, set cookie
        if ($this->task->isLoggedIn()) {
            curl_setopt($curl, CURLOPT_COOKIE, $this->task->website->cookie);
        }

        $data = curl_exec( $curl );
        curl_close( $curl );

        return $this->getHeaders($data);
    }

    public function login($loginUrl) {
        $username           = $this->task->website->login;
        $password           = $this->task->website->password;

        //init curl
        $ch = curl_init();

        //Set the URL to work with
        curl_setopt($ch, CURLOPT_URL, $loginUrl);

        // ENABLE HTTP POST
        curl_setopt($ch, CURLOPT_POST, 1);

        // set user agent
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36');

        //Set the post parameters
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password);

        // do not follow location
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );

        //Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
        //not to print out the results of its query.
        //Instead, it will return the results as a string return value
        //from curl_exec() instead of the usual true/false.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // track request header
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        // read headers
        curl_setopt($ch, CURLOPT_HEADER, 1);

        //execute the request (the login)
        $data = curl_exec($ch);
        
        // var_dump(curl_getinfo($ch)); echo '<hr />';

        curl_close($ch);
        // find cookies
        $cookie = '';
        $pattern = '/Set-Cookie:(.*?)\n/';

        if (preg_match_all($pattern, $data, $result)) {
            $cookie = implode(';', $result[1]);
        } else {
            $cookie = null;
        }

        return $cookie;
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
            $status = explode(' ', $headers['http_code']);
            $headers['http_code'] = $status[1];
        }
        // fix content type
        if ($response) {
            $type = explode(';', $headers['Content-Type']);
            $headers['Content-Type'] = $type[0];
        }

        return $headers;
    }

    /**
    *   Download the file
    */
    public function download() {
        $url = $this->task->getFullUrl();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        // Issue a HEAD request and not follow any redirects.
        // curl_setopt( $curl, CURLOPT_NOBODY, true );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false );

        // if we are logged in, set cookie
        if ($this->task->isLoggedIn()) {
            curl_setopt($curl, CURLOPT_COOKIE, $this->task->website->cookie);
        }

        $file = curl_exec( $curl );
        curl_close( $curl );

        if ($file === false) {
            throw new \Exception("Wrong file");
        }

        return $file;
    }
}
