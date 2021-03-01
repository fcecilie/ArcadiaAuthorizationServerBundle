<?php

namespace Arcadia\Bundle\AuthorizationBundle\Command;

use Arcadia\Bundle\AuthorizationBundle\Entity\TokenUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TokenUserListCommand extends Command
{
    protected static $defaultName = 'arcadia:token-user:list';

    private EntityManagerInterface $em;

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('List the TokenUsers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tokenUsers = $this->em->getRepository(TokenUser::class)->findAll();

        $table = new Table($output);
        $table->setHeaders(['username', 'password', 'roles']);

        /** @var TokenUser $tokenUser */
        foreach ($tokenUsers as $tokenUser) {
            $table->addRow([$tokenUser->getUsername(), $tokenUser->getPassword(), implode(', ', $tokenUser->getRoles())]);
        }

        $table->render();
        return Command::SUCCESS;
    }
}