<?php
namespace Corrigeaton\Bundle\ScheduleBundle\Service;

class ADEService
{

    public function findClassroomName($classNum)
    {
        $url = "https://www.etud.insa-toulouse.fr/planning/index.php?gid=".$classNum."&wid=0&ics=1";
        $week =  file_get_contents($url);
        $res = array();
        preg_match("CALNAME:([^ ]*)", $week, $res);
        return $res[1];
    }
}