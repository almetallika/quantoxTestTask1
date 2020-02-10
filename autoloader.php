<?php

spl_autoload_register(function ($class) {

    $classFile = $_SERVER['DOCUMENT_ROOT'].'/'.trim($class, 'App\\').".php";

    if (file_exists($classFile)) {
        include $classFile;
    }
});