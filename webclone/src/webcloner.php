<?php

class WebCloner {

    protected $task;

    protected $downloader;

    public function __construct($task) {
        $this->task = $task;
        $this->downloader = new Downloader($task);
    }

    public function run() {
        $url = $this->task->getFullUrl();
        llog("Starting job: $url");

        $info = $this->downloader->getFileInfo();
        llog("Response status code is: ".$info['http_code']);

        switch ($info['http_code']) {
            case '200':
                $this->_handleOK($info);
                break;
            case '400':
            case '401':
            case '402':
            case '403':
            case '404':
                $this->_handleNotFound($info);
                break;
            case '301':
            case '302':
                $this->_handleRedirect($info);
                break;
            default:
                llog("ERROR: UNEXPECTED STATUS CODE!");
        }
    }

    private function _handleRedirect($info) {
        // fill task information
        $this->task->document->redirect_location = $this->task->generateRedirectLocation($info['Location']);
        $this->task->document->http_code = $info['http_code'];
        $this->task->document->response_headers = json_encode($info);
        $this->task->document->done = 1;

        // create new task where redirect leads
        $this->task->createSubTask($info['Location']);

        // save them
        $this->task->save();
    }

    private function _handleNotFound($info) {
        // fill task information
        $this->task->document->http_code = $info['http_code'];
        $this->task->document->response_headers = json_encode($info);
        $this->task->document->done = 1;

        // save them
        $this->task->save();
    }

    private function _handleOK($info) {
        if (!$this->task->isLoggedIn() && in_array($info['Content-Type'], array('text/html', 'text/xml', 'text/xhtml'))) {
            $this->_login($info);
        }

        $content = $this->downloader->download();

        $parser = null;

        switch ($info['Content-Type']) {
            case 'text/html':
            case 'text/xhtml':
            case 'text/xml':
                $parser = new XmlParser($this->task, $content);
                break;
            default:
                $parser = new FileParser($this->task, $content);
                break;
        }

        $parser->createSubTasks();
        $fixedContent = $parser->getFixedContent();

        $filename = $this->task->getFilename();
        file_put_contents($filename, $fixedContent);

        $this->task->document->done = 1;
        $this->task->document->http_code = $info['http_code'];
        $this->task->document->content_type = $info['Content-Type'];
        $this->task->document->response_headers = json_encode($info);
        $this->task->save();
    }

    private function _login($info) {
        $content = $this->downloader->download();

        $parser = new XmlParser($this->task, $content);
        $loginInfo = $parser->getLoginInfo();

        $cookie = $this->downloader->login($loginInfo['login_url']);

        if ($cookie) {
            $this->task->saveCookie($cookie);
        }
        var_dump($cookie);
        return $cookie;
    }

}
