<?php

namespace App\DTO;

class TranslatorResponseDTO extends BaseDTO
{
    public function __construct(
        public string $result,
    ) {
    }
}
