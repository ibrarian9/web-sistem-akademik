<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ESignatureService;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ESignatureTest extends TestCase
{
    public function test_esignature_service_generates_valid_code_and_qr()
    {
        $code = ESignatureService::generateCode('RAP', 123, '2026-07-21');
        $this->assertStringStartsWith('TTD-RAP-123-', $code);

        $url = ESignatureService::getVerificationUrl($code);
        $this->assertStringContainsString('/verifikasi-dokumen/' . $code, $url);

        $qr = ESignatureService::generateQrCode($url);
        $this->assertStringStartsWith('data:image/', $qr);
    }

    public function test_public_verification_route_loads_successfully()
    {
        $code = ESignatureService::generateCode('RES', 99, '2026-07-21');
        
        $response = $this->get('/verifikasi-dokumen/' . $code);

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN RESMI SAH');
        $response->assertSee($code);
    }

    public function test_signature_data_helper_returns_default_officers()
    {
        $data = ESignatureService::getSignatureData('kepala_sekolah', 'RAP', 1);

        $this->assertNotEmpty($data['nama']);
        $this->assertNotEmpty($data['nip']);
        $this->assertEquals('TTD-RAP-1-', substr($data['code'], 0, 10));
    }
}
