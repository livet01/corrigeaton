<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Exception;


class BadEventException extends \Exception {
    /**
     * @var object
     */
    protected $class;

    function __construct($class,$message = "")
    {
        parent::__construct($message);
        $this->class = $class;
    }
    /**
     * @param object $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return object
     */
    public function getClass()
    {
        return $this->class;
    }





} 