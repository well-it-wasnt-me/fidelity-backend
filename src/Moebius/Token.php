<?php

namespace App\Moebius;

final class Token {
    /**
     * Create token
     *
     * @param int $length
     * @return string
     */
    public function make_token($length = 16){
        if ($length < 8 || $length > 44) {
            die();
        }
        $length_odd = (($length % 2) != 0);
        $length_has_root = (strpos(sqrt($length), '.') === false);
        $keys = [];
        $offset = $length_odd ? 1 : 0;

        $key_str = $keys[($offset + 0)] = $this->rand_alphanumeric();
        $key_str .= $keys[(intval($length / 4) - 1 + $offset)] = $this->rand_alphanumeric();
        $key_str .= $keys[(intval(($length / 2)) - 1 + $offset)] = $this->rand_alphanumeric();
        $key_str .= $keys[(($length - 2) + $offset)] = $this->rand_alphanumeric();
        // echo "c - $key_str<br>";
        $hashed_keys = $length_has_root ? sha1(md5($key_str)) : sha1(sha1($key_str));
        // echo "d - $hashed_keys<br>";

        $hash_enum = 0;
        for ($i = 0; $i < $length; $i++) {
            if (!isset($keys[$i])) { // Undefined offset
                $keys[$i] = $hashed_keys[$hash_enum];
                $hash_enum++;
            }
        }
        ksort($keys);
        return implode('', $keys);
    }

    /**
     * Funzione generazione caratteri random
     *
     * @return string Caratteri random
     */
    function rand_alphanumeric()
    {
        $subsets[0] = array('min' => 48, 'max' => 57); // ascii digits
        $subsets[1] = array('min' => 65, 'max' => 90); // ascii lowercase English letters
        $subsets[2] = array('min' => 97, 'max' => 122); // ascii uppercase English letters
        $s = rand(0, 2);
        $ascii_code = rand($subsets[$s]['min'], $subsets[$s]['max']);
        return chr($ascii_code);
    }


    /**
     * Check if the given token is valid
     * @param $str
     * @return bool
     */
    function verify_token($str) {
        $length = strlen($str);
        $keys = str_split($str);
        $length_odd = (($length % 2) != 0);
        $length_has_root = (strpos(sqrt($length), '.') === false);
        $offset = $length_odd ? 1 : 0;
        $key_str = '';
        $key_str .= $keys[$pos1 = (int)(0 + $offset)];
        $key_str .= $keys[$pos2 = (int)(($length / 4) - 1 + $offset)];
        $key_str .= $keys[$pos3 = (int)(($length / 2) - 1 + $offset)];
        $key_str .= $keys[$pos4 = (int)(($length - 2) + $offset)];
        $hashed_keys = $length_has_root ? sha1(md5($key_str)) : sha1(sha1($key_str));
        $hash_string = '';
        for ($i = 0; $i < $length; $i++) {
            if ($i != $pos1 &&
                $i != $pos2 &&
                $i != $pos3 &&
                $i != $pos4) {
                $hash_string .= $keys[$i];
            }
        }
        $hash_length = $length - 4;
        return ($hash_string == substr($hashed_keys, 0, $hash_length));
    }
}