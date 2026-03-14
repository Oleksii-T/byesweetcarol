<?php

namespace App\Services\AIModels;

use App\Contracts\AiModelContract;
use App\DTO\AIResponseDTO;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnthropicModel implements AiModelContract
{
    private string $model;

    private string $secret;

    public function __construct(string $model)
    {
        $this->model = $model;
        $this->secret = config('services.anthropic.secret');
    }

    public function generate(string $prompt): AIResponseDTO
    {
        $url = 'https://api.anthropic.com/v1/messages';
        $data = [
            'model' => $this->model,
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        $response = Http::withHeader('x-api-key', $this->secret)->withHeader('anthropic-version', '2023-06-01')->post($url, $data)->json();
        $errorData = $response['error'] ?? null;

        if ($errorData) {
            \Log::warning('[AI ERROR]', [
                'model' => $this->model,
                'prompt' => $prompt,
                'error' => $errorData,
            ]);

            throw new HttpException(400, $errorData['message']);
        }

        return AIResponseDTO::fromArray([
            'result' => $response['content'][0]['text'],
            'cost' => 0,
            'data' => $response,
        ]);
    }
}
