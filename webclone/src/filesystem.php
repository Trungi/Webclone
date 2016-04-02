<?php

class FileSystem {

    public function __construct() {
    }

    public function save($directory, $filename, $file) {
        if (!file_exists($directory)) {
            llog("Creating directory: $directory");
            mkdir($directory, WEBCLONE_FILEMOD, true);
        }

        $fullPath = rtrim($directory, '/') . '/' . ltrim($filename, '/');

        llog("Saving as $fullPath");

        file_put_contents($fullPath, $file);
    }


}
