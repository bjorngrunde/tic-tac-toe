<?php

namespace App\Core\Rules;

use App\Core\Rules\RuleInterface;

class MinRule extends RuleInterface
{
  public function validate(array $formData, string $field, array $params): bool
  {
    if (empty($params[0])) {
      return false;
    }

    $length = (int) $params[0];

    return $formData[$field] >= $length;
  }

  public function getMessage(array $formData, string $field, array $params): string
  {
    return "Must be at least {$params[0]}";
  }
}
