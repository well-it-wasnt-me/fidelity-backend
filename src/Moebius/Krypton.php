<?php


namespace App\Moebius;

class Krypton
{
    protected $method = 'AES-128-CTR'; // default cipher method if none supplied
    private $key;

    protected function iv_bytes()
    {
        return openssl_cipher_iv_length($this->method);
    }

    /**
     * Krypton constructor.
     *
     * @param bool $key Encryption Key
     * @param bool $method Encryption Method
     */
    public function __construct($key = false, $method = false)
    {
        if (!$key) {
            $key = getenv('KRYPTON_KEY'); // default encryption key if none supplied
        }

        if (ctype_print($key)) {
            // convert ASCII keys to binary format
            $this->key = openssl_digest($key, 'SHA256', true);
        } else {
            $this->key = $key;
        }

        if ($method) {
            if (in_array($method, openssl_get_cipher_methods())) {
                $this->method = $method;
            } else {
                die(__METHOD__ . ": unrecognised cipher method: {$method}");
            }
        }
    }

    /**
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $iv = openssl_random_pseudo_bytes($this->iv_bytes());
        return bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
    }

    /**
     * @param string $data
     * @return bool|false|string
     */
    public function decrypt(string $data)
    {
        $iv_strlen = 2  * $this->iv_bytes();
        if (preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
            list(, $iv, $crypted_string) = $regs;
            if (ctype_xdigit($iv) && strlen($iv) % 2 == 0) {
                return openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
            }
        }
        return false; // failed to decrypt
    }
}
