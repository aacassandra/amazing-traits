<?php

namespace AmazingTraits\Helpers;
use DateTime;
use Illuminate\Support\Str;

/**
 * Class untuk simple generate decrypt dan encryption
 */
class Cryptor {
    function encrypt_decrypt($action, $string): bool|string
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = env('CRYPTO_KEY');
        $secret_iv = env('CRYPTO_IV');

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            if (str_contains($output, '::')) {
                $split = explode('::', $output);
                $output = $split[0];
            }
        }

        return $output;
    }

    public function encrypt( string|int $id ): bool|string
    {
        return $this->encrypt_decrypt('encrypt', $id);
    }

    public function decrypt( $encrypted ): bool|string
    {
        return $this->encrypt_decrypt('decrypt', $encrypted);
    }
}
