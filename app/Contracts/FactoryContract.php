<?php

namespace App\Contracts;

interface FactoryContract
{
    public function make(string $type);

    public function available(): array;
}
