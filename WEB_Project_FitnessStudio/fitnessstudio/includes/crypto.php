<?php

$key = '0123456789abcdef0123456789abcdef';
$iv  = 'abcdef9876543210';

function encrypt($plaintext) {
    global $key, $iv;

    $raw = openssl_encrypt(
        $plaintext,
        'aes-256-cbc',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    return base64_encode($raw);
}

function decrypt($ciphertext_b64) {
    global $key, $iv;

    $raw = base64_decode($ciphertext_b64);

    return openssl_decrypt(
        $raw,
        'aes-256-cbc',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
}
