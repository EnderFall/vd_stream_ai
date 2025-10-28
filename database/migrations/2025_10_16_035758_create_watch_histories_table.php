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
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->integer('watched_seconds')->default(0); // How many seconds watched
            $table->integer('total_seconds')->default(0); // Total video duration
            $table->boolean('completed')->default(false); // Whether user watched the entire video
            $table->timestamp('last_watched_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'video_id']); // One record per user-video pair
            $table->index(['user_id', 'last_watched_at']);
            $table->index('video_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
