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

    public function autoDecrypt($val)
    {
        return $val === null ? null : (is_numeric($val) && isset(app()->request->encrypt) && app()->request->encrypt === 'false' ?
            $val :
            (is_numeric($val) ? $val : $this->decrypt($val)));
    }

    public function autoDecryptAllNested($reqArr)
    {
        if (is_string($reqArr)) $reqArr = json_decode($reqArr, true);

        foreach ($reqArr as $currentCol => $val) {
            if (is_array($val)) {
                // is detail
                $reqArr[$currentCol] = $this->autoDecryptAllNested($val);
            } else if ((Str::endswith($currentCol, '_id') && $val && !is_numeric($val))) {
                $reqArr[$currentCol] = $this->autoDecrypt($val) ?? $val;
            } else if ($currentCol === 'id') {
                $reqArr[$currentCol] = $this->autoDecrypt($val) ?? $val;
            }
        }

        return $reqArr;
    }
}
