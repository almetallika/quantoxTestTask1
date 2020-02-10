<?php


namespace App;

use App\Db;
use App\Model;
use App\Student;
use App\Students;

class Route
{
    static function router($uri)
    {
        if (strlen($uri) == 1) {
            die(json_encode("no parameters..."));
        }
        $routing =  explode('/', substr($uri,1));
        $controller = ucfirst($routing[0]);
        $action = "index";
        $parameters = ($routing[1]) ?: "";
        $controller::$action($parameters);
    }
}
