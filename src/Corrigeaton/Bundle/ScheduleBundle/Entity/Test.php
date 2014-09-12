<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Test
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Test
{
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
     * @var boolean
     *
     * @ORM\Column(name="isFinished", type="boolean")
     */
    private $isFinished;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="Classroom", inversedBy="tests")
     */
    protected $classrooms;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher", inversedBy="tests")
     */
    protected $teacher;

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
     * Set isFinished
     *
     * @param boolean $isFinished
     * @return Test
     */
    public function setIsFinished($isFinished)
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    /**
     * Get isFinished
     *
     * @return boolean 
     */
    public function getIsFinished()
    {
        return $this->isFinished;
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
     * Constructor
     */
    public function __construct()
    {
        $this->classrooms = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add classrooms
     *
     * @param \Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom $classrooms
     * @return Test
     */
    public function addClassroom(\Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom $classrooms)
    {
        $this->classrooms[] = $classrooms;

        return $this;
    }

    /**
     * Remove classrooms
     *
     * @param \Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom $classrooms
     */
    public function removeClassroom(\Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom $classrooms)
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
    public function setTeacher(\Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher $teacher = null)
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
