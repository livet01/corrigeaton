<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Test
 *
 * @ORM\Table(name="schedule_test")
 * @ORM\Entity(repositoryClass="Corrigeaton\Bundle\ScheduleBundle\Entity\Repository\TestRepository")
 */
class Test
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_corrected", type="datetime", nullable=true)
     */
    private $dateCorrected;

    /**
     * @var integer
     *
     * @ORM\Column(name="numReminder", type="integer")
     */
    private $numReminder = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="finishToken", type="string", length=255)
     */
    private $finishToken;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Classroom")
     * @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     */
    protected $classrooms;

    /**
     * @var Teacher
     *
     * @ORM\ManyToOne(targetEntity="Teacher", inversedBy="tests")
     * @ORM\JoinColumn(name="teacher_id", referencedColumnName="id")
     */
    protected $teacher;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->classrooms = new \Doctrine\Common\Collections\ArrayCollection();
        $this->finishToken = uniqid(time(),true);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Test
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Test
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Test
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set numReminder
     *
     * @param integer $numReminder
     * @return Test
     */
    public function setNumReminder($numReminder)
    {
        $this->numReminder = $numReminder;

        return $this;
    }

    /**
     * Get numReminder
     *
     * @return integer 
     */
    public function getNumReminder()
    {
        return $this->numReminder;
    }

    /**
     * Set finishToken
     *
     * @param string $finishToken
     * @return Test
     */
    public function setFinishToken($finishToken)
    {
        $this->finishToken = $finishToken;

        return $this;
    }

    /**
     * Get finishToken
     *
     * @return string 
     */
    public function getFinishToken()
    {
        return $this->finishToken;
    }

    /**
     * Add classrooms
     *
     * @param \Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom $classrooms
     * @return Test
     */
    public function addClassroom(Classroom $classrooms)
    {
        $this->classrooms[] = $classrooms;

        return $this;
    }

    /**
     * Remove classrooms
     *
     * @param \Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom $classrooms
     */
    public function removeClassroom(Classroom $classrooms)
    {
        $this->classrooms->removeElement($classrooms);
    }

    /**
     * Get classrooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassrooms()
    {
        return $this->classrooms;
    }

    /**
     * Set teacher
     *
     * @param \Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher $teacher
     * @return Test
     */
    public function setTeacher(Teacher $teacher = null)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return \Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher 
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Return :
     *          -1 if nothing to send
     *          0 if mail '0'
     *          1 if mail '1' ....
     * @return int
     */
    public function doISend()
    {
        $intTemps = array(0,3,4.5,5.25,6,6.75,7.5,8.25,9);
        $ret = -1;
        $dateExam = $this->date;
        $dateToday = new \DateTime();
        $diffDate = $dateToday->diff($dateExam,true);
        $dayDiff = intval($diffDate->format('d'));
        $monthDiff = intval($diffDate->format('m'));
        $yearDiff = intval($diffDate->format('Y'));
        $totDay = $dayDiff + $monthDiff*30 + $yearDiff*365;
        $numReminder = $this->numReminder;
        for ($i=0; $i <10; $i++ )
        {
            if ($totDay == intval($intTemps[$i]*7))
            {
                if ($numReminder == $i)
                {
                    $ret = $i;
                }
            }

        }
        return $ret;
    }

    public function isCorrected(){
        return ! is_null($this->dateCorrected);
    }

    /**
     * Set dateCorrected
     *
     * @param \DateTime $dateCorrected
     * @return Test
     */
    public function setDateCorrected($dateCorrected)
    {
        $this->dateCorrected = $dateCorrected;

        return $this;
    }

    /**
     * Get dateCorrected
     *
     * @return \DateTime 
     */
    public function getDateCorrected()
    {
        return $this->dateCorrected;
    }
}
