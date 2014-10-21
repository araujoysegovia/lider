<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCategoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lider:update:category')
            ->setDescription('Actualiza la categoria de las preguntas en el question history')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $repoMongo = $dm->getRepository("LiderBundle:QuestionHistory");
        $repoPostgre = $em->getRepository("LiderBundle:Question");
        $list = $repoMongo->getQuestionWithoutCategory();
        foreach($list->toArray() as $question)
        {
            $que = $question->getQuestion();
            $q = $repoPostgre->find($que->getQuestionId());
            $que->getDataFromQuestionEntity($q);
        }
        $dm->flush();
    }
}