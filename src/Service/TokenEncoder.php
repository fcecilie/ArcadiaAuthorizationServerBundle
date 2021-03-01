<?php

namespace Arcadia\Bundle\AuthorizationBundle\Service;

use Arcadia\Bundle\AuthorizationBundle\Exception\BadPayloadFormatException;
use Arcadia\Bundle\AuthorizationBundle\Exception\DecodingException;
use Arcadia\Bundle\AuthorizationBundle\Exception\TokenExpiredException;

class TokenEncoder
{
    private DataEncoder $dataEncoder;
    private KeysProvider $keysProvider;
    private array $servers;

    public function __construct(DataEncoder $dataEncoder, KeysProvider $keysProvider, array $arcadiaAuthorizationServers)
    {
        $this->dataEncoder = $dataEncoder;
        $this->keysProvider = $keysProvider;
        $this->servers = $arcadiaAuthorizationServers;
    }

    public function encode(string $username): string
    {
        if (!isset($this->servers[$username])) {
            throw new \LogicException("No server $username registered in arcadia_authorization.yaml config file.");
        }

        $key = $this->keysProvider->getKey($username);
        $server = $this->servers[$username];

        $expirationDate = (new \DateTime($server['ttl']))->format('c');

        $encodedToken = $this->dataEncoder->encode("$expirationDate.{$server['password']}", $key);

        $encodedPayload = base64_encode(serialize([
            'username' => $server['username'],
            'encodedToken' => $encodedToken,
        ]));

        return $encodedPayload;
    }

    public function decode(string $encodedPayload): ?array
    {
        if (($encodedPayload = base64_decode($encodedPayload)) === false) {
            throw new DecodingException(DecodingException::PAYLOAD_BASE64_DECODING_FAILURE);
        }

        if (($payload = unserialize($encodedPayload)) === false) {
            throw new DecodingException(DecodingException::PAYLOAD_UNSERIALIZING_FAILURE);
        }

        if (!isset($payload['username'])) {
            throw new BadPayloadFormatException(BadPayloadFormatException::NO_USERNAME);
        }

        if (!isset($payload['encodedToken'])) {
            throw new BadPayloadFormatException(BadPayloadFormatException::NO_ENCODED_TOKEN);
        }

        $key = $this->keysProvider->getKey($payload['username']);
        try {
            $token = $this->dataEncoder->decode($payload['encodedToken'], $key);
        } catch (\Throwable $throwable) {
            throw new DecodingException(DecodingException::TOKEN_DECODING_FAILURE, $throwable);
        }

        if (count($exploded = explode('.', $token, 2)) !== 2) {
            throw new BadPayloadFormatException(BadPayloadFormatException::BAD_TOKEN_FORMAT);
        }

        [$expirationDateRaw, $password] = $exploded;
        $currentDatetime = new \DateTime('now');

        try {
            $expirationDate = new \DateTime($expirationDateRaw);
        } catch (\Throwable $throwable) {
            throw new BadPayloadFormatException(BadPayloadFormatException::BAD_EXPIRATION_DATE_RAW_FORMAT, $throwable);
        }

        if ($currentDatetime > $expirationDate) {
            throw new TokenExpiredException();
        }

        return [
            'username' => $payload['username'],
            'password' => $password,
        ];
    }
}