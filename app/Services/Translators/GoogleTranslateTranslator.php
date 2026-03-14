<?php

namespace App\Services\Translators;

use App\Contracts\TranslatorContract;
use App\DTO\TranslatorResponseDTO;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GoogleTranslateTranslator implements TranslatorContract
{
    public function __construct()
    {
        //
    }

    public function make(string $text, string $language): TranslatorResponseDTO
    {
        throw new HttpException(400, 'Translator not connected.');
    }
}
