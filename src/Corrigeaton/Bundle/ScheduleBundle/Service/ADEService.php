<?php
namespace Corrigeaton\Bundle\ScheduleBundle\Service;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException;
use Symfony\Component\DomCrawler\Crawler;

class ADEService
{

    private $urlPlanning;
    private $urlAnnuaire;

    public function __construct($urlPlanning, $urlAnnuaire)
    {
        $this->urlPlanning = $urlPlanning;
        $this->urlAnnuaire = $urlAnnuaire;
    }

    public function findClassroomName($id)                                                        // Give the class' name using the class' id
    {
        $week = file_get_contents(sprintf($this->urlPlanning,$id));                                                               // Open the ics in 'week'
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

    /**
     * Find in INSA annuaire the teacher with his name
     * @param String $name Teacher name
     * @return Teacher
     * @throws \Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException
     */
    public function findTeacher($name)
    {
        $teacher = new Teacher();

        // Data post to send
        $data = array('texteNom' => urlencode($name));

        // Get page detail for a teacher
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlAnnuaire);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow 302 redirection return by the first page
        $output = curl_exec($ch);
        curl_close($ch);

        // Parse Teacher info with DOMCrawler
        try{
            $crawler = new Crawler($output);
            $detail = $crawler->filter("#content > dl.detail")->children();
            $teacher->setSurname($detail->eq(1)->html());
            $teacher->setName($detail->eq(3)->html());
            $teacher->setEmail($detail->eq(5)->children()->html());
        }
        catch(\InvalidArgumentException $e){
            throw new ResourceNotFoundException("Teacher \"".$name."\" not found");
        }

        return $teacher;
    }
}