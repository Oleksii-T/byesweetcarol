<?php

namespace App\Services\Translators;

use App\Contracts\TranslatorContract;
use App\DTO\TranslatorResponseDTO;

class FakeTranslator implements TranslatorContract
{
    public function __construct()
    {
        //
    }

    public function make(string $text, string $language): TranslatorResponseDTO
    {
        // sleep(1);

        return TranslatorResponseDTO::fromArray([
            'result' => "[$language] $text",
        ]);
    }
}
