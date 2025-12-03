<?php

namespace App\Jobs;

use App\Models\Evidence;
use App\Services\IpfsPinner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PinEvidenceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Evidence $evidence;

    public function __construct(Evidence $evidence)
    {
        $this->evidence = $evidence;
        $this->onQueue('ipfs');
    }

    public function handle(IpfsPinner $pinner): void
    {
        $this->evidence->markPinning();

        // temporary local path to pass to HTTP client
        $disk = $this->evidence->disk;
        $path = $this->evidence->path;
        $localPath = Storage::disk($disk)->path($path);

        try {
            $cid = $pinner->pinFile($localPath, $this->evidence->filename);
            $this->evidence->markPinned($cid);
        } catch (Throwable $e) {
            report($e);
            $this->evidence->markFailed();
            throw $e;
        }
    }
}
