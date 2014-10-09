<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 18/09/14
 * Time: 14:17
 */

namespace Corrigeaton\Bundle\ReportBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class ReportEvent extends Event{
    protected $class;
    protected $log;

    public function __construct($class, $log)
    {
        $this->setClass($class);
        $this->setLog($log);
    }

    /**
     * @param mixed $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }




} 