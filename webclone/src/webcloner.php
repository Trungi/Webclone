<?php

class WebCloner {

    protected $task;

    public function __construct($task) {
        $this->task = $task;
    }

    public function run() {
        $url = $this->task->getId();
        llog("Starting job: $url");

        $file = $this->download();
        llog("Saving job: $url");
        $this->save($file);

        // parse new content
        llog("Parsing job: $url");
        $parser = new XmlParser($this->task, $file);
        $tasks = $parser->getTasks();

        return $tasks;
    }

    protected function download() {
        $downloader = new Downloader($this->task);

        $filesize = $downloader->getSize();

        if ($filesize > WEBCLONE_MAXFILESIZE) {
            throw new DownloadSizeException();
        }

        $file = $downloader->download();
        return $file;
    }

    protected function save($file) {
        $filesystem = new FileSystem();

        $filesystem->save(
            $this->task->getSaveDir(),
            $this->task->getFilenamePath(),
            $file
        );
    }

}
