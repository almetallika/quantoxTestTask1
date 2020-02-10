<?php


namespace App;
use App\Db;

class Model
{
    static function getStudentsList()
    {
        return db::super_query("SELECT s.id, s.name, b.name as schoolBoard FROM students s LEFT JOIN boards b ON (b.id=s.schoolBoard)",true);
    }

    static function getStudentData($studentId)
    {
        $pass = "false";
        $studentData = db::super_query("SELECT s.id, s.name, b.name as board, b.ruleSet, b.conditions, b.value, b.format 
											FROM students s
											LEFT JOIN boards b ON (b.id=s.schoolBoard)
											WHERE s.id='{$studentId}'");
        $studentData['grades'] = db::super_query("SELECT grade FROM grades
											WHERE student='{$studentId}'",true);
        switch ($studentData['ruleSet']) {
            case "average":
                $query = "SELECT AVG(grade) as value FROM grades WHERE student='{$studentId}'";
                break;
            case "max":
                $query = "SELECT MAX(grade) as value FROM grades WHERE student='{$studentId}'";
                break;
        }
        $threshold = db::super_query($query);

        switch($studentData['conditions']){
            case ">":
                if ($threshold['value'] > $studentData['value']) {
                    $pass = "true";
                }
                break;
            case ">=":
                if ($threshold['value'] >= $studentData['value']) {
                    $pass = "true";
                }
                break;
        }

        $studentData['threshold'] = $threshold['value'];
        $studentData['pass'] = $pass;

        self::out($studentData, $studentData['format']);
    }

    static function out($data, $outMode = "JSON")
    {
        $out = '';
        switch ($outMode) {
            case "XML":
                $out = self::arrayToXml($data);
                break;
            case "JSON":
            default:
                $out = json_encode($data);
                break;
        }
        echo $out;
    }

    static function arrayToXml($array, $rootElement = null, $xml = null) {
        $_xml = $xml;

        if ($_xml === null) {
            $_xml = new \SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) { //nested array
                self::arrayToXml($v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }

}