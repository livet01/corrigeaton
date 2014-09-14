<?php
namespace Corrigeaton\Bundle\ScheduleBundle\Service;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;

class ADEService
{

    private $urlPlanning;

    public function __construct($urlPlanning)
    {
        $this->urlPlanning = $urlPlanning;
    }

    public function findClassroomName(Classroom $classroom)                                                        // Give the class' name using the class' id
    {
        $week = file_get_contents(sprintf($this->urlPlanning,$classroom->getId()));                                                               // Open the ics in 'week'
        $res = array();                                                                                 // Array for the reg match result's
        preg_match("/CALNAME:([^ ]*)/", $week, $res);                                                   // Reg Exp which find the name in brackets
        return $res[1];                                                                                 //
    }

    public function findTestsTeacher(Test $test)                                                        // Give the teacher's name for a given test(=param)
    {
        $uid = $test->getFinishToken();                                                                 // The Finish Token is the uid of his Test
        $classNum = $test->getClassrooms(0)->getClassNum();                                             // Give the id of a class related to the test
        $res = array();                                                                                 //
        $url = "https://srv-ade.insa-toulouse.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?resources="
            .$classNum."&projectId=4&calType=ical&firstDate=2014-08-04&lastDate=2015-07-31";            // Url to the calendar of the class chosed
        $week = file_get_contents($url);                                                                // Put the calendar (ics version) in 'week'
        $regexp = "/([^ n]*) P.\\\\n[(][^()]*[)]\nuid:".$uid;                                           // Reg Exp which find the teacher related to a test (found because of the uid)
        preg_match($regexp, $week, $res);                                                               // Search for the teacher's name in brackets
        return $res[1];
    }

    public function findTeachersAdress($name) // Give the mail adress of a teacher with his name(=param)
    {
        $res = array();                                                                                 // Array for the reg match result's
        $data = array('namefield' => '', 'first_namefield' => '', 'telephonefield' => '', 'pageLength' => '',
            'startIndex' => '0', '_query' => 'Annuaire', 'texteNom' => $name, 'textePrenom' =>'', 'texteTelephone' => '',
            'slctAffectation' => '-1', 'slctFonction' => '------+Inconnue+------', 'n-page' => '10' );  // Array of POST values
        $regexp = "/N100[^ ]{2}=\"([^\".]*[.]".$name.")/i";                                             // Reg exp, surname.name in brackets, i option : non sensible to case
        $ch = curl_init();                                                                              // Initiation of the curl request
        curl_setopt($ch, CURLOPT_URL, 'www.insa-toulouse.fr/fr/annuaire.html');                         // Url of the form
        curl_setopt($ch, CURLOPT_POST, 1);                                                              // Request-type : POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                    // Give the array of values
        curl_setopt($ch, CURLOPT_HEADER, FALSE);                                                        // Bullshit
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                 // Bullshit
        $output = curl_exec($ch);                                                                       // Launch the request, result in 'output'
        preg_match($regexp, $output, $res);                                                             // Results in res
        $email = $res[1]."@";                                                                           // Here and after : building the mail adress
        preg_match("/N100[^ ]{2} \\+=\"([^&\"]+)/", $output, $res);                                     //
        $email = $email.$res[1];                                                                        //
        return $email;                                                                                  //
    }
}