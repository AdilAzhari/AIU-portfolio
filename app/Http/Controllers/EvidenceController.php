<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenceRequest;
use App\Jobs\PinEvidenceJob;
use App\Models\Evidence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EvidenceController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Evidence/Upload');
    }

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

        return back()->with('status', 'Evidence uploaded and queued for IPFS pinning.');
    }
}
