<?php

class XmlParser {

    private $task;

    private $content;

    private $parser;

    public function __construct($task, $content) {
        $this->content = $content;
        $this->task = $task;

        $this->parser = new PHPHtmlParser\Dom;
        $this->parser->load($content);
    }

    public function getTasks() {
        $tasks = array();

        foreach ($this->parser->find('[href]') as $link) {
            $task = $this->createTask($link->href);
            
            if ($task) {
                $tasks[] = $task;
            }
        }

        foreach ($this->parser->find('[src]') as $link) {
            $task = $this->createTask($link->src);

            if ($task) {
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    private function createTask($link) {
        try {
            $currentLink = $this->task->getUrl();

            $parser = new UrlParser($currentLink, $link);
            $url = $parser->getFullUrl();

            $task = new Task(
                $this->task->getRootDir(),
                $this->task->getRootUrl(),
                $url
            );

            return $task;
        } catch (InvalidURLException $e) {
            llog('Skipping');
        }
    }

    private function parseUrl($link) {
        return $link;
    }

}
