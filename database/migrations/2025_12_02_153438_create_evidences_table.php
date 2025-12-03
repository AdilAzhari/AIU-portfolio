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
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // uploader
            $table->string('filename');
            $table->string('path'); // storage path
            $table->string('disk')->default(config('filesystems.default'));
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('sha256', 128)->nullable()->index();
            $table->string('cid')->nullable()->index();
            $table->json('metadata')->nullable(); // metadata (title, description, tags)
            $table->enum('visibility', ['private', 'public'])->default('private');
            $table->enum('status', ['uploaded', 'pinning', 'pinned', 'failed'])->default('uploaded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidences');
    }
};
