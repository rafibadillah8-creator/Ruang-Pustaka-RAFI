<?php

namespace App\Services;

use InvalidArgumentException;
use Midtrans\Config;
use RuntimeException;

class MidtransGateway
{
    public function configure(): void
    {
        $clientKey = trim((string) config('services.midtrans.client_key'));
        $serverKey = trim((string) config('services.midtrans.server_key'));
        $isProduction = (bool) config('services.midtrans.is_production', false);

        if ($clientKey === '' || $serverKey === '') {
            throw new InvalidArgumentException('MIDTRANS_CLIENT_KEY dan MIDTRANS_SERVER_KEY wajib diisi.');
        }

        self::assertCredentialTypesMatch($clientKey, $serverKey);

        // Mengandalkan nilai is_production dari .env secara ketat.
        // Dihapus: Auto-detect environment, karena beberapa akun Midtrans Sandbox 
        // ternyata menghasilkan key tanpa prefix 'SB-' (seperti kasus ini).

        Config::$serverKey = $serverKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $curlOptions = [
            CURLOPT_HTTPHEADER => [],
        ];
        $caCertificate = config('services.midtrans.ca_certificate');

        if (!$caCertificate) {
            $phpCaCertificate = ini_get('curl.cainfo');
            $laragonCaCertificate = dirname(base_path(), 2)
                . DIRECTORY_SEPARATOR . 'etc'
                . DIRECTORY_SEPARATOR . 'ssl'
                . DIRECTORY_SEPARATOR . 'cacert.pem';

            if ($phpCaCertificate && is_file($phpCaCertificate)) {
                $caCertificate = $phpCaCertificate;
            } elseif ($phpCaCertificate && is_file($laragonCaCertificate)) {
                $caCertificate = $laragonCaCertificate;
            } elseif ($phpCaCertificate) {
                throw new RuntimeException(
                    'Sertifikat CA pada curl.cainfo tidak ditemukan. Atur MIDTRANS_CA_CERT ke path sertifikat yang valid.'
                );
            }
        }

        if ($caCertificate) {
            if (!is_file($caCertificate)) {
                throw new RuntimeException('File MIDTRANS_CA_CERT tidak ditemukan.');
            }

            $curlOptions[CURLOPT_CAINFO] = $caCertificate;
        }

        Config::$curlOptions = $curlOptions;
    }

    public function transactionStatus(string $orderId): array
    {
        $this->configure();

        $status = json_decode(
            json_encode(\Midtrans\Transaction::status($orderId), JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($status)) {
            throw new RuntimeException('Midtrans returned an invalid transaction status response.');
        }

        return $status;
    }

    public static function assertCredentialTypesMatch(string $clientKey, string $serverKey): void
    {
        $clientPrefixes = ['Mid-client-', 'SB-Mid-client-'];
        $serverPrefixes = ['Mid-server-', 'SB-Mid-server-'];
        $hasClientPrefix = false;
        $hasServerPrefix = false;

        foreach ($clientPrefixes as $prefix) {
            $hasClientPrefix = $hasClientPrefix || str_starts_with($clientKey, $prefix);
        }

        foreach ($serverPrefixes as $prefix) {
            $hasServerPrefix = $hasServerPrefix || str_starts_with($serverKey, $prefix);
        }

        $clientUsesSandboxPrefix = str_starts_with($clientKey, 'SB-Mid-client-');
        $serverUsesSandboxPrefix = str_starts_with($serverKey, 'SB-Mid-server-');

        if (!$hasClientPrefix || !$hasServerPrefix || $clientUsesSandboxPrefix !== $serverUsesSandboxPrefix) {
            throw new InvalidArgumentException(
                'MIDTRANS_CLIENT_KEY dan MIDTRANS_SERVER_KEY harus berupa pasangan key Midtrans yang valid.'
            );
        }
    }

    public static function snapJsUrl(bool $isProduction): string
    {
        // Dihapus auto-detect karena key sandbox user ternyata tidak ada prefix SB-

        return $isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }
}
