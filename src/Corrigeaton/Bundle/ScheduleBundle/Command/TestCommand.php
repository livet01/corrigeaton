<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Command;


use Proxies\__CG__\Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('corrigeaton:test:find')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine");
        $entities = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->findAll();

        foreach($entities as $classroom){
            $this->getContainer()->get("corrigeaton_schedule.ade_service")->findAndAddTests($classroom->getId(), new \DateTime(), new \DateTime());
        }

    }
} 