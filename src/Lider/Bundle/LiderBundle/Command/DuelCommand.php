<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DuelCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lider:duel')
            ->setDescription('Inactiva todos los duelos terminados que se haya pasado su fecha final, e inicia los nuevos duelos ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $gameManager = $this->getContainer()->get('game_manager');
        $gameManager->stopDuels();
        $gameManager->stopGames();
        $games = $gameManager->startGames();
    }
}