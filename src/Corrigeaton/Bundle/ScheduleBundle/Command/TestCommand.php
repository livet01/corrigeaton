<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Command;


use Corrigeaton\Bundle\ReportBundle\Event\ReportEvent;
use Corrigeaton\Bundle\ScheduleBundle\Exception\BadEventException;
use Corrigeaton\Bundle\ScheduleBundle\Exception\ResourceNotFoundException;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
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
        $em = $this->getContainer()->get("doctrine.orm.entity_manager");
        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $entities = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->findAll();

        $output->writeln("<info>Traitement de ".count($entities)." classes</info>");

        $nbTestAdd = 0;

        foreach($entities as $classroom){
            $tests = $this->getContainer()->get("corrigeaton_schedule.ade_service")->findTests($classroom->getId(), new \DateTime(), new \DateTime("2 week"));

            foreach($tests as $test){
                $evBD = $em->getRepository('CorrigeatonScheduleBundle:Test')->find($test->getUID());
                if(!$evBD)
                {
                    try{
                        $evBD = $this->getContainer()->get("corrigeaton_schedule.ade_service")->parseEvent($test);
                        $em->persist($evBD);
                        $output->writeln("<info>Ajout de ".$evBD->getName().'</info>');
                        $nbTestAdd++;
                    }
                    catch(BadEventException $e){
                        $reportEvent = new ReportEvent($e->getClass(),$e->getMessage());
                        $dispatcher = $this->getContainer()->get('event_dispatcher');
                        $dispatcher->dispatch('corrigeaton_report.events.report', $reportEvent);
                        $output->writeln('<error>'.$e->getMessage()."</error>");
                    }
                    catch(ResourceNotFoundException $e){
                        $reportEvent = new ReportEvent($e->getClass(), $e->getMessage());
                        $output->writeln('<error>'.$e->getMessage()."</error>");}
                }

                if(!$evBD->getClassrooms()->contains($classroom)){
                    $evBD->addClassroom($classroom);
                }
            }

        }
        $output->writeln("<info>".$nbTestAdd." tests ajoutés</info>");
        $em->flush();
    }
} 