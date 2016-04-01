<?php

class FileSystem {

    public function __construct() {
    }

    public function save($directory, $filename, $file) {
        if (!file_exists($directory)) {
            mkdir($directory);
        }

        var_dump($directory);
        if (!endsWith($directory, '/')) {
            $directory = $directory . '/';
        }

        echo '<hr />';
        var_dump($directory);
        var_dump($filename);
        var_dump($directory . $filename);
        echo '<hr />';

        file_put_contents($directory . $filename, $file);
    }


}
