<?php

namespace App\Dto;

/**
 * @template T of BodyContentDto
 */
interface BodyContentArrayDto extends BodyContentDto
{
    /**
     * @return class-string<T>
     */
    public function getClassname(): string;

    /**
     * @return array<int, T>
     */
    public function getList(): array;

    /**
     * @param array<int, T> $data
     */
    public function setList(array $data): void;
}
