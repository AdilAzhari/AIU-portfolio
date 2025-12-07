<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenceRequest;
use App\Jobs\PinEvidenceJob;
use App\Models\Evidence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvidenceController extends Controller
{
    public function store(StoreEvidenceRequest $request): RedirectResponse
    {
        $file = $request->file('file');

        // sanitize & generate filename
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).
                   '-'.time().'.'.$file->getClientOriginalExtension();

        $disk = config('filesystems.default');
        $path = $file->storeAs('evidence/'.auth()->id(), $filename, $disk);

        // compute sha256
        $localAbsolute = Storage::disk($disk)->path($path);
        $sha256 = hash_file('sha256', $localAbsolute);

        $evidence = Evidence::create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => $disk,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'sha256' => $sha256,
            'metadata' => [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
            ],
            'visibility' => $request->input('visibility', 'private'),
            'status' => 'uploaded',
        ]);

        $evidence->log('evidence_uploaded', [
            'sha256' => $evidence->sha256,
        ]);

        // dispatch job (async) to pin to IPFS
        PinEvidenceJob::dispatch($evidence);

        try {
            $response = Http::attach(
                'file',
                file_get_contents($absolute),
                $filename
            )->post('http://127.0.0.1:5001/api/v0/add');

            if ($response->ok() && isset($response['Hash'])) {
                $cid = $response['Hash'];
                $evidence->markPinned($cid);
            } else {
                $evidence->markFailed();
            }
        } catch (\Throwable) {
            // IPFS node offline or unreachable
            $evidence->markFailed();
        }

        return back()->with('status', 'Evidence uploaded and queued for IPFS pinning.');
    }
}
