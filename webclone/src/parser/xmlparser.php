<?php
use \Wa72\HtmlPageDom\HtmlPageCrawler;

class XmlParser {

    private $task;

    private $content;

    private $page;

    public function __construct($task, $content) {
        $this->content = $content;
        $this->task = $task;

        $this->page = new HtmlPageCrawler($content);
    }

    public function getFixedContent() {
        $urlParser = new UrlParser();

        foreach ($this->page->filter('[href]') as $link) {
            $url = $urlParser->compileRelativeUrl(
                $this->task->website->rootUrl,
                $this->task->document->url,
                $link->getAttribute('href')
            );

            $link->setAttribute('href', $url);
        }
        foreach ($this->page->filter('[src]') as $link) {
            $url = $urlParser->compileRelativeUrl(
                $this->task->website->rootUrl,
                $this->task->document->url,
                $link->getAttribute('src')
            );

            $link->setAttribute('src', $url);
        }

        return $this->page->saveHTML();
    }

    public function createSubTasks() {
        foreach ($this->page->filter('[href]') as $link) {
            $task = $this->createTask($link->getAttribute('href'));
        }

        foreach ($this->page->filter('[src]') as $link) {
            $task = $this->createTask($link->getAttribute('src'));
        }
    }

    private function createTask($url) {
        // TODO: remove
        if ($url == "http://spsz.6f.sk/home/logout") { llog("skipping logout"); return; }

        $this->task->createSubTask($url);
    }

}
