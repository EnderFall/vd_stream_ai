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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_path'); // Path to video file
            $table->string('thumbnail_path')->nullable(); // Path to thumbnail
            $table->string('duration')->nullable(); // Video duration in seconds
            $table->string('file_size')->nullable(); // File size in bytes
            $table->enum('status', ['processing', 'published', 'draft', 'private'])->default('processing');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->json('tags')->nullable(); // Video tags
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['status', 'visibility']);
            $table->index('user_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
