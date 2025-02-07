<?php

namespace App\Rules;

use App\Rules\RuleInterface;

class UsernameRule implements RuleInterface
{
  public function validate(array $formData, string $field, array $params): bool
  {
    if (empty($params[0])) {
      return false;
    }

    return preg_match('/^[a-zA-ZäöåÄÖÅ0-9_]+$/', $params[0]) === 1 ? true : false;
  }

  public function getMessage(array $formData, string $field, array $params): string
  {
    return "only letters, numbers, and underscores allowed";
  }
}
