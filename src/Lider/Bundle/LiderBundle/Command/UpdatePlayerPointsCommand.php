<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePlayerPointsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lider:player:update')
            ->setDescription('Actualiza el duelo y la pregunta del jugador en los puntos')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $repoQuestionMongo = $dm->getRepository("LiderBundle:QuestionHistory");
        $playerPointRepo = $em->getRepository("LiderBundle:PlayerPoint");
        $list = $repoQuestionMongo->findBy(array("find" => true, "duel" => true));
        $dateMongo = new \MongoDate(strtotime("2014-10-19"));
        $datePostgre = new \DateTime("2014-10-19");
        foreach($list as $question)
        {
            // if($question->getEntryDate() < $datePostgre)
            // {
                $pp = $playerPointRepo->findPlayerPoint($question->getPlayer()->getPlayerId(), $question->getPoints(), $question->getEntryDate());
                $duel = $em->getRepository("LiderBundle:Duel")->find($question->getDuelId());
                $question = $em->getRepository("LiderBundle:Question")->find($question->getQuestion()->getQuestionId());
                foreach($pp as $p)
                {
                    $p->setDuel($duel);
                }
            // }
        }
        $em->flush();
        foreach($list as $question)
        {
            $pp = $playerPointRepo->findOneBy(array("player" => $question->getPlayer()->getPlayerId(), "duel" => $question->getDuelId(), "points" => $question->getPoints(), "question" => null));
            // foreach($pp as $p)
            // {
            if($pp)
            {
                $q = $em->getRepository("LiderBundle:Question")->find($question->getQuestion()->getQuestionId());
                $pp->setQuestion($q);
            }
            else{
                echo "no encontro puntos para el jugador ".$question->getPlayer()->getPlayerId()." del duelo ".$question->getDuelId()."\n";
            }
                
            // }
            $em->flush();
        }
        $em->flush();
    }
}