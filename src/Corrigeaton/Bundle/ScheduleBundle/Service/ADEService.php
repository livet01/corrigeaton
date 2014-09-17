<?php
namespace Corrigeaton\Bundle\ScheduleBundle\Service;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Corrigeaton\Bundle\ScheduleBundle\Exception\BadEventException;
use Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class ADEService manage connexion with planning, ade and INSA annuaire
 * @package Corrigeaton\Bundle\ScheduleBundle\Service
 */
class ADEService
{


    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     * See config
     */
    private $urlPlanning;

    /**
     * @var string
     * See config
     */
    private $urlAnnuaire;

    /**
     * @var string
     * See config
     */
    private $urlADE;


    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param string $urlPlanning
     * @param string $urlAnnuaire
     * @param string $urlADE
     */
    public function __construct(EntityManager $em,$urlPlanning, $urlAnnuaire, $urlADE)
    {
        $this->em = $em;
        $this->urlPlanning = $urlPlanning;
        $this->urlAnnuaire = $urlAnnuaire;
        $this->urlADE = $urlADE;
    }

    /**
     * Find name of classroom
     * @param int $id id of classroom
     * @return string
     */
    public function findClassroomName($id)
    {
        $week = file_get_contents(sprintf($this->urlPlanning,$id));
        $res = array();
        preg_match("/CALNAME:([^ ]*)/", $week, $res);
        return $res[1];
    }

    /**
     * Find and add new test in BD
     * @param int $id id of classroom
     * @param \DateTime $beginAt
     * @param \DateTime $endAt
     */
    public function findAndAddTests($id, \DateTime $beginAt, \DateTime $endAt){

        $totem = false;
        // Find event in id classes at today
        $url = sprintf($this->urlADE,$id,$beginAt->format("Y-m-d"),$endAt->format("Y-m-d"));

        $ical = new \SG_iCalReader($url);

        $evts = $ical->getEvents();
        $tests = array();

        if(count($evts) > 0){
            foreach($evts as $event){

                // Begin by EX -> is an exam
                if(strpos($event->getSummary(),"CM") === 0){
                    $uid = $event->getUID();
                    $evBD = $this->em->getRepository('CorrigeatonScheduleBundle:Test')->findOneBy(array("uid" => $uid));
                    if(!$evBD)
                    {
                        try{
                            $this->em->persist($this->parseEvent($event));
                        }
                        catch(BadEventException $e){var_dump('Bad');}
                        catch(ResourceNotFoundException $e){var_dump('Not found : '.$e->getMessage());}
                    }
                }
            }
        }
        $this->em->flush();
    }

    private function parseEvent(\SG_iCal_VEvent $event)
    {
        // Check validity of event
        $description = $event->getDescription();
        var_dump($description);

        $res = explode ( "\n" , $description );

        $teachNameAndInitial = $res[count($res)-2];

        if(!$this->isTeacher($teachNameAndInitial)){
            throw new BadEventException();
        }

        $test = new Test();
        $test->setName($event->getSummary());
        $date = new \DateTime();
        $date->setTimestamp($event->getStart());
        $test->setDate($date);
        $test->setNumReminder(0);
        $test->setUid($event->getUID());
        $token = (string)rand();
        $test->setFinishToken($event->getUID().$token);
        $test->setStatus(Test::STATUS_FUTURE);
        $test->setTeacher($this->findTeacher($this->getSurname($teachNameAndInitial),$this->getInital($teachNameAndInitial)));
        return $test;
    }

    private function isTeacher($fullName){
        return count(explode(" ",$fullName)) >= 2;
    }

    private function getInital($fullName){
        $res = explode(" ",$fullName);
        return trim($res[1][0]);
    }

    private function getSurname($fullName){
        $res = explode(" ",$fullName);
        return trim($res[0]);
    }

    public function findTestsTeacher(Test $test)                                                        // Give the teacher's name for a given test(=param)
    {
        $uid = $test->getUID();                                                                          // The token is the uid of his Test
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
     * Find in BD or in INSA annuaire the teacher with his name
     * @param String $nameAndInitial Teacher name
     * @return Teacher
     * @throws \Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException
     */
    public function findTeacher($surname, $initial)
    {
        $teacher = $this->em->getRepository('CorrigeatonScheduleBundle:Teacher')->findOneBySurname($surname);

        if(!$teacher){
            return $this->findTeacherAnnuaire($surname,$initial);
        }

        return $teacher;
    }

    private function clearEmail($email){
        return str_replace('<span style="display: none;"> </span>',"",$email);
    }

    /**
     * Find in INSA annuaire the teacher
     * @param $nameAndInitial
     * @return Teacher
     * @throws \Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException
     */
    public function findTeacherAnnuaire($surname, $initial){

        $teacher = new Teacher();

        // Data post to send
        $data = array('texteNom' => $surname);

        // Get page detail for a teacher
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlAnnuaire);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow 302 redirection return by the first page
        $output = curl_exec($ch);
        curl_close($ch);

        $crawler = new Crawler($output);

        $detail = $crawler->filter("#content > dl.detail");
        if($detail->count() === 1){ // Have 1 result
            $detail = $detail->children();
            $teacher->setSurname($detail->eq(1)->html());
            $teacher->setName($detail->eq(3)->html());
            $teacher->setEmail($this->clearEmail($detail->eq(5)->children()->html()));
        }
        else { // Have more or 0 result
            $results = $crawler->filter("#content div.results");
            $teachers = array();

            if($results->count() === 1){ // Have more results
                $results->filter("tbody > tr")->each(function ($node, $i) use ($surname, $initial, &$teachers){

                    $node = $node->children();

                    if(strcasecmp($node->eq(0)->html(),$surname) === 0 && stripos($node->eq(1)->html(),$initial) === 0){
                        $teacher = new Teacher();
                        $teacher->setSurname($node->eq(0)->html());
                        $teacher->setName($node->eq(1)->html());
                        $teacher->setEmail($this->clearEmail($node->eq(2)->children()->html()));
                        $teachers[] = $teacher;
                    }
                });

                if(count($teachers) == 1){
                    $teacher = $teachers[0];
                }
                else {
                    throw new ResourceNotFoundException("Teacher \"".$surname.' '.$initial."\" not found");
                }
            }
            else{ // Unknown
                throw new ResourceNotFoundException("Teacher \"".$surname.' '.$initial."\" not found");
            }
        }

        $this->em->persist($teacher);
        return $teacher;
    }
}