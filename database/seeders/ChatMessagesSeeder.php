<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatMessage;
use App\Models\User;

class ChatMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create one if none exists
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Create some sample chat messages
        ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => 'Hello, can you help me with my influencer schedule?',
        ]);

        ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'ai',
            'message' => 'Hi there! I\'d be happy to help you with your influencer schedule. What specific aspects would you like assistance with?',
        ]);

        ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => 'I need recommendations for the best times to post on Instagram.',
        ]);

        ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'ai',
            'message' => 'For Instagram, the best times to post are typically weekdays between 11 AM - 1 PM and 7-9 PM, and weekends around 10-11 AM. This is when your audience is most active. Would you like me to analyze your current schedule and provide personalized recommendations?',
        ]);
    }
}
