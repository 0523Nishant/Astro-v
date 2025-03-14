<?php

namespace App\Http\Services\V1;

class EncryptionService
{
    /**
     * @method doEncrypt()
     *  To get the encrypted string using encryption key and iv key.
     *
     * @param array $data Array used to do encryption
     *      @param string $data.payload Mandatory Encrypted payload to be decrypt
     *      @param string $data.key     Mandatory Portal's encryption key set in Database
     *      @param string $data.iv      Mandatory Portal's Hexa IV key set in Database
     *      @param string $data.salt    Optional Default 256, Encryption type
     *
     * @return String encrypted string else false
     */
    public function doEncrypt(array $data = [])
    {
        $payload    = trim($data['payload']);
        $key        = trim($data['key']);
        $iv         = trim($data['iv']);
        $salt       = (!empty($data['salt']) && $data['salt'] == '128') ? 'aes-128-cbc' : 'aes-256-cbc';
        if (!empty($payload) && !empty($key) && !empty($iv)) {
            return openssl_encrypt($payload, $salt, $key, 0, $iv);
        }
        return false;
    }

    /**
     * @method doDecrypt()
     *  To get the json encdoded non-encrypted string using encryption key and iv key.
     *
     * @param array $data Array used to do decryption
     *      @param string $data.payload Mandatory Encrypted payload to be decrypt
     *      @param string $data.key     Mandatory Portal's encryption key set in Database
     *      @param string $data.iv      Mandatory Portal's Hexa IV key set in Database
     *      @param string $data.salt    Optional Default 256, Encryption type
     *
     * @return String encrypted string else false
     */
    public function doDecrypt(array $data = [])
    {
        $payload    = trim($data['payload']);
        $key        = trim($data['key']);
        $iv         = trim($data['iv']);
        $salt       = (!empty($data['salt']) && $data['salt'] == '128') ? 'aes-128-cbc' : 'aes-256-cbc';
        if (!empty($payload) && !empty($key) && !empty($iv)) {
            return openssl_decrypt($payload, $salt, $key, 0, $iv);
        }
        return false;
    }
}
