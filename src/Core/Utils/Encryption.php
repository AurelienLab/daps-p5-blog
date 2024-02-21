<?php

namespace App\Core\Utils;

/**
 * Shorthands to encrypt / decrypt data to / from token
 */
class Encryption
{

    /**
     * @param mixed $data
     *
     * @return string
     * @throws \Exception
     */
    public static function encrypt(mixed $data): string
    {
        $key = config('app.key');
        $method = 'AES-256-CBC';
        $iv = Str::rand(16);
        $token = openssl_encrypt(serialize($data), $method, $key, 0, $iv);
        $token = base64_encode($iv.'//'.$token);

        return $token;
    }

    /**
     * @param string $token
     *
     * @return mixed
     * @throws \Exception
     */
    public static function decrypt(string $token): mixed
    {
        $data = explode('//', base64_decode($token));
        $iv = $data[0];
        $token = $data[1];

        $serializedData = openssl_decrypt($token, 'AES-256-CBC', config('app.key'), 0, $iv);

        $data = unserialize($serializedData);

        return $data;
    }
}
