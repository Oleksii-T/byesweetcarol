<?php

namespace App\Services\AIModels;

use App\Contracts\AiModelContract;
use App\DTO\AIResponseDTO;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OpenAiModel implements AiModelContract
{
    private string $model;

    private string $secret;

    public function __construct(string $model)
    {
        $this->model = $model;
        $this->secret = config('services.openai.secret');
    }

    public function generate(string $prompt): AIResponseDTO
    {
        $url = 'https://api.openai.com/v1/responses';
        $data = [
            'model' => $this->model,
            'input' => $prompt,
        ];

        $response = Http::withToken($this->secret)->post($url, $data)->json();
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
            'result' => $response['output'][0]['content'][0]['text'],
            'cost' => 0,
            'data' => $response,
        ]);
    }
}
