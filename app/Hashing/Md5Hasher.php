<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class Md5Hasher implements HasherContract
{
    /**
     * Hash the given value.
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function make($value, array $options = [])
    {
        return md5($value);
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param string $value
     * @param string $hashedValue
     * @param array $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return hash_equals($hashedValue, $this->make($value, $options));
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param string $hashedValue
     * @param array $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        // MD5 hashes are always 32 characters long
        return strlen($hashedValue) !== 32;
    }

    /**
     * Get information about a given hash.
     *
     * @param string $hashedValue
     * @return array
     */
    public function info($hashedValue)
    {
        return [
            'algo' => 'md5',
            'algoName' => 'md5',
            'options' => [],
        ];
    }
}