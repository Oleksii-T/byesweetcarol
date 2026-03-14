<?php

namespace App\DTO;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseDTO
{
    public static function fromArray(array $data): self
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties();
        $constructorData = [];

        foreach ($properties as $property) {
            $constructorData[$property->name] = $data[$property->name];
        }

        return $reflectionClass->newInstanceArgs($constructorData);
    }

    public function toArray(): array
    {
        $properties = get_object_vars($this);
        $array = [];

        foreach ($properties as $property => $value) {
            $array[$property] = $this->$property;
        }

        return $array;
    }

    /** @return array<static> */
    public static function fromCollection(EloquentCollection|Collection $collection): array
    {
        return $collection->map(fn ($item) => static::fromArray($item->toArray()))->toArray();
    }

    /** @return array<static> */
    public static function fromArrays(array $array): array
    {
        return array_map(fn ($item) => static::fromArray($item), $array);
    }

    /** @return array{data: array<static>, links: array, meta: array} */
    public static function fromPages(LengthAwarePaginator $collection): array
    {
        $array = $collection->toArray();

        $data = $array['data'];
        $links = $array['links'];
        unset($array['data']);
        unset($array['links']);

        return [
            'data' => array_map(fn ($item) => static::fromArray($item), $data),
            'links' => $links,
            'meta' => $array,
        ];
    }
}
