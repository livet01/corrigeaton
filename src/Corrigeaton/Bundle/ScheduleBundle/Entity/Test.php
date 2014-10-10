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
     * Add numReminder
     *
     * @return Test
     */
    public function addNumReminder()
    {
        $this->numReminder ++;

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
        $intTemps = array('P59D','P55D','P51D','P47D','P43D','P39D','P35D','P31D','P20D','P0D');
        $dateToday = new \DateTime();
        $numReminder = $this->numReminder;
        for ($i=0; $i < count($intTemps); $i++)
        {
            $date = clone $this->date;
            $date->add(new \DateInterval($intTemps[$i]));

            if ($dateToday >= $date && $numReminder < count($intTemps)-$i)
            {
                $numReminder = count($intTemps)-$i;
                return $numReminder;
            }
        }
        return -1;
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

    public function __toString(){
        return "Test : ".$this->name;
    }
}
