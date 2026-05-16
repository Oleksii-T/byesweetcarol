<?php

namespace App\DTO;

class AIResponseDTO extends BaseDTO
{
    public function __construct(
        public string $result,
        public string $cost,
        public ?array $data,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['result'], $data['cost'], $data['data']);
    }
}
