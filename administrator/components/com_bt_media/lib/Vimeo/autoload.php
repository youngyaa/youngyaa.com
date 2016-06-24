<?php
spl_autoload_register(function ($class) {
    // Make sure that the class being loaded is in the vimeo namespace
    if (substr(strtolower($class), 0, 6) !== 'vimeo\\') {
        return;
    }
    // Locate and load the file that contains the class
    $path = dirname(__DIR__) . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {

        require($path);
    }
});