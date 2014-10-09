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
    public function findTests($id, \DateTime $beginAt, \DateTime $endAt){

        // Find event in id classes at today
        $url = sprintf($this->urlADE,$id,$beginAt->format("Y-m-d"),$endAt->format("Y-m-d"));

        $ical = new \SG_iCalReader($url);

        $evts = $ical->getEvents();
        $tests = array();

        if(count($evts) > 0){
            foreach($evts as $event){

                // Begin by EX -> is an exam
                if(strpos($event->getSummary(),"CM") === 0){
                    $tests[] = $event;
                }
            }
        }
        return $tests;
    }

    public function parseEvent(\SG_iCal_VEvent $event)
    {


        $test = new Test();
        $test->setName($event->getSummary());
        $date = new \DateTime();
        $date->setTimestamp($event->getStart());
        $test->setDate($date);
        $test->setNumReminder(0);
        $test->setId($event->getUID());
        $test->setFinishToken((string)rand());
        // Check validity of event
        $description = $event->getDescription();

        $res = explode ( "\n" , $description );

        $teachNameAndInitial = $this->clearParticule($res[count($res)-2]);

        if(!$this->isTeacher($teachNameAndInitial)){
            throw new BadEventException($test, "Bad event : ".$event->getSummary());
        }
        $test->setTeacher($this->findTeacher($this->getSurname($teachNameAndInitial),$this->getInital($teachNameAndInitial)));

        return $test;
    }

    private function isTeacher($fullName){
        $res = explode(" ",$fullName);
        return count($res) >= 2 && strlen($res[0]) >= 1 && strlen($res[1]) >=1 ;
    }

    private function getInital($fullName){
        $res = explode(" ",$fullName);
        return trim($res[1][0]);
    }

    private function getSurname($fullName){
        $res = explode(" ",$fullName);
        return trim($res[0]);
    }
    private function clearParticule($fullName){
        return trim(preg_replace("/([dleauDLEAU]{2}[ ]{1})*/","",$fullName));
    }

    /**
     * Find in BD or in INSA annuaire the teacher with his name
     * @param String $nameAndInitial Teacher name
     * @return Teacher
     * @throws \Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException
     */
    public function findTeacher($surname, $initial)
    {
        $teacher = $this->em->getRepository('CorrigeatonScheduleBundle:Teacher')->findOneBy(array('ADEname'=>$surname));

        if(!$teacher){
            $teacher =  $this->findTeacherAnnuaire($surname,$initial);
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
    private function findTeacherAnnuaire($surname, $initial){

        $teacher = new Teacher();
        $teacher->setADEname($surname);

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
                        $teacher->setADEname($surname);
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
                    throw new ResourceNotFoundException($teacher, "Teacher \"".$surname.' '.$initial."\" not found");
                }
            }
            else{ // Unknown
                throw new ResourceNotFoundException($teacher, "Teacher \"".$surname.' '.$initial."\" not found");
            }
        }

        $this->em->persist($teacher);
        $this->em->flush();
        return $teacher;
    }
}