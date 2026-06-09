<?php

namespace App\Services;

class EncryptionService
{
    protected string $key;
    protected string $method = 'aes-256-cbc';

    public function __construct()
    {
        // Use custom key or app key
        $key = env('STREAM_ENCRYPTION_KEY', env('APP_KEY', 'default-key-32-chars-long-12345678'));
        // MD5 hash it to guarantee a 32-byte key for AES-256
        $this->key = substr(hash('sha256', $key), 0, 32);
    }

    public function encrypt(string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($value, $this->method, $this->key, 0, $iv);
        
        // Return IV + Encrypted string base64 encoded
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $payload): string
    {
        if (empty($payload)) {
            return '';
        }

        try {
            $data = base64_decode($payload);
            $ivLength = openssl_cipher_iv_length($this->method);
            
            if (strlen($data) <= $ivLength) {
                return '';
            }

            $iv = substr($data, 0, $ivLength);
            $encryptedText = substr($data, $ivLength);
            
            $decrypted = openssl_decrypt($encryptedText, $this->method, $this->key, 0, $iv);
            
            return $decrypted ?: '';
        } catch (\Exception $e) {
            return '';
        }
    }
}
