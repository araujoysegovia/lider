<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePlayerWinInDuelCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lider:update:duel')
            ->setDescription('Actualiza el jugador ganador de un duelo segun los puntos obtenidos en ese duelo ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $duels = $em->getRepository("LiderBundle:Duel")->findBy(array("finished" => true));
        foreach($duels as $duel)
        {
            $point1 = $duel->getPointOne();
            $point2 = $duel->getPointTwo();
            if($point1 < $point2)
            {
                $duel->setPlayerWin($duel->getPlayerTwo());
            }
            elseif($point1 > $point2)
            {
                $duel->setPlayerWin($duel->getPlayerOne());
            }
            else{
                $duel->setPlayerWin(null);
            }
            $em->flush();
        }
    }
}