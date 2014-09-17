<?php
namespace Corrigeaton\Bundle\ScheduleBundle\Service;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
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
                        $this->em->persist($this->parseEvent($event));
                    }
                }
            }
        }
        $this->em->flush();
    }

    private function parseEvent(\SG_iCal_VEvent $event)
    {
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
        $description = $event->getDescription();
        $res = explode ( "\n" , $description );
        $teachNameAndInitial = $res[count($res)-2];
        $test->setTeacher($this->findTeacher($teachNameAndInitial));
        return $test;
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
     * @param String $name Teacher name
     * @return Teacher
     * @throws \Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException
     */
    public function findTeacher($nameAndInitial)
    {
        $temp = explode(" ", $nameAndInitial);
        $name=$temp[0];
        $teacher = $this->em->getRepository('CorrigeatonScheduleBundle:Teacher')->findByName($name);

        if(!$teacher){
            return $this->findTeacherAnnuaire($nameAndInitial);
        }

        return $teacher;
    }

    /**
     * Find in INSA annuaire the teacher
     * @param $name
     * @return Teacher
     * @throws \Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException
     */
    private function findTeacherAnnuaire($nameAndInitial){

        $teacher = new Teacher();
        // Data post to send
        $temp = explode(" ", $nameAndInitial)[0];
        $name = $temp[0];
        $initial = explode(".", $temp[1])[0];
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
            $moreThanOne = $crawler->filter(".result-report > strong")->children();
            $results = $crawler->filter(".results tbody ")->children();
            $data = array();
            foreach ($results as $result => $node) {
                if(strcasecmp($node->eq(1)->html(), $name)  == 0)
                {
                    $teacher->setName($node->eq(1)->html());
                    $teacher->setSurname($node->eq(2)->html());
                    $teacher->setEmail($node->eq(3)->children()->html());
                }
            }
        }
        catch(\InvalidArgumentException $e){
            try{
                $detail = $crawler->filter("#content > dl.detail")->children();
                $teacher->setSurname($detail->eq(1)->html());
                $teacher->setName($detail->eq(3)->html());
                $teacher->setEmail($detail->eq(5)->children()->html());
            }
            catch(\InvalidArgumentException $e){
                throw new ResourceNotFoundException("Teacher \"".$name."\" not found");
            }

        }
        $this->em->persist($teacher);
        $this->em->flush();
        return $teacher;
    }
}