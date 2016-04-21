<?php

/**
*   This parser does nothing
*/
class FileParser {

    private $task;

    private $content;


    public function __construct($task, $content) {
        $this->content = $content;
        $this->task = $task;
    }

    public function getFixedContent() {
        return $this->content;
    }

    public function createSubTasks() {

    }

}
