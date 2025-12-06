<?php

namespace App\Helpers;

class JWTHelper
{
    private static $secretKey = 'responsi_mobile_buku_secret_key_2024_very_long_and_secure';

    /**
     * Generate Simple Token
     * 
     * @param array $payload Data yang akan disimpan dalam token
     * @return string Encoded Token
     */
    public static function generateToken($payload)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (24 * 60 * 60); // Token berlaku 24 jam

        $token = [
            'iss' => 'responsi-mobile-buku-api', // Issuer
            'aud' => 'responsi-mobile-buku-app', // Audience
            'iat' => $issuedAt,                  // Issued at
            'exp' => $expirationTime,            // Expiration time
            'data' => $payload                   // User data
        ];

        $tokenString = base64_encode(json_encode($token));
        $signature = hash_hmac('sha256', $tokenString, self::$secretKey);
        
        return $tokenString . '.' . $signature;
    }

    /**
     * Validate and Decode Token
     * 
     * @param string $token Encoded Token
     * @return object|false Decoded token data atau false jika invalid
     */
    public static function validateToken($token)
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 2) {
                return false;
            }

            $tokenString = $parts[0];
            $signature = $parts[1];

            // Verify signature
            $expectedSignature = hash_hmac('sha256', $tokenString, self::$secretKey);
            if (!hash_equals($expectedSignature, $signature)) {
                return false;
            }

            // Decode token
            $decoded = json_decode(base64_decode($tokenString));
            
            // Check expiration
            if (!$decoded || !isset($decoded->exp) || time() > $decoded->exp) {
                return false;
            }

            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get User ID from Token
     * 
     * @param string $token JWT Token
     * @return int|false User ID atau false jika invalid
     */
    public static function getUserIdFromToken($token)
    {
        $decoded = self::validateToken($token);
        if ($decoded && isset($decoded->data->id)) {
            return $decoded->data->id;
        }
        return false;
    }

    /**
     * Get User Data from Token
     * 
     * @param string $token JWT Token
     * @return object|false User data atau false jika invalid
     */
    public static function getUserDataFromToken($token)
    {
        $decoded = self::validateToken($token);
        if ($decoded && isset($decoded->data)) {
            return $decoded->data;
        }
        return false;
    }

    /**
     * Check if Token is Expired
     * 
     * @param string $token JWT Token
     * @return bool True jika expired, false jika masih valid
     */
    public static function isTokenExpired($token)
    {
        $decoded = self::validateToken($token);
        if ($decoded && isset($decoded->exp)) {
            return time() > $decoded->exp;
        }
        return true;
    }
}