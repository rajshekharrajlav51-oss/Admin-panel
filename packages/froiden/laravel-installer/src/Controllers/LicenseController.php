<?php

namespace Froiden\LaravelInstaller\Controllers;

use Froiden\LaravelInstaller\Helpers\Reply;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    public function form()
    {
        return view('vendor.installer.license');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'purchase_code' => 'required|string|max:255',
            'domain_url' => 'required|string|max:255',
        ]);

        $this->persistLicense(
            $validated['purchase_code'],
            $validated['domain_url']
        );

        return Reply::redirect(
            route('LaravelInstaller::environment'),
            'License details saved successfully.'
        );
    }

    private function persistLicense(string $purchaseCode, string $domainUrl): void
    {
        $token = (string) Str::uuid();
        $signature = hash('sha256', $purchaseCode . '|' . $domainUrl);

        $envPath = base_path('.env');
        $env = file_exists($envPath) ? (string) file_get_contents($envPath) : '';

        $rows = preg_split("/\r\n|\n|\r/", $env) ?: [];
        $cleanRows = preg_grep(
            '/^(LICENSE_PURCHASE_CODE|LICENSE_TOKEN|LICENSE_SIGNATURE)=/i',
            $rows,
            PREG_GREP_INVERT
        );

        $licenseBlock = [
            'LICENSE_PURCHASE_CODE="' . addslashes($purchaseCode) . '"',
            'LICENSE_TOKEN="' . $token . '"',
            'LICENSE_SIGNATURE="' . $signature . '"',
        ];

        $newEnv = trim(implode(PHP_EOL, $cleanRows));
        if ($newEnv !== '') {
            $newEnv .= PHP_EOL;
        }

        file_put_contents($envPath, $newEnv . implode(PHP_EOL, $licenseBlock) . PHP_EOL);
    }
}
