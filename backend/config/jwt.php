<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this in your .env file, as it will be used to sign
    | your tokens. A helper command is provided for this:
    | `php artisan jwt:secret`
    |
    | Note: This will be used for Symmetric algorithms only (HMAC),
    | since RSA and ECDSA use a private/public key combo (see below).
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Keys
    |--------------------------------------------------------------------------
    |
    | The basic Symmetric algorithms (HMAC) use a shared secret configuration.
    | Asymmetric algorithms (RSA, ECDSA) require a private and public key
    | pair.
    |
    | You can set either the path or the key content itself here.
    |
    */

    'keys' => [

        /*
        |--------------------------------------------------------------------------
        | Public Key
        |--------------------------------------------------------------------------
        |
        | Path to the public key.
        |
        */

        'public' => env('JWT_PUBLIC_KEY'),

        /*
        |--------------------------------------------------------------------------
        | Private Key
        |--------------------------------------------------------------------------
        |
        | Path to the private key.
        |
        */

        'private' => env('JWT_PRIVATE_KEY'),

        /*
        |--------------------------------------------------------------------------
        | Passphrase
        |--------------------------------------------------------------------------
        |
        | The passphrase for your private key. Can be null if none.
        |
        */

        'passphrase' => env('JWT_PASSPHRASE'),

    ],

    /*
    |--------------------------------------------------------------------------
    | JWT time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the issued token will be
    | valid for. Defaults to 1 hour.
    |
    | You can also set this to null, to yield a never expiring token.
    |
    */

    'ttl' => env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Refresh time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. I.e. The user can log back in within 2 weeks without having to
    | enter their credentials again. Defaults to 2 weeks.
    |
    | You can also set this to null, to yield an infinite refresh window.
    |
    */

    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm inside the header.
    |
    | Supported:
    | HMAC: HS256, HS384, HS512
    | RSA: RS256, RS384, RS512
    | ECDSA: ES256, ES384, ES512
    | EdDSA: EdDSA
    |
    */

    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    |
    | Specify the required claims that must exist in any token as a validation
    | step. These are dynamic claims, which will be verified against the
    | corresponding value in the token payload.
    |
    */

    'required_claims' => [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistent Connections
    |--------------------------------------------------------------------------
    |
    | By enabling this option, the token will be bound to the specific IP address
    | and User-Agent of the request. This helps to mitigate session hijacking.
    |
    */

    'persistent_connections' => false,

    /*
    |--------------------------------------------------------------------------
    | Lock Subject
    |--------------------------------------------------------------------------
    |
    | By default the class name of the bound model will be cached as a claim
    | to check that the correct user model is retrieved. If this is disabled,
    | the subject will only represent the ID.
    |
    */

    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | JWT blacklist grace period
    |--------------------------------------------------------------------------
    |
    | When multiple requests are made at the same time, it can lead to multiple
    | tokens being generated, this grace period allows for old tokens to still
    | be valid for the specified time (in seconds) to prevent race conditions.
    |
    */

    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | To disable the blacklist functionality, set this to false.
    | Note: You will no longer be able to invalidate tokens.
    |
    */

    'show_black_list_exception' => env('JWT_SHOW_BLACK_LIST_EXCEPTION', true),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Cache Driver
    |--------------------------------------------------------------------------
    |
    | The cache driver used to store the blacklisted tokens.
    |
    */

    'blacklist_driver' => env('JWT_BLACKLIST_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Token signers
    |--------------------------------------------------------------------------
    |
    | Define custom token signers for cryptographic operations if required.
    |
    */

    'providers' => [

        'jwt' => PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci::class,

        'auth' => PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate::class,

        'storage' => PHPOpenSourceSaver\JWTAuth\Providers\Storage\Illuminate::class,

    ],

];
