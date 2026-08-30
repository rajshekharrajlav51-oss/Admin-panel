<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LicenseRevalidateController extends Controller
{
    public function form()
    {
        return redirect()->route('LaravelInstaller::license');
    }

    public function verify(Request $request)
    {
        return redirect()->route('LaravelInstaller::license.verify', $request->all());
    }
}
