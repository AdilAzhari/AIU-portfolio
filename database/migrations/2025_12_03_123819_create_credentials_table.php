<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issuer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('evidence_id')->nullable()->constrained('evidences')->nullOnDelete();

            // Credential core metadata
            $table->string('title');
            $table->text('description')->nullable();

            // Status flow
            $table->enum('status', [
                'pending',       // created but not approved
                'issued',        // approved
                'revoked',       // invalidated later
            ])->default('pending')->index();

            // Revocation information
            $table->text('revocation_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();

            // Future-proof fields for anchoring / verification
            $table->string('cid')->nullable()->index();       // optional: IPFS metadata CID
            $table->string('anchor_hash')->nullable()->index(); // optional: on-chain hash
            $table->timestamp('anchored_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
