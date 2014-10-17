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
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $repoQuestionMongo = $dm->getRepository("LiderBundle:QuestionHistory");
        $listMongo = $repoQuestionMongo->getQuestionReport();
        $repoQuestionPostgres = $em->getRepository("LiderBundle:Question");
        $listPostgres = $repoQuestionPostgres->findAll();
        foreach($listMongo as $questionMongo)
        {
            foreach($listPostgres as $questionPostgre)
            {
                if($questionMongo['question.questionId'] == $questionPostgre->getId())
                {
                    $percent = $questionMongo['win'] * 100 / $questionMongo['total'];
                    $value = $percent / 10;
                    $level = intval($value);
                    $questionPostgre->setLevel(10-$level);
                    break;
                }
                

            }
        }
        $em->flush();
    }
}