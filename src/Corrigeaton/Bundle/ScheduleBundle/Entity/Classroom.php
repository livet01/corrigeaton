<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Entity;

use Corrigeaton\Bundle\ReportBundle\Service\ReportEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Corrigeaton\Bundle\ScheduleBundle\Constraint as ScheduleAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Classroom
 *
 * @ORM\Table(name="schedule_classroom")
 * @ORM\Entity
 * @UniqueEntity("id")
 */
class Classroom
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @Assert\NotBlank()
     * @ScheduleAssert\ContainsIDValid()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(
     *     message = "'{{ value }}' n'est pas un email valide.",
     *     checkMX = true
     * )
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * Set email
     *
     * @param string $email
     * @return Classroom
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
     * Set id
     *
     * @param integer $id
     * @return Classroom
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
     * @return Classroom
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

    public function __toString(){
        return str_replace('_'," ",$this->getName());
    }
}
