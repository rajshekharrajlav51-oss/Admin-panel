<?php

namespace Tests\Unit;

use App\Services\FirebaseConfigService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FirebaseConfigServiceTest extends TestCase
{
    #[Test]
    public function it_rejects_an_invalid_service_account_payload(): void
    {
        $service = new FirebaseConfigService();

        $status = $service->validateServiceAccountData([
            'type' => 'service_account',
            'project_id' => 'demo-project',
        ]);

        $this->assertFalse($status['valid']);
        $this->assertContains('client_email', $status['errors']);
        $this->assertContains('private_key', $status['errors']);
    }

    #[Test]
    public function it_accepts_a_valid_service_account_payload(): void
    {
        $service = new FirebaseConfigService();

        $status = $service->validateServiceAccountData([
            'type' => 'service_account',
            'project_id' => 'demo-project',
            'client_email' => 'firebase-adminsdk@example.iam.gserviceaccount.com',
            'private_key' => "-----BEGIN PRIVATE KEY-----\nabc\n-----END PRIVATE KEY-----\n",
        ]);

        $this->assertTrue($status['valid']);
        $this->assertSame('demo-project', $status['project_id']);
    }
}
