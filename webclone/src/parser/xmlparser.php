<?php

class XmlParser {

    private $task;

    private $content;

    private $parser;

    public function __construct($task, $content) {
        $this->content = $content;
        $this->task = $task;

        $this->parser = new PHPHtmlParser\Dom;
    }

    public function getFixedContent() { return $this->content;
        $this->parser->load($this->content);

        $urlParser = new UrlParser();

        foreach ($this->parser->find('[href]') as $link) {
            $url = $urlParser->compileRelativeUrl(
                $this->task->website->rootUrl,
                $this->task->document->url,
                $link->href
            );
            $link->setAttribute('href', $url);
        }
        foreach ($this->parser->find('[src]') as $link) {
            $url = $urlParser->compileRelativeUrl(
                $this->task->website->rootUrl,
                $this->task->document->url,
                $link->src
            );
            $link->setAttribute('src', $url);
        }

        return $this->parser->outerHtml;
    }

    public function createSubTasks() {
        $this->parser->load($this->content);

        foreach ($this->parser->find('[href]') as $link) {
            $task = $this->createTask($link->href);
        }

        foreach ($this->parser->find('[src]') as $link) {
            $task = $this->createTask($link->src);
        }
    }

    private function createTask($url) {
        // TODO: remove
        if ($url == "http://spsz.6f.sk/home/logout") { llog("skipping logout"); return; }

        $this->task->createSubTask($url);
    }

    public function getLoginInfo() {
        $this->parser->load($this->content);
        $form = $this->parser->find("form");

        return array(
            'login_url' => $form->getAttribute('action')
        );

    }

}
