<?php

namespace Arcadia\Bundle\AuthorizationBundle\Command;

use Arcadia\Bundle\AuthorizationBundle\Entity\TokenUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TokenUserRemoveCommand extends Command
{
    protected static $defaultName = 'arcadia:token-user:remove';

    private EntityManagerInterface $em;
    private string $keysPath;

    public function __construct(string $name = null, EntityManagerInterface $em, string $arcadiaAuthorizationKeysPath)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->keysPath = $arcadiaAuthorizationKeysPath;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Remove a TokenUser.')
            ->addArgument('tokenUserUsername', InputArgument::REQUIRED, 'The username of the TokenUser to remove.');
        ;
    }

    private function removePrivateKey(string $tokenUserUsername): void
    {
        $keyFilename = "$this->keysPath/$tokenUserUsername-key.pem";

        if (file_exists($keyFilename)) {
            if (unlink($keyFilename) === false) {
                throw new \RuntimeException("Function unlink() failed to remove $keyFilename.");
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tokenUserUsername = $input->getArgument('tokenUserUsername');
        $tokenUser = $this->em->getRepository(TokenUser::class)->findOneBy(['username' => $tokenUserUsername]);

        if (!$tokenUser instanceof TokenUser) {
            $io->error("Unable to find TokenUser with username $tokenUserUsername.");
            return Command::FAILURE;
        }

        $this->removePrivateKey($tokenUserUsername);

        $this->em->remove($tokenUser);
        $this->em->flush();

        $io->success("TokenUser $tokenUserUsername has been removed.");

        return Command::SUCCESS;
    }
}