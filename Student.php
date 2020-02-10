<?php

//namespace App;

use App\Model;

class Student
{
    static function index($id = '')
    {
        if ($id == '') {
            die(json_encode("no id..."));
        }
        Model::getStudentData($id);
    }
}
