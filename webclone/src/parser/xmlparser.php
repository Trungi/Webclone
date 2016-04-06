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

    public function getFixedContent() {
        return $this->content;
        // $this->parser->load($this->content);

        // foreach ($this->parser->find('[href]') as $link) {
        //     try {
        //         $href = $this->task->getRelativeUrl($link->href);
        //         $link->setAttribute('href', $href);
        //     } catch (InvalidURLException $e) {

        //     }
        // }
        // foreach ($this->parser->find('[src]') as $link) {
        //     try {
        //         $src = $this->task->getRelativeUrl($link->src);
        //         $link->setAttribute('src', $src);
        //     } catch (InvalidURLException $e) {

        //     }
        // }

        // return $this->parser->outerHtml;
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
        $this->task->createSubTask($url);
    }

}
