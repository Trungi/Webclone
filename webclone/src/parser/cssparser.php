<?php

class XmlParser {

    private $task;

    private $content;

    private $regex = "/@import \s*u?r?l?\(?[\"'](.*)[\"']\)?/i";

    public function __construct($task, $content) {
        $this->content = $content;
        $this->task = $task;
    }

    public function getFixedContent() { return $this->content;
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

        return preg_replace_callback(
            $this->regex,
            function ($matches) use ($urlParser) {
                $link = $matches[0];
                
                return $urlParser->compileRelativeUrl(
                    $this->task->website->rootUrl,
                    $this->task->document->url,
                    $link->src
                );
            },
            $this->content
        );
    }

    public function createSubTasks() {
        preg_match($this->regex, $this->content, $matches);

        foreach ($match as $matches) {
            $this->createTask($match[0]);
        }

    }

    private function createTask($url) {
        $this->task->createSubTask($url);
    }

}
