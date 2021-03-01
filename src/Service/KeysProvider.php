<?php

namespace Arcadia\Bundle\AuthorizationBundle\Service;

class KeysProvider
{
    private string $keysPath;

    public function __construct(string $arcadiaAuthorizationKeysPath)
    {
        $this->keysPath = $arcadiaAuthorizationKeysPath;
    }

    public function getKey(string $tokenUserUsername): string
    {
        $keyFilename = "$this->keysPath/$tokenUserUsername-key.pem";
        if (!file_exists($keyFilename)) {
            throw new \LogicException("File $keyFilename does not exist for $tokenUserUsername.");
        }

        $keyContent = file_get_contents($keyFilename);
        if ($keyContent === false) {
            throw new \RuntimeException("Function file_get_contents() failed on file $keyFilename.");
        }

        return $keyContent;
    }
}