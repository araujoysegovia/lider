<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DuelSimulatorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lider:simulator')
            ->setDescription('simulador de duelos.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $listGames = $em->getRepository("LiderBundle:Game")->findBy(array("active" => true));
        $gearman = $this->getContainer()->get('gearman');
        foreach($listGames as $game)
        {
            try{
                $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkersimulator~simulate', json_encode(array(
                            'gameId' => $game->getId()
                          )));
            }catch(\Exception $e){
                return $e;
            }
        }
    }
}