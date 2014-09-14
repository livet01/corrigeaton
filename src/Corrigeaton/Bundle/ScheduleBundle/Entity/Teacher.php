<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teacher
 *
 * @ORM\Table(name="schedule_teacher")
 * @ORM\Entity
 */
class Teacher
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
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="unregisterToken", type="string", length=255)
     */
    private $unregisterToken;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isUnregistered", type="boolean")
     */
    private $isUnregistered;

    /**
     * @ORM\OneToMany(targetEntity="Test", mappedBy="teacher")
     */
    protected $tests;
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
     * @return Teacher
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
     * Set surname
     *
     * @param string $surname
     * @return Teacher
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Teacher
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set unregisterToken
     *
     * @param string $unregisterToken
     * @return Teacher
     */
    public function setUnregisterToken($unregisterToken)
    {
        $this->unregisterToken = $unregisterToken;

        return $this;
    }

    /**
     * Get unregisterToken
     *
     * @return string 
     */
    public function getUnregisterToken()
    {
        return $this->unregisterToken;
    }

    /**
     * Set isUnregistered
     *
     * @param boolean $isUnregistered
     * @return Teacher
     */
    public function setIsUnregistered($isUnregistered)
    {
        $this->isUnregistered = $isUnregistered;

        return $this;
    }

    /**
     * Get isUnregistered
     *
     * @return boolean 
     */
    public function getIsUnregistered()
    {
        return $this->isUnregistered;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tests = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tests
     *
     * @param \Corrigeaton\Bundle\ScheduleBundle\Entity\Test $tests
     * @return Teacher
     */
    public function addTest(\Corrigeaton\Bundle\ScheduleBundle\Entity\Test $tests)
    {
        $this->tests[] = $tests;

        return $this;
    }

    /**
     * Remove tests
     *
     * @param \Corrigeaton\Bundle\ScheduleBundle\Entity\Test $tests
     */
    public function removeTest(\Corrigeaton\Bundle\ScheduleBundle\Entity\Test $tests)
    {
        $this->tests->removeElement($tests);
    }

    /**
     * Get tests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTests()
    {
        return $this->tests;
    }
}
