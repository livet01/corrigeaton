<?php
namespace Corrigeaton\Bundle\ScheduleBundle\Service;

class ADEService
{

    public function findClassroomName($classNum)
    {
        $url = "https://www.etud.insa-toulouse.fr/planning/index.php?gid=".$classNum."&wid=0&ics=1";
        $week =  file_get_contents($url);
        $res = array();
        preg_match("/CALNAME:([^ ]*)/", $week, $res);
        return $res[1];
    }
    public function findTeachersAdress($name)
    {
        $ch = curl_init();
        $data = array('namefield' => '', 'first_namefield' => '', 'telephonefield' => '', 'pageLength' => '',
            'startIndex' => '0', '_query' => 'Annuaire', 'texteNom' => $name, 'textePrenom' =>'', 'texteTelephone' => '',
            'slctAffectation' => '-1', 'slctFonction' => '------+Inconnue+------', 'n-page' => '10' );
        curl_setopt($ch, CURLOPT_URL, 'www.insa-toulouse.fr/fr/annuaire.html');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $res = array();
        preg_match("/N10001=\"([^\"]*)/", $output, $res);
        $email = $res[1]."@";
        preg_match("/N10001 \\+=\"([^&\"]+)/", $output, $res);
        $email = $email.$res[1];
        return $email;
    }
}