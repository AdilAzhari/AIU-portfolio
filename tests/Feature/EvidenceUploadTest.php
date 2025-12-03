<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EvidenceUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_and_job_dispatched()
    {
        Queue::fake();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('evidence.store'), [
                'file' => UploadedFile::fake()->create('doc.pdf', 1000),
                'title' => 'Test',
            ])
            ->assertRedirect();

        // assert DB entry exists & job dispatched
        $this->assertDatabaseHas('evidences', [
            'user_id' => $user->id,
            'filename' => 'doc.pdf',
        ]);

        Queue::assertPushed(\App\Jobs\PinEvidenceJob::class);
    }
}
