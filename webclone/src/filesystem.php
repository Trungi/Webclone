<?php

class FileSystem {

    public function __construct() {
    }

    public function save($subpath, $nextpath, $file) {
        $fullPath = rtrim($subpath, '/') . '/' . ltrim($nextpath, '/');
        $pos = strrpos($fullPath, '/');
        llog("Full path: $fullPath ---- $pos");

        $directory = substr($fullPath, 0, $pos+1);

        if (!file_exists($directory)) {
            llog("Creating directory: $directory");
            mkdir($directory, WEBCLONE_FILEMOD, true);
        }
        
        file_put_contents($fullPath, $file);
    }


}
