<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Command;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStatusCommand extends ContainerAwareCommand
{

	protected function configure()
	{
		$this->setName('corrigeaton:test:update_status');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		$exams = $em->getRepository('CorrigeatonScheduleBundle:Test')
					->findByStatus(Test::STATUS_FUTURE);

		$output->writeln("<info>Traitement de ".count($exams)." examens</info>");

		$modif = 0;
		$now = time();
		foreach($exams as $exam)
		{
			if($now - $exam->getDate()->getTimestamp() > 0)
			{
				$exam->setStatus(Test::STATUS_NOTCORRECTED);
				$em->persist($exam);
				$output->writeln("<info>Changement de status de : ".$exam->getName()."</info>");
				$modif++;
			}
		}

		$em->flush();
		$output->writeln("<info>".$modif." test updated</info>");

	}

}
