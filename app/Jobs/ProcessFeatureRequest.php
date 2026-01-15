<?php

namespace App\Jobs;

use App\Models\FeatureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessFeatureRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public FeatureRequest $featureRequest
    ) {}

    public function handle(): void
    {
        $this->featureRequest->markAsProcessing();

        try {
            $provider = config('feedback.ai_provider', 'claude');

            $response = match ($provider) {
                'openai' => $this->processWithOpenAI(),
                'claude' => $this->processWithClaude(),
                default => throw new \Exception("Unknown AI provider: {$provider}"),
            };

            if ($response['success']) {
                $prUrl = $this->createPullRequest($response['content']);
                $this->featureRequest->markAsCompleted($prUrl, $response['content']);
            } else {
                $this->featureRequest->markAsFailed($response['error'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            Log::error('Failed to process feature request', [
                'feature_request_id' => $this->featureRequest->id,
                'error' => $e->getMessage(),
            ]);

            $this->featureRequest->markAsFailed($e->getMessage());

            throw $e;
        }
    }

    protected function processWithClaude(): array
    {
        $apiKey = config('feedback.claude_api_key');

        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'Claude API key not configured'];
        }

        $prompt = $this->buildPrompt();

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => config('feedback.claude_model', 'claude-sonnet-4-20250514'),
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        if ($response->successful()) {
            $content = $response->json('content.0.text', '');
            return ['success' => true, 'content' => $content];
        }

        return [
            'success' => false,
            'error' => $response->json('error.message', 'Claude API request failed'),
        ];
    }

    protected function processWithOpenAI(): array
    {
        $apiKey = config('feedback.openai_api_key');

        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'OpenAI API key not configured'];
        }

        $prompt = $this->buildPrompt();

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('feedback.openai_model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant that analyzes feature requests and provides implementation suggestions for a Laravel application.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => 4096,
        ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content', '');
            return ['success' => true, 'content' => $content];
        }

        return [
            'success' => false,
            'error' => $response->json('error.message', 'OpenAI API request failed'),
        ];
    }

    protected function buildPrompt(): string
    {
        $type = ucfirst(str_replace('_', ' ', $this->featureRequest->type));

        return <<<PROMPT
You are analyzing a {$type} for a Laravel-based task management application called TaskFlow.

## Request Details

**Title:** {$this->featureRequest->title}

**Description:**
{$this->featureRequest->description}

**Priority:** {$this->featureRequest->priority}

**Submitted by:** {$this->featureRequest->user->email}

## Instructions

Please analyze this request and provide:

1. **Feasibility Assessment**: Is this request feasible? What are the main considerations?

2. **Implementation Plan**: If feasible, provide a high-level implementation plan including:
   - Files that would need to be created or modified
   - Database changes (if any)
   - Key code changes

3. **Code Snippets**: Provide relevant code snippets for the most important changes.

4. **Estimated Complexity**: Rate the complexity (Low/Medium/High) and explain why.

5. **Potential Issues**: Any potential issues or edge cases to consider.

Format your response in clear markdown sections.
PROMPT;
    }

    protected function createPullRequest(string $aiResponse): ?string
    {
        $githubToken = config('feedback.github_token');
        $repoOwner = config('feedback.github_repo_owner');
        $repoName = config('feedback.github_repo_name');

        if (empty($githubToken) || empty($repoOwner) || empty($repoName)) {
            Log::warning('GitHub configuration incomplete, skipping PR creation', [
                'feature_request_id' => $this->featureRequest->id,
            ]);
            return null;
        }

        try {
            $branchName = 'feature-request/' . $this->featureRequest->id . '-' . \Str::slug($this->featureRequest->title);

            // For now, we'll create an issue instead of a full PR
            // A full PR would require more complex git operations
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$githubToken}",
                'Accept' => 'application/vnd.github.v3+json',
            ])->post("https://api.github.com/repos/{$repoOwner}/{$repoName}/issues", [
                'title' => "[{$this->featureRequest->type}] {$this->featureRequest->title}",
                'body' => $this->buildIssueBody($aiResponse),
                'labels' => [$this->featureRequest->type, 'ai-analyzed'],
            ]);

            if ($response->successful()) {
                return $response->json('html_url');
            }

            Log::error('Failed to create GitHub issue', [
                'feature_request_id' => $this->featureRequest->id,
                'response' => $response->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception creating GitHub issue', [
                'feature_request_id' => $this->featureRequest->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    protected function buildIssueBody(string $aiResponse): string
    {
        return <<<BODY
## Original Request

**Submitted by:** {$this->featureRequest->user->name} ({$this->featureRequest->user->email})
**Priority:** {$this->featureRequest->priority}
**Type:** {$this->featureRequest->type}

### Description
{$this->featureRequest->description}

---

## AI Analysis

{$aiResponse}

---

*This issue was automatically created from a feature request submitted through the feedback system.*
BODY;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessFeatureRequest job failed permanently', [
            'feature_request_id' => $this->featureRequest->id,
            'error' => $exception->getMessage(),
        ]);

        $this->featureRequest->markAsFailed('Job failed after maximum retries: ' . $exception->getMessage());
    }
}
