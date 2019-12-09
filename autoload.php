<?php

require_once "config/database.php";

spl_autoload_register(function ($className) {
    $directories = [
        "tools/",
        "app/controllers/",
        "app/models/"
    ];

    foreach ($directories as $directory) {
        if (file_exists($directory . $className . ".php")) {
            require_once $directory . $className . ".php";
        }
    }
});