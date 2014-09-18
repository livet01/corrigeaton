<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Command;


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
        $entities = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->findAll();

        $output->writeln("<info>Traitement de ".count($entities)." classes</info>");

        $nbTestAdd = 0;

        foreach($entities as $classroom){
            $tests = $this->getContainer()->get("corrigeaton_schedule.ade_service")->findTests($classroom->getId(), new \DateTime(), new \DateTime("2 week"));

            foreach($tests as $test){
                $evBD = $em->getRepository('CorrigeatonScheduleBundle:Test')->findOneByUid(array("uid" => $test->getUID()));
                if(!$evBD)
                {
                    try{
                        $newTest = $this->getContainer()->get("corrigeaton_schedule.ade_service")->parseEvent($test);
                        $em->persist($newTest);
                        $output->writeln("<info>Ajout de ".$newTest->getName().'</info>');
                        $nbTestAdd++;
                    }
                    catch(BadEventException $e){$output->writeln('<error>'.$e->getMessage()."</error>");}
                    catch(ResourceNotFoundException $e){$output->writeln('<error>'.$e->getMessage()."</error>");}
                }
            }

        }
        $output->writeln("<info>".$nbTestAdd." tests ajoutés</info>");
        $em->flush();
    }
} 