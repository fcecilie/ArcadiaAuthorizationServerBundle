<?php

namespace Arcadia\Bundle\AuthorizationBundle\Command;

use Arcadia\Bundle\AuthorizationBundle\Entity\TokenUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TokenUserCreateCommand extends Command
{
    protected static $defaultName = 'arcadia:token-user:create';

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
            ->setDescription('Create a new TokenUser.')
            ->addArgument('tokenUserUsername', InputArgument::REQUIRED, 'The username of the TokenUser to create.');
        ;
    }

    private function createPrivateKey(string $tokenUserUsername): void
    {
        $newKeyPair = openssl_pkey_new([
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($newKeyPair, $privateKeyContent);

        if (!is_dir($this->keysPath)) {
            if (!mkdir($this->keysPath, 0777, true)) {
                throw new \RuntimeException("Function mkdir() failed to create directory $this->keysPath.");
            }
        }

        $keyFilename = "$this->keysPath/$tokenUserUsername-key.pem";

        if (file_put_contents($keyFilename, $privateKeyContent) === false) {
            throw new \RuntimeException("Function file_put_contents() failed with file $keyFilename.");
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tokenUserUsername = $input->getArgument('tokenUserUsername');
        ($tokenUser = new TokenUser())->setUsername($tokenUserUsername);

        $this->createPrivateKey($tokenUserUsername);

        $this->em->persist($tokenUser);
        $this->em->flush();

        $io->success("TokenUser $tokenUserUsername has been created.");

        return Command::SUCCESS;
    }
}