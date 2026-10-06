<?php

namespace Tests\Unit;

use App\Services\MidtransGateway;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MidtransGatewayTest extends TestCase
{
    public function test_accepts_midtrans_keys_used_by_sandbox_dashboard(): void
    {
        MidtransGateway::assertCredentialTypesMatch('Mid-client-example', 'Mid-server-example');

        $this->assertTrue(true);
    }

    public function test_accepts_explicit_sandbox_key_prefixes(): void
    {
        MidtransGateway::assertCredentialTypesMatch('SB-Mid-client-example', 'SB-Mid-server-example');
        $this->assertTrue(true);
    }

    public function test_rejects_mismatched_client_and_server_key_prefixes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MidtransGateway::assertCredentialTypesMatch('Mid-client-example', 'SB-Mid-server-example');
    }

    public function test_rejects_unrecognized_key_prefixes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MidtransGateway::assertCredentialTypesMatch('invalid-client', 'invalid-server');
    }
}
