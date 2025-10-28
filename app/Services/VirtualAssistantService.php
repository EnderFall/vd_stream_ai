<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\MonthlyTarget;
use App\Models\VARecommendation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class VirtualAssistantService
{
    protected $baseUrl;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        // Using OpenRouter API
        $this->baseUrl = 'https://openrouter.ai/api/v1/chat/completions';
        $this->apiKey = config('services.openrouter.api_key');
        $this->model = config('services.openrouter.model');
    }

    /**
     * Generate AI recommendations based on user's schedules and transactions
     */
    public function generateRecommendations(Collection $schedules, Collection $transactions, ?MonthlyTarget $target): array
    {
        // Prepare data for AI analysis
        $scheduleData = $this->prepareScheduleData($schedules);
        $transactionData = $this->prepareTransactionData($transactions);
        $targetData = $this->prepareTargetData($target);

        // Create prompt for OpenAI
        $prompt = $this->buildPrompt($scheduleData, $transactionData, $targetData);

        try {
            // Use OpenRouter API for recommendations
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? null;

                if ($content) {
                    // Try to parse JSON response
                    $parsed = json_decode($content, true);
                    if ($parsed && is_array($parsed)) {
                        return $this->formatRecommendations($parsed);
                    }
                }
                return $this->getFallbackRecommendations();
            } else {
                throw new \Exception('OpenRouter API request failed: ' . $response->body());
            }

        } catch (\Exception $e) {
            // Fallback recommendations if AI fails
            return $this->getFallbackRecommendations();
        }
    }

    private function prepareScheduleData(Collection $schedules): array
    {
        return $schedules->map(function ($schedule) {
            return [
                'platform' => $schedule->platform,
                'start_time' => $schedule->start_at->format('H:i'),
                'day_of_week' => $schedule->start_at->dayOfWeek,
                'estimated_income' => $schedule->estimated_income,
                'status' => $schedule->status,
            ];
        })->toArray();
    }

    private function prepareTransactionData(Collection $transactions): array
    {
        $monthlyIncome = $transactions->where('type', 'income')->sum('amount');
        $monthlyExpenses = $transactions->where('type', 'expense')->sum('amount');

        $platformPerformance = $transactions->where('type', 'income')
            ->whereNotNull('schedule_id')
            ->groupBy(function ($transaction) {
                return $transaction->schedule->platform ?? 'unknown';
            })
            ->map(function ($group) {
                return $group->sum('amount');
            });

        return [
            'monthly_income' => $monthlyIncome,
            'monthly_expenses' => $monthlyExpenses,
            'net_profit' => $monthlyIncome - $monthlyExpenses,
            'platform_performance' => $platformPerformance->toArray(),
        ];
    }

    private function prepareTargetData(?MonthlyTarget $target): array
    {
        if (!$target) {
            return ['has_target' => false];
        }

        return [
            'has_target' => true,
            'target_amount' => $target->target_amount,
            'note' => $target->note,
        ];
    }

    private function buildPrompt(array $scheduleData, array $transactionData, array $targetData): string
    {
        $prompt = "Analyze this influencer's data and provide 3-5 strategic recommendations for optimal posting times and content strategies:\n\n";

        $prompt .= "Current Schedules:\n" . json_encode($scheduleData, JSON_PRETTY_PRINT) . "\n\n";

        $prompt .= "Financial Data:\n" . json_encode($transactionData, JSON_PRETTY_PRINT) . "\n\n";

        $prompt .= "Monthly Target:\n" . json_encode($targetData, JSON_PRETTY_PRINT) . "\n\n";

        $prompt .= "Please provide recommendations in this JSON format:\n";
        $prompt .= "[\n";
        $prompt .= "  {\n";
        $prompt .= "    \"start_time\": \"2024-01-15 14:30:00\",\n";
        $prompt .= "    \"reason\": \"High engagement time based on platform analytics\",\n";
        $prompt .= "    \"confidence\": 85\n";
        $prompt .= "  }\n";
        $prompt .= "]\n\n";
        $prompt .= "Focus on:\n";
        $prompt .= "- Best posting times for different platforms\n";
        $prompt .= "- Content strategies based on financial performance\n";
        $prompt .= "- Gap analysis for reaching monthly targets\n";
        $prompt .= "- Platform-specific optimization";

        return $prompt;
    }

    private function formatRecommendations(?array $aiResponse): array
    {
        if (!$aiResponse || !is_array($aiResponse)) {
            return $this->getFallbackRecommendations();
        }

        return array_map(function ($rec) {
            return [
                'start_time' => Carbon::parse($rec['start_time'] ?? now()->addDays(rand(1, 7))),
                'reason' => $rec['reason'] ?? 'AI-generated recommendation',
                'confidence' => min(100, max(0, $rec['confidence'] ?? 70)),
            ];
        }, array_slice($aiResponse, 0, 5)); // Limit to 5 recommendations
    }

    private function getFallbackRecommendations(): array
    {
        $baseTime = now()->startOfDay()->addHours(9); // 9 AM today

        return [
            [
                'start_time' => $baseTime->copy()->addHours(2), // 11 AM
                'reason' => 'Peak engagement time for Instagram and TikTok based on general analytics',
                'confidence' => 75,
            ],
            [
                'start_time' => $baseTime->copy()->addHours(6), // 3 PM
                'reason' => 'Optimal time for YouTube content based on viewer patterns',
                'confidence' => 70,
            ],
            [
                'start_time' => $baseTime->copy()->addDays(1)->addHours(1), // Tomorrow 10 AM
                'reason' => 'Good time for Twitter/X posts to maximize reach',
                'confidence' => 65,
            ],
        ];
    }

    /**
     * Generate AI chat response for conversational interaction
     */
    public function generateChatResponse(string $userMessage, array $conversationHistory = []): string
    {
        // Prepare conversation context for the AI
        $context = $this->prepareChatContext($userMessage, $conversationHistory);

        try {
            // Build messages array for OpenRouter
            $messages = [];

            // Add conversation history
            for ($i = 0; $i < count($context['past_user_inputs']); $i++) {
                $messages[] = [
                    'role' => 'user',
                    'content' => $context['past_user_inputs'][$i]
                ];
                if (isset($context['generated_responses'][$i])) {
                    $messages[] = [
                        'role' => 'assistant',
                        'content' => $context['generated_responses'][$i]
                    ];
                }
            }

            // Add current message
            $messages[] = [
                'role' => 'user',
                'content' => $context['current_message']
            ];

            // Use OpenRouter API for chat
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 150,
                'temperature' => 0.8,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? null;

                if ($content) {
                    // Clean up the response
                    $content = $this->cleanChatResponse($content, $userMessage);
                    return $content ?: $this->getFallbackChatResponse();
                }
            }

            throw new \Exception('OpenRouter API request failed: ' . $response->body());

        } catch (\Exception $e) {
            // Return fallback response
            return $this->getFallbackChatResponse();
        }
    }

    private function prepareChatContext(string $userMessage, array $conversationHistory): array
    {
        $pastUserInputs = [];
        $generatedResponses = [];

        // Take last 5 exchanges for context (limit to prevent token overflow)
        $recentHistory = array_slice($conversationHistory, -10);

        foreach ($recentHistory as $message) {
            if ($message['sender'] === 'user') {
                $pastUserInputs[] = $message['message'];
            } elseif ($message['sender'] === 'ai') {
                $generatedResponses[] = $message['message'];
            }
        }

        // Ensure we have at least one initial exchange for the model
        if (empty($pastUserInputs)) {
            $pastUserInputs[] = 'Hello';
            $generatedResponses[] = 'Hi there! How can I help you today?';
        }

        return [
            'past_user_inputs' => $pastUserInputs,
            'generated_responses' => $generatedResponses,
            'current_message' => $userMessage
        ];
    }

    private function cleanChatResponse(string $response, string $userMessage): string
    {
        // Remove the user's message if it's echoed back
        $response = str_replace($userMessage, '', $response);

        // Clean up common artifacts
        $response = trim($response);

        // Remove any leading/trailing punctuation that might be artifacts
        $response = ltrim($response, '.,!?;');

        return trim($response);
    }

    private function getFallbackChatResponse(): string
    {
        $responses = [
            "I'm here to help you with your influencer activities. What would you like to know?",
            "How can I assist you with your content scheduling and financial planning today?",
            "Feel free to ask me anything about optimizing your social media presence or managing your finances.",
            "I'm your AI assistant for influencer success. What can I help you with?",
            "Let me know how I can support your influencer journey today."
        ];

        return $responses[array_rand($responses)];
    }
}
