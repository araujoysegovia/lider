<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionLevelDifficultCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lider:question:level:difficult')
            ->setDescription('Obtiene El nivel de dificultad de las preguntas segun un promedio entre cantidad de veces correcta y cantidad de veces que sale ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $gameManager = $this->getContainer()->get('game_manager');
        $gameManager->stopDuels();
        $gameManager->stopGames();
        $gameManager->startGames();
    }
}