<?php

namespace App\Rules;

use App\Rules\RuleInterface;

class MaxRule implements RuleInterface
{
  public function validate(array $formData, string $field, array $params): bool
  {
    if (empty($params[0])) {
      return false;
    }

    $length = (int) $params[0];

    return $formData[$field] <= $length;
  }

  public function getMessage(array $formData, string $field, array $params): string
  {
    return "Must be at most {$params[0]}";
  }
}
