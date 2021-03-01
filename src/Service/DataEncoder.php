<?php

namespace Arcadia\Bundle\AuthorizationBundle\Service;

class DataEncoder
{
    private const CIPHER = 'aes-256-gcm';

    public function encode(string $data, string $key): string
    {
        $cipher = self::CIPHER;
        if (!in_array($cipher, openssl_get_cipher_methods())) {
            throw new \Exception("Algorithm $cipher is not available on this platform.");
        }

        $ivLength = openssl_cipher_iv_length($cipher);
        $tagLength = 16;

        $iv = openssl_random_pseudo_bytes($ivLength);

        $encryptedData = openssl_encrypt($data, $cipher, $key,  OPENSSL_RAW_DATA, $iv, $tag, "", $tagLength);
        if ($encryptedData === false) {
            throw new \RuntimeException('Function openssl_encrypt() failed.');
        }

        return base64_encode($iv.$encryptedData.$tag);
    }

    public function decode(string $data, string $key): ?string
    {
        $cipher = self::CIPHER;
        if (!in_array($cipher, openssl_get_cipher_methods())) {
            throw new \Exception("Algorithm $cipher is not available on this platform.");
        }

        $data = base64_decode($data);

        $dataLength = strlen($data);
        $ivLength = openssl_cipher_iv_length($cipher);
        $tagLength = 16;

        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $dataLength - $tagLength, $tagLength);
        $data = substr($data, $ivLength, $dataLength - $ivLength - $tagLength);

        $data = openssl_decrypt($data, $cipher, $key,  OPENSSL_RAW_DATA, $iv, $tag);
        if ($data === false) {
            $data = null;
        }

        return $data;
    }
}