<?php

namespace App\Factories;

use App\Contracts\FactoryContract;
use App\Contracts\TranslatorContract;
use App\Services\Translators\DeeplTranslator;
use App\Services\Translators\FakeTranslator;
use App\Services\Translators\GoogleTranslateTranslator;
use Illuminate\Testing\Exceptions\InvalidArgumentException;

class TranslatorFactory implements FactoryContract
{
    public function make(string $model): TranslatorContract
    {
        return match ($model) {
            'deepl' => new DeeplTranslator(),
            'google-translate' => new GoogleTranslateTranslator(),
            'testing' => new FakeTranslator(),
            default => throw new InvalidArgumentException('Invalid translator'),
        };
    }

    public function available(): array
    {
        return [
            [
                'id' => 'deepl',
                'name' => 'DeepL',
            ],
            [
                'id' => 'google-translate',
                'name' => 'Google Translate',
            ],
            [
                'id' => 'testing',
                'name' => 'Testing',
            ],
        ];
    }
}
