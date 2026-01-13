<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Credential;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function generate(Credential $credential): View
    {
        // Ensure user owns this credential
        if ($credential->student_id !== auth()->id()) {
            abort(403);
        }

        // Generate verification URL
        $url = route('verify.show', $credential->id);

        // Generate QR code as SVG
        $qr = QrCode::size(300)
            ->format('svg')
            ->generate($url);

        return view('qrcode.show', [
            'qrCode' => $qr,
            'credential' => $credential,
            'url' => $url,
        ]);
    }
}
