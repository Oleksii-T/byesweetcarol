<?php

namespace App\Contracts;

use App\DTO\TranslatorResponseDTO;

interface TranslatorContract
{
    public function make(string $text, string $language): TranslatorResponseDTO;
}
