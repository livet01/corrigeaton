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
    const STATUS_FUTURE = "future";
    const STATUS_NOTCORRECTED = "notCorrected";
    const STATUS_CORRECTED = "corrected";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @var integer
     *
     * @ORM\Column(name="numReminder", type="integer")
     */
    private $numReminder;

    /**
     * @var string
     *
     * @ORM\Column(name="finishToken", type="string", length=255)
     */
    private $finishToken;

    /**
     * @var uid
     * @ORM\Column(name="uid", type="string", length=255)
     */
    private $uid;


    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

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
     * @param int id
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
     * Set uid
     *
     * @param string
     * @return Test
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
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
     * Set status
     *
     * @param string $status
     * @return Test
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
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
}
