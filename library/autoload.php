<?php

namespace Visit;

class Autoload
{
    public function __construct()
    {
        spl_autoload_register([$this, 'loadClasses']); // register the autoload function
    }
    public function loadClasses()
    {
        $dir = __DIR__ . '/'; // set the path to the library directory

        // get all PHP files in the library directory
        $files = glob($dir . 'class-*.php');

        foreach ($files as $file) {
            require_once $file; // load each PHP file
        }
    }
}

$autoload = new Autoload(); // create a new instance of the class
