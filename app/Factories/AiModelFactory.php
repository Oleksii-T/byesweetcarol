<?php

namespace App\Factories;

use App\Contracts\AiModelContract;
use App\Contracts\FactoryContract;
use App\Services\AIModels\AnthropicModel;
use App\Services\AIModels\FakerAiModel;
use App\Services\AIModels\JusperAiModel;
use App\Services\AIModels\OpenAiModel;
use Illuminate\Testing\Exceptions\InvalidArgumentException;

class AiModelFactory implements FactoryContract
{
    public function make(string $model): AiModelContract
    {
        return match ($model) {
            'gpt-4o-mini' => new OpenAiModel($model),
            'gpt-4o' => new OpenAiModel($model),
            'claude-3-7-sonnet-20250219' => new AnthropicModel($model),
            'jusper-ai' => new JusperAiModel(),
            'testing' => new FakerAiModel(),
            default => throw new InvalidArgumentException('Invalid model'),
        };
    }

    public function available(): array
    {
        return [
            [
                'id' => 'gpt-4o-mini',
                'name' => 'GPT-4o mini',
            ],
            [
                'id' => 'gpt-4o',
                'name' => 'GPT-4o',
            ],
            [
                'id' => 'claude-3-7-sonnet-20250219',
                'name' => 'Claude 3.7 Sonnet',
            ],
            [
                'id' => 'jusper-ai',
                'name' => 'Jusper AI',
            ],
            [
                'id' => 'testing',
                'name' => 'Testing',
            ],
        ];
    }
}
