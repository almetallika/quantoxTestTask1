<?php


//namespace App;
use App\Model;

class Students
{
    static function index()
    {
        $result = Model::getStudentsList();
        if ($result) {
            echo json_encode($result);
        }
    }
}