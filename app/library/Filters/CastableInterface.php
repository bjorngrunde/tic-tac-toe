<?php

namespace App\Filters;

interface CastableInterface
{
  public function cast(array $formData): void;
  public function getFields(): array;
  public function getField(string $field): mixed;
}
