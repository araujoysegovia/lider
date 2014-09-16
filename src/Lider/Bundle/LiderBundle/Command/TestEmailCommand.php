<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;

class TestEmailCommand extends CreateSchemaDoctrineCommand
{
	protected function configure()
    {
        parent::configure();

        $this
            ->setName('sifinca:schema:create')
            ->setDescription('Executes (or dumps) the SQL needed to generate the database schema')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));
        parent::execute($input, $output);
        $conn = $this->getApplication()->getKernel()->getContainer()->get("database_connection");
        $sequence = "CREATE SEQUENCE sec_crm_oportunity_oportunityNumber_seq
					  INCREMENT 1
					  MINVALUE 1
					  MAXVALUE 9223372036854775807
					  START 1
					  CACHE 1;";
		try{
			$res = $conn->executeQuery($sequence);
		}catch(\Exception $ex){}
		
    }
}