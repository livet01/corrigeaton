<?php

namespace Corrigeaton\Bundle\MailerBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class ReminderEvent extends Event {
    private $test;

    function __construct($test)
    {
        $this->test = $test;
    }

    /**
     * @return \Corrigeaton\Bundle\ScheduleBundle\Entity\Test
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param mixed $test
     */
    public function setTest($test)
    {
        $this->test = $test;
    }


} 