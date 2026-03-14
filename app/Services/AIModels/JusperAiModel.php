<?php

namespace App\Services\AIModels;

use App\Contracts\AiModelContract;
use App\DTO\AIResponseDTO;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JusperAiModel implements AiModelContract
{
    private string $secret;

    public function __construct()
    {
        $this->secret = config('services.jusperai.secret');
    }

    public function generate(string $prompt): AIResponseDTO
    {
        $url = 'https://api.jasper.ai/v1/command';
        $data = [
            'inputs' => [
                'command' => '',
                'context' => $prompt,
            ],
            'options' => [
                'outputCount' => 1,
                // 'inputLanguage' => 'English',
                // 'outputLanguage' => 'English',
                'languageFormality' => 'default',
                'completionType' => 'performance',
            ],
        ];

        $response = Http::withHeader('X-API-Key', $this->secret)->post($url, $data)->json();
        $errorData = $response['errors'] ?? null;

        if ($errorData) {
            \Log::warning('[AI ERROR]', [
                'model' => 'jusper-ai',
                'prompt' => $prompt,
                'error' => $errorData,
            ]);

            throw new HttpException(400, implode(', ', $errorData));
        }

        return AIResponseDTO::fromArray([
            'result' => $response['data'][0]['text'],
            'cost' => 0,
            'data' => $response,
        ]);
    }
}
