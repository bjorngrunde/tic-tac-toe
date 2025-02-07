<?php

namespace App\Core\Rules;

interface RuleInterface
{
  public function validate(array $formData, string $field, array $params): bool;

  public function getMessage(array $formData, string $field, array $params): string;
}
