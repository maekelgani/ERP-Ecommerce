<?php

/**
 * API Configuration
 * Third-party API keys and settings for Biteship and Midtrans
 */

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Biteship API Configuration (Shipping)
define('BITESHIP_API_KEY', $_ENV['BITESHIP_API_KEY'] ?? '');
define('BITESHIP_BASE_URL', 'https://api.biteship.com/v1');

// Midtrans API Configuration (Payment)
define('MIDTRANS_CLIENT_KEY', $_ENV['MIDTRANS_CLIENT_KEY'] ?? '');
define('MIDTRANS_SERVER_KEY', $_ENV['MIDTRANS_SERVER_KEY'] ?? '');
define('MIDTRANS_IS_PRODUCTION', ($_ENV['MIDTRANS_IS_PRODUCTION'] ?? 'false') === 'true');
define('MIDTRANS_BASE_URL', MIDTRANS_IS_PRODUCTION
    ? 'https://api.midtrans.com'
    : 'https://api.sandbox.midtrans.com');

// Store Origin Configuration for Biteship (Warehouse/Store location)
define('STORE_ORIGIN_POSTAL_CODE', '10730'); // Mangga Dua Mall Jakarta Pusat
define('STORE_ORIGIN_AREA_ID', 'IDNP10JKTS.JKPD.SABE.MDUS'); // Biteship Area ID for origin
use Midtrans\Config as MidtransConfig;

/**
 * Initialize Midtrans Configuration
 * Call this before using Midtrans SDK
 */
// function initMidtrans()
// {
//     if (class_exists('\Midtrans\Config')) {
//         \Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
//         \Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
//         \Midtrans\Config::$isSanitized = true;
//         \Midtrans\Config::$is3ds = true;
//     }
// }

function initMidtrans(): void
{
    if (class_exists(MidtransConfig::class)) {
        MidtransConfig::$serverKey = MIDTRANS_SERVER_KEY;
        MidtransConfig::$isProduction = MIDTRANS_IS_PRODUCTION;
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
    }
}


/**
 * Get Biteship Authorization Header
 */
function getBiteshipHeaders()
{
    return [
        'Authorization' => 'Bearer ' . BITESHIP_API_KEY,
        'Content-Type' => 'application/json'
    ];
}

/**
 * Get Midtrans Authorization Header (Basic Auth)
 */
function getMidtransHeaders()
{
    return [
        'Authorization' => 'Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':'),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];
}
