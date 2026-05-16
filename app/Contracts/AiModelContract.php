<?php

namespace App\Contracts;

use App\DTO\AIResponseDTO;

interface AiModelContract
{
    public function generate(string $prompt): AIResponseDTO;
}
