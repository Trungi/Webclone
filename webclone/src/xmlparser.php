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
            $tasks[] = $this->createTask($link->href);
        }

        foreach ($this->parser->find('[src]') as $link) {
            $tasks[] = $this->createTask($link->src);
        }

        return $tasks;
    }

    private function createTask($link) {
        $task = new Task(
            $this->task->getRootDir(),
            $this->task->getRootUrl(),
            $link
        );

        return $task;
    }

    private function parseUrl($link) {
        return $link;
    }

}
