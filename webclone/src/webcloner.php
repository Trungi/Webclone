<?php

class WebCloner {

    protected $task;

    public function __construct($task) {
        $this->task = $task;
    }

    public function run() {
        $file = $this->download();
        $this->save($file);

        // parse new content
        $parser = new Parser($this->task, $file);
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
            $this->task->getFilename(),
            $file
        );
    }

}
